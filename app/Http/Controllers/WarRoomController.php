<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Declaration, CustomsOffice, Province};
use Illuminate\Support\Facades\DB;

class WarRoomController extends Controller
{
    public function index()
    {
        // 1. KPI de Performance Financière [cite: 10]
        $stats = [
            'total_cif' => Declaration::sum('montant_cif'),
            'total_tps' => Declaration::sum('taxe_due'),
            'taux_validation' => (Declaration::where('statut', 'valide')->count() / max(Declaration::count(), 1)) * 100,
            'alertes_critiques' => Declaration::whereIn('statut', ['suspect', 'fraude_suspectée', 'litige'])->count(),
        ];

        // 2. Performance par Province pour le graphique [cite: 46]
        $provincePerformance = Province::with(['offices.declarations'])
            ->get()
            ->map(fn($province) => [
                'name' => $province->name,
                'total' => $province->offices->flatMap->declarations->sum('taxe_due'),
                'count' => $province->offices->flatMap->declarations->count(),
            ]);

        // 3. Flux des dernières preuves certifiées (Note de Perception/RRJ) [cite: 24, 26]
        $latestAudits = Declaration::with(['office', 'agent'])
            ->whereNotNull('agent_id')
            ->latest()
            ->take(6)
            ->get();

        // 4. Dossiers à haut risque (Priority Score > 8) 
        $highRiskFiles = Declaration::where('priority_score', '>', 7)
            ->where('statut', '!=', 'valide')
            ->with('office')
            ->orderByDesc('priority_score')
            ->take(5)
            ->get();

        return view('admin.war-room-v2', compact('stats', 'provincePerformance', 'latestAudits', 'highRiskFiles'));
    }
}