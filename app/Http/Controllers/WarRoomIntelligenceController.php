<?php

namespace App\Http\Controllers;

use App\Models\{Declaration, CustomsOffice, Province, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarRoomIntelligenceController extends Controller
{
    public function index(Request $request)
    {
        // Initialisation de la requête de base pour les filtres
        $query = Declaration::query();

        // --- SYSTÈME DE FILTRES DYNAMIQUES ---
        if ($request->filled('date_range')) {
            $range = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [Carbon::parse($range[0]), Carbon::parse($range[1] ?? now())]);
        }

        if ($request->filled('province_id')) {
            $query->whereHas('office', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('office_id')) {
            $query->where('customs_office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->filled('min_risk')) {
            $query->where('priority_score', '>=', $request->min_risk);
        }

        // --- CALCUL DES KPIS FILTRÉS ---
        $stats = [
            'total_cif' => (clone $query)->sum('montant_cif'),
            'total_sydonia' => (clone $query)->sum('taxe_due'),
            'total_valide' => (clone $query)->where('statut', 'valide')->sum('taxe_due'),
            'dossiers_count' => (clone $query)->count(),
            'alertes' => (clone $query)->whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect'])->count(),
        ];
        $stats['ecart'] = $stats['total_sydonia'] - $stats['total_valide'];
        $stats['performance'] = $stats['total_sydonia'] > 0 ? ($stats['total_valide'] / $stats['total_sydonia']) * 100 : 0;

        // --- DONNÉES GÉOGRAPHIQUES (Toujours globales pour la vision d'ensemble) ---
        $offices = CustomsOffice::withCount(['declarations as alertes' => function($q) {
            $q->whereIn('statut', ['alerte', 'suspect']);
        }])->get();

        // --- ANALYSE PAR IMPORTATEUR (Nouveau détail) ---
        $topImportateurs = (clone $query)
            ->select('importateur', DB::raw('SUM(taxe_due) as total_du'), DB::raw('COUNT(*) as nbr_dossiers'))
            ->groupBy('importateur')
            ->orderByDesc('total_du')
            ->take(5)
            ->get();

        // --- PERFORMANCE AGENTS (Nouveau détail) ---
        $agentPerformance = User::role('agent')
            ->withCount(['declarations as dossiers_traites' => function($q) use ($request) {
                if($request->filled('office_id')) $q->where('customs_office_id', $request->office_id);
            }])
            ->orderByDesc('dossiers_traites')
            ->take(5)
            ->get();

        $provinces = Province::all();
        $allOffices = CustomsOffice::all();

        return view('admin.war-room-intelligence', compact(
            'stats', 'offices', 'topImportateurs', 'agentPerformance', 
            'provinces', 'allOffices', 'request'
        ));
    }
}