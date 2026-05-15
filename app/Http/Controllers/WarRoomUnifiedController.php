<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Declaration, CustomsOffice, Province};
use Illuminate\Support\Facades\DB;

class WarRoomUnifiedController extends Controller
{
    public function index()
    {
        // 1. KPIs de Performance et Écart (Financier + Opérationnel)
        $totalSydonia = Declaration::sum('taxe_due');
        $totalValide = Declaration::where('statut', 'valide')->sum('taxe_due');
        
        $stats = [
            'total_cif' => Declaration::sum('montant_cif'),
            'total_tps' => $totalSydonia,
            'total_valide' => $totalValide,
            'ecart' => $totalSydonia - $totalValide,
            'taux_validation' => (Declaration::count() > 0) 
                ? (Declaration::where('statut', 'valide')->count() / Declaration::count()) * 100 
                : 0,
            'alertes_critiques' => Declaration::whereIn('statut', ['suspect', 'fraude_suspectée', 'litige', 'alerte'])->count(),
            'taux_couverture' => (Declaration::count() > 0) 
                ? (Declaration::whereNotNull('agent_id')->count() / Declaration::count()) * 100 
                : 0,
        ];

        // 2. Données Cartographiques (Bureaux avec scores)
        $offices = CustomsOffice::withCount(['declarations as alertes' => function($query) {
            $query->whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect']);
        }])->get()->map(fn($office) => [
            'name' => $office->name,
            'lat' => $office->latitude,
            'lng' => $office->longitude,
            'alertes' => $office->alertes,
            'color' => $office->alertes > 5 ? '#ef4444' : '#f59e0b'
        ]);

        // 3. Performance Provinciale
        $provincePerformance = Province::with(['offices.declarations'])
            ->get()
            ->map(fn($province) => [
                'name' => $province->name,
                'total' => $province->offices->flatMap->declarations->sum('taxe_due'),
            ]);

        // 4. Dossiers à Haut Risque (Ciblage)
        $highRiskFiles = Declaration::where('priority_score', '>', 7)
            ->where('statut', '!=', 'valide')
            ->with('office')
            ->orderByDesc('priority_score')
            ->take(5)
            ->get();

        // 5. Flux d'Audit Récent (Timeline)
        $latestAudits = Declaration::with(['office', 'agent'])
            ->whereNotNull('agent_id')
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('admin.war-room-unified', compact('stats', 'offices', 'provincePerformance', 'highRiskFiles', 'latestAudits'));
    }
}