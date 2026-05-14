<?php

use Livewire\Volt\Component;
use App\Models\Declaration;
use App\Models\CustomsOffice;

new class extends Component {
    public function with(): array
    {
        $totalCount = Declaration::count();
        $conformeCount = Declaration::where('statut', 'conforme')->count();
        $alerteCount = Declaration::where('statut', 'alerte')->count();

        $potentiel = Declaration::sum('taxe_due');
        $reel = Declaration::where('statut', 'conforme')->sum('taxe_due');
        $gap = Declaration::where('statut', 'alerte')->sum('taxe_due');
        $taux = $totalCount > 0 ? ($conformeCount / $totalCount) * 100 : 0;

        // Calcul des tendances (vs 7 jours avant)
        $potentielPrev = Declaration::where('created_at', '<', now()->subDays(7))->sum('taxe_due');
        $realPrev = Declaration::where('statut', 'conforme')
            ->where('created_at', '<', now()->subDays(7))
            ->sum('taxe_due');

        $trendsGrowth = $potentielPrev > 0 ? (($potentiel - $potentielPrev) / $potentielPrev) * 100 : 0;
        $trendsRecovery = $realPrev > 0 ? (($reel - $realPrev) / $realPrev) * 100 : 0;

        // Top 10 des importateurs à haut risque
        $topRiskyImporters = Declaration::with('office')->select('importateur', 'numero_dcl', 'taxe_due', 'statut', 'priority_score')->where('statut', 'alerte')->orderByDesc('priority_score')->limit(10)->get();

        // Live Feed: Alertes récentes avec détails riches
        $recentAlerts = Declaration::with('office')->where('statut', 'alerte')->orderByDesc('created_at')->take(8)->get();

        // Heatmap data: Agrégation par bureau douanier
        $heatmapData = CustomsOffice::with([
            'declarations' => function ($q) {
                $q->where('statut', 'alerte');
            },
        ])
            ->get()
            ->map(function ($office) {
                return [
                    'id' => $office->id,
                    'name' => $office->name,
                    'lat' => (float) ($office->latitude ?? -4.038),
                    'lng' => (float) ($office->longitude ?? 21.758),
                    'alerts' => $office->declarations->count(),
                    'risk_score' => $office->declarations->avg('priority_score') ?? 0,
                ];
            });

        return [
            'stats' => [
                'potentiel' => $potentiel,
                'reel' => $reel,
                'gap' => $gap,
                'gps_alerts' => Declaration::where('gps_validated', false)->count(),
                'taux' => $taux,
                'totalDeclarations' => $totalCount,
                'conformeCount' => $conformeCount,
                'alerteCount' => $alerteCount,
            ],
            'trends' => [
                'growth' => $trendsGrowth,
                'recovery' => $trendsRecovery,
            ],
            'topRiskyImporters' => $topRiskyImporters,
            'recentAlerts' => $recentAlerts,
            'heatmapData' => $heatmapData,
            'networkStatus' => 'OPÉRATIONNEL',
            'lastSync' => now(),
        ];
    }
}; ?>



<div class="min-h-screen bg-gradient-to-br from-zinc-950 via-blue-950/20 to-zinc-950">
    <!-- Header Stratégique -->
    <div class="sticky top-0 z-50 border-b border-zinc-800/50 backdrop-blur-xl bg-zinc-950/80">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span
                                class="text-xs font-mono tracking-[0.3em] text-green-400 font-bold">{{ $networkStatus }}</span>
                        </div>
                    </div>
                    <h1 class="text-5xl font-black text-white tracking-tighter">
                        SITUATION GÉNÉRALE
                        <span
                            class="block text-3xl mt-1 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                            TAXTPS v6
                        </span>
                    </h1>
                </div>

                <div class="flex flex-col items-end gap-4">
                    <div class="px-6 py-3 rounded-xl bg-zinc-900/50 border border-zinc-800 backdrop-blur-sm">
                        <p class="text-xs text-zinc-500 font-mono tracking-wide mb-1 uppercase">Dernière
                            synchronisation</p>
                        <p class="text-lg font-mono text-blue-400 font-bold">
                            {{ $lastSync->format('H:i:s') }}
                        </p>
                    </div>
                    <flux:badge color="lime">
                        <span class="font-mono text-xs">{{ $stats['totalDeclarations'] }}
                            déclarations</span>
                    </flux:badge>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Section -->
    <div class="px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Potentiel SYDONIA -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900/80 to-blue-900/30 border border-blue-500/20 p-6 hover:border-blue-500/50 transition-all duration-300">
                <div
                    class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl group-hover:blur-2xl transition-all duration-300">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-blue-400/70 uppercase tracking-widest">Potentiel
                            SYDONIA</span>
                        <svg class="w-6 h-6 text-blue-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-black text-white mb-3">
                        ${{ number_format($stats['potentiel'], 0, ',', ' ') }}</h3>
                    <div class="flex items-center gap-2 text-sm font-mono">
                        <span class="text-green-400 font-bold">↑ {{ round($trends['growth'], 1) }}%</span>
                        <span class="text-zinc-600">vs 7j</span>
                    </div>
                    <p class="text-xs text-blue-400/60 mt-3 font-mono">PRÉVISIONS AUTOMATISÉES</p>
                </div>
            </div>

            <!-- Recouvrement Réel -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900/80 to-green-900/30 border border-green-500/20 p-6 hover:border-green-500/50 transition-all duration-300">
                <div
                    class="absolute -top-40 -right-40 w-80 h-80 bg-green-500/10 rounded-full blur-3xl group-hover:blur-2xl transition-all duration-300">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-green-400/70 uppercase tracking-widest">Recouvrement
                            Réel</span>
                        <svg class="w-6 h-6 text-green-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-black text-green-400 mb-3">
                        ${{ number_format($stats['reel'], 0, ',', ' ') }}</h3>
                    <div class="flex items-center gap-2 text-sm font-mono">
                        <span class="text-green-400 font-bold">↑ {{ round($trends['recovery'], 1) }}%</span>
                        <span class="text-zinc-600">vs 7j</span>
                    </div>
                    <p class="text-xs text-green-400/60 mt-3 font-mono">RECETTES SÉCURISÉES</p>
                </div>
            </div>

            <!-- Taux de Couverture -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900/80 to-purple-900/30 border border-purple-500/20 p-6 hover:border-purple-500/50 transition-all duration-300">
                <div
                    class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl group-hover:blur-2xl transition-all duration-300">
                </div>
                <div class="relative">
                    <span class="text-xs font-bold text-purple-400/70 uppercase tracking-widest">Taux de
                        Couverture</span>
                    <div class="mt-4 mb-4">
                        <h3 class="text-4xl font-black text-purple-300">{{ round($stats['taux'], 1) }}%</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="h-3 w-full bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500"
                                style="width: {{ $stats['taux'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-zinc-400">
                            <span>{{ $stats['conformeCount'] }} conformes</span>
                            <span>{{ $stats['alerteCount'] }} alertes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Écart / Gap -->
            <div
                class="group relative overflow-hidden rounded-2xl {{ $stats['gap'] > $stats['potentiel'] * 0.15 ? 'bg-gradient-to-br from-red-950/50 to-red-900/20 border border-red-500/30' : 'bg-gradient-to-br from-zinc-900/80 to-orange-900/30 border border-orange-500/20' }} p-6 transition-all duration-300">
                <div
                    class="absolute -top-40 -right-40 w-80 h-80 {{ $stats['gap'] > $stats['potentiel'] * 0.15 ? 'bg-red-500/10' : 'bg-orange-500/10' }} rounded-full blur-3xl group-hover:blur-2xl transition-all duration-300">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="text-xs font-bold {{ $stats['gap'] > $stats['potentiel'] * 0.15 ? 'text-red-400/70' : 'text-orange-400/70' }} uppercase tracking-widest">
                            Écart (Fraude)
                        </span>

                        @if($stats['gap'] > $stats['potentiel'] * 0.15)
                        <flux:badge color="red" size="sm">ALERTE</flux:badge>
                        @else
                        <flux:badge color="amber" size="sm">VIGILANCE</flux:badge>
                        @endif
                    </div>
                    <h3
                        class="text-4xl font-black {{ $stats['gap'] > $stats['potentiel'] * 0.15 ? 'text-red-400' : 'text-orange-400' }} mb-3">
                        ${{ number_format($stats['gap'], 0, ',', ' ') }}</h3>
                    <p
                        class="text-xs {{ $stats['gap'] > $stats['potentiel'] * 0.15 ? 'text-red-400/60' : 'text-orange-400/60' }} font-mono">
                        {{ round(($stats['gap'] / max($stats['potentiel'], 1)) * 100, 1) }}% du potentiel
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Grid: Heatmap + Live Feed -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Heatmap Section -->
            <div class="lg:col-span-2">
                <flux:card class="h-full border-blue-500/20 bg-gradient-to-br from-zinc-900/50 to-blue-900/20">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-white flex items-center gap-3">
                            <span class="text-2xl">🗺️</span>
                            Carte de Chaleur - Zones de Risque
                        </h2>
                        <flux:badge color="amber">{{ $heatmapData->where('alerts', '>', 0)->count() }}
                            zones actives</flux:badge>
                    </div>

                    <!-- Leaflet Container -->
                    <div id="heatmap-container"
                        class="w-full h-[500px] rounded-xl bg-zinc-900 border border-zinc-800 overflow-hidden relative">
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-blue-500/50 animate-pulse mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 20l-5.447-2.724A1 1 0 003 16.382V5.618a1 1 0 011.553-.894L9 7m0 0l6-4m-6 4v12m0-12l6-4m6 4v12m0-12l5.447-2.724A1 1 0 0021 5.618v10.764a1 1 0 01-1.553.894L15 17">
                                </path>
                            </svg>
                            <p class="text-zinc-400 font-mono text-sm">Leaflet.js Heatmap</p>
                            <p class="text-zinc-600 text-xs mt-2">{{ $heatmapData->sum('alerts') }}
                                alertes mappées</p>
                        </div>
                    </div>

                    <!-- Heatmap Legend -->
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-zinc-800">
                        @foreach ($heatmapData->sortByDesc('alerts')->take(3) as $zone)
                        <div class="p-4 rounded-lg bg-zinc-800/50 border border-zinc-700">
                            <p class="text-xs text-zinc-500 font-mono mb-2">{{ $zone['name'] }}</p>
                            <p class="text-2xl font-black text-orange-400">{{ $zone['alerts'] }}</p>
                            <p class="text-xs text-zinc-600 mt-1">alertes</p>
                        </div>
                        @endforeach
                    </div>
                </flux:card>
            </div>

            <!-- Live Feed Section -->
            <div>
                <flux:card class="h-full border-green-500/20 bg-gradient-to-br from-zinc-900/50 to-green-900/20">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-black text-white flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Flux Direct
                        </h3>
                        <flux:badge color="lime">Live</flux:badge>
                    </div>

                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                        @forelse ($recentAlerts as $alert)
                        <div
                            class="group p-4 rounded-xl bg-zinc-800/50 border border-zinc-700 hover:border-green-500/50 hover:bg-zinc-800/80 transition-all duration-200 cursor-pointer">
                            <div class="flex justify-between items-start gap-2 mb-2">
                                <span class="text-xs font-mono text-green-400 font-bold">{{ $alert->numero_dcl }}</span>
                                <span class="text-[10px] text-zinc-600">{{ $alert->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-bold text-white truncate">{{ $alert->importateur }}
                            </p>
                            <div class="flex items-center justify-between mt-3">
                                <span
                                    class="text-xs text-zinc-500">{{ $alert->office->name ?? 'Bureau inconnu' }}</span>
                                <flux:badge color="red" size="sm">
                                    <span class="text-[10px]">{{ $alert->statut }}</span>
                                </flux:badge>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <p class="text-zinc-600 text-sm font-mono">Aucune alerte récente</p>
                        </div>
                        @endforelse
                    </div>
                </flux:card>
            </div>
        </div>

        <!-- Top 10 Importateurs à Haut Risque -->
        <div class="mb-8">
            <flux:card class="border-red-500/20 bg-gradient-to-br from-zinc-900/50 to-red-900/20">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-white flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        Top 10 - Importateurs Haut Risque
                    </h2>
                    <flux:badge color="red">
                        <span class="font-mono text-sm">{{ $topRiskyImporters->count() }} entités</span>
                    </flux:badge>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-zinc-800 bg-zinc-900/50">
                            <tr class="text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Rang</th>
                                <th class="px-6 py-4">Numéro Déclaration</th>
                                <th class="px-6 py-4">Importateur</th>
                                <th class="px-6 py-4">Bureau Douanier</th>
                                <th class="px-6 py-4">Montant TPS</th>
                                <th class="px-6 py-4">Score de Risque</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($topRiskyImporters as $index => $importer)
                            <tr class="hover:bg-zinc-800/30 transition-colors duration-200 group">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-black bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-mono text-green-400 font-bold">{{ $importer->numero_dcl }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-white group-hover:text-red-300 transition-colors">{{ $importer->importateur }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-zinc-400">{{ $importer->office->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-mono font-bold text-red-400">${{ number_format($importer->taxe_due, 0, ',', ' ') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-20 bg-zinc-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-orange-500 to-red-500 rounded-full"
                                                style="width: {{ min(($importer->priority_score / 100) * 100, 100) }}%">
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-red-400">{{ round($importer->priority_score, 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-zinc-600 font-mono">
                                    Aucun risque détecté
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </flux:card>
        </div>

        <!-- Footer Intelligence -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-8">
            <flux:card class="border-blue-500/20 bg-gradient-to-br from-zinc-900/50 to-blue-900/20">
                <div class="text-center">
                    <p class="text-4xl font-black text-blue-400 mb-2">{{ $stats['totalDeclarations'] }}
                    </p>
                    <p class="text-xs text-zinc-400 uppercase tracking-wider font-mono">Déclarations
                        traitées</p>
                </div>
            </flux:card>

            <flux:card class="border-green-500/20 bg-gradient-to-br from-zinc-900/50 to-green-900/20">
                <div class="text-center">
                    <p class="text-4xl font-black text-green-400 mb-2">
                        {{ round(($stats['conformeCount'] / max($stats['totalDeclarations'], 1)) * 100, 1) }}%
                    </p>
                    <p class="text-xs text-zinc-400 uppercase tracking-wider font-mono">Taux de conformité
                    </p>
                </div>
            </flux:card>

            <flux:card class="border-amber-500/20 bg-gradient-to-br from-zinc-900/50 to-amber-900/20">
                <div class="text-center">
                    <p class="text-4xl font-black text-amber-400 mb-2">{{ $stats['gps_alerts'] }}</p>
                    <p class="text-xs text-zinc-400 uppercase tracking-wider font-mono">Anomalies GPS</p>
                </div>
            </flux:card>
        </div>
    </div>
</div>