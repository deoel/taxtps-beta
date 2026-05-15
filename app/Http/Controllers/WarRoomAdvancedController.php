<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Declaration, CustomsOffice, Province, User};
use Illuminate\Support\Facades\DB;

class WarRoomAdvancedController extends Controller
{
    public function index()
    {
        // 1. KPIs FINANCIERS COMPLETS (Fusion V1 & V2)
        $stats = [
            'total_cif' => Declaration::sum('montant_cif'),
            'total_sydonia' => Declaration::sum('taxe_due'), // Potentiel
            'total_valide' => Declaration::where('statut', 'valide')->sum('taxe_due'), // Réel
            'dossiers_suspects' => Declaration::whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect', 'litige'])->count(),
            'taux_validation' => (Declaration::count() > 0) ? (Declaration::where('statut', 'valide')->count() / Declaration::count()) * 100 : 0,
            'taux_couverture' => (Declaration::count() > 0) ? (Declaration::whereNotNull('agent_id')->count() / Declaration::count()) * 100 : 0,
        ];
        $stats['ecart'] = $stats['total_sydonia'] - $stats['total_valide'];

        // 2. ANALYSE GÉOGRAPHIQUE AVEC SCORES DE RISQUE (Pour Leaflet)
        $offices = CustomsOffice::withCount(['declarations as alertes' => function($query) {
            $query->whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect']);
        }])->get()->map(fn($office) => [
            'id' => $office->id,
            'name' => $office->name,
            'code' => $office->code_bureau,
            'lat' => $office->latitude,
            'lng' => $office->longitude,
            'alertes' => $office->alertes,
            'total_collecte' => $office->declarations()->where('statut', 'valide')->sum('taxe_due'),
            'color' => $office->alertes > 10 ? '#ef4444' : ($office->alertes > 5 ? '#f59e0b' : '#3b82f6')
        ]);

        // 3. PERFORMANCE PROVINCIALE (Données Graphiques)
        $provincePerformance = Province::with(['offices.declarations'])
            ->get()
            ->map(fn($province) => [
                'name' => $province->name,
                'total' => $province->offices->flatMap->declarations->sum('taxe_due'),
                'count' => $province->offices->flatMap->declarations->count(),
                'valide' => $province->offices->flatMap->declarations->where('statut', 'valide')->count(),
            ]);

        // 4. CIBLAGE HAUTE PRIORITÉ (Algorithme de risque)
        $highRiskFiles = Declaration::where('priority_score', '>', 7)
            ->where('statut', '!=', 'valide')
            ->with(['office', 'province'])
            ->orderByDesc('priority_score')
            ->take(10)
            ->get();

        // 5. AUDIT TRAIL TEMPS RÉEL (Flux d'activité détaillé)
        $latestAudits = Declaration::with(['office', 'agent'])
            ->whereNotNull('agent_id')
            ->latest('updated_at')
            ->take(15)
            ->get();

        return view('admin.war-room-advanced', compact(
            'stats', 
            'offices', 
            'provincePerformance', 
            'highRiskFiles', 
            'latestAudits'
        ));
    }
}