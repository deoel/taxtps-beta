<?php

namespace App\Http\Controllers;

use App\Models\Declaration;
use App\Models\CustomsOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarRoom2Controller extends Controller
{
    public function index()
    {
        // KPIs Globaux
        $stats = [
            'total_sydonia' => Declaration::sum('taxe_due'),
            'total_valide' => Declaration::where('statut', 'valide')->sum('taxe_due'),
            'dossiers_suspects' => Declaration::whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect'])->count(),
            'taux_couverture' => Declaration::count() > 0 
                ? (Declaration::whereNotNull('agent_id')->count() / Declaration::count()) * 100 
                : 0,
        ];

        $stats['ecart'] = $stats['total_sydonia'] - $stats['total_valide'];

        // Données pour la carte (Bureaux avec score de risque)
        $offices = CustomsOffice::withCount(['declarations as alertes' => function($query) {
            $query->whereIn('statut', ['alerte', 'fraude_suspectée']);
        }])->get()->map(function($office) {
            return [
                'name' => $office->name,
                'lat' => $office->latitude,
                'lng' => $office->longitude,
                'alertes' => $office->alertes,
                'color' => $office->alertes > 5 ? '#ef4444' : '#f59e0b'
            ];
        });

        // Flux d'activités récentes
        $recentActions = Declaration::with(['agent', 'office'])
            ->whereNotNull('agent_id')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('war-room.index', compact('stats', 'offices', 'recentActions'));
    }
}