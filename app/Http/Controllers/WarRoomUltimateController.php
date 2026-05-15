<?php

namespace App\Http\Controllers;

use App\Models\{Declaration, CustomsOffice, Province, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarRoomUltimateController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. SYSTÈME DE FILTRES DYNAMIQUES (Hérité de Intelligence) ---
        $query = Declaration::query();

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [Carbon::parse($request->date_from), Carbon::parse($request->date_to)->addDay()]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }

        if ($request->filled('province_id')) {
            $query->whereHas('office', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('office_id')) {
            $query->where('customs_office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        // --- 2. KPIs FINANCIERS FILTRÉS ---
        $stats = [
            'total_cif' => (clone $query)->sum('montant_cif'),
            'total_sydonia' => (clone $query)->sum('taxe_due'),
            'total_valide' => (clone $query)->where('statut', 'valide')->sum('taxe_due'),
            'dossiers_suspects' => (clone $query)->whereIn('statut', ['alerte', 'fraude_suspectée', 'suspect', 'litige'])->count(),
            'taux_validation' => ((clone $query)->count() > 0) ? ((clone $query)->where('statut', 'valide')->count() / (clone $query)->count()) * 100 : 0,
            'taux_couverture' => ((clone $query)->count() > 0) ? ((clone $query)->whereNotNull('agent_id')->count() / (clone $query)->count()) * 100 : 0,
        ];
        $stats['ecart'] = $stats['total_sydonia'] - $stats['total_valide'];
        $stats['performance'] = $stats['total_sydonia'] > 0 ? ($stats['total_valide'] / $stats['total_sydonia']) * 100 : 0;

        // --- 3. ANALYSE GÉOGRAPHIQUE (Hérité de Advanced) ---
        $offices = CustomsOffice::withCount(['declarations as alertes' => function ($q) {
            $q->whereIn('statut', ['alerte', 'suspect']);
        }])->get()->map(function ($office) {
            return [
                'id' => $office->id,
                'name' => $office->name,
                'code_bureau' => $office->code_bureau,
                'latitude' => $office->latitude,
                'longitude' => $office->longitude,
                'alertes' => $office->alertes,
                'total_collecte' => Declaration::where('customs_office_id', $office->id)->where('statut', 'valide')->sum('taxe_due')
            ];
        });

        // --- 4. ANALYSE PAR IMPORTATEUR & PERFORMANCE AGENTS ---
        $topImportateurs = (clone $query)
            ->select('importateur', DB::raw('SUM(taxe_due) as total_du'), DB::raw('COUNT(*) as nbr_dossiers'))
            ->groupBy('importateur')
            ->orderByDesc('total_du')
            ->take(5)->get();

        $agentPerformance = User::role('agent')
            ->withCount(['declarations as dossiers_traites' => function ($q) use ($request) {
                if ($request->filled('office_id')) $q->where('customs_office_id', $request->office_id);
            }])
            ->orderByDesc('dossiers_traites')
            ->take(5)->get();

        // --- 5. HAUT RISQUE & AUDIT TRAIL ---
        $highRiskFiles = (clone $query)->where('priority_score', '>', 7)
            ->where('statut', '!=', 'valide')
            ->with(['office'])
            ->orderByDesc('priority_score')
            ->take(10)->get();

        $latestAudits = Declaration::with(['office', 'agent'])
            ->whereNotNull('agent_id')
            ->latest('updated_at')
            ->take(15)->get();

        // --- 6. HEURE DE LA DERNIÈRE SYNCHRONISATION ---
        $lastSyncTime = Declaration::latest('updated_at')->first()?->updated_at ?? now();

        // --- 7. PERFORMANCE PAR PROVINCE (Filtrée) ---
        $provincePerformance = Province::all()->map(function ($province) use ($request) {
            // On repart d'une nouvelle requête de déclarations pour cette province
            $pQuery = Declaration::whereHas('office', function ($q) use ($province) {
                $q->where('province_id', $province->id);
            });

            // On applique EXACTEMENT les mêmes filtres que la query principale
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $pQuery->whereBetween('created_at', [Carbon::parse($request->date_from), Carbon::parse($request->date_to)->addDay()]);
            } elseif ($request->filled('date_from')) {
                $pQuery->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            } elseif ($request->filled('date_to')) {
                $pQuery->whereDate('created_at', '<=', Carbon::parse($request->date_to));
            }

            if ($request->filled('status')) {
                $pQuery->where('statut', $request->status);
            }

            // Si un bureau spécifique est filtré, et qu'il n'est pas dans cette province, 
            // le total sera naturellement à 0 grâce à la condition ci-dessous
            if ($request->filled('office_id')) {
                $pQuery->where('customs_office_id', $request->office_id);
            }

            return [
                'name'   => $province->name,
                'total'  => (clone $pQuery)->sum('taxe_due'),
                'count'  => (clone $pQuery)->count(),
                'valide' => (clone $pQuery)->where('statut', 'valide')->count(),
            ];
        });

        return view('admin.war-room.ultimate', compact(
            'stats',
            'offices',
            'topImportateurs',
            'agentPerformance',
            'highRiskFiles',
            'latestAudits',
            'provincePerformance',
            'lastSyncTime'
        ))->with([
            'provinces' => Province::all(),
            'allOffices' => CustomsOffice::all()
        ]);
    }
}