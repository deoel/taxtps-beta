<?php

use Livewire\Volt\Component;
use App\Models\Declaration;
use App\Models\CustomsOffice;

new class extends Component {
    public function with(): array
    {
        $totalCount = Declaration::count();
        
        return [
            'stats' => [
                'potentiel' => Declaration::sum('taxe_due'),
                'reel' => Declaration::where('statut', 'conforme')->sum('taxe_due'),
                'gap' => Declaration::where('statut', 'alerte')->sum('taxe_due'),
                'gps_alerts' => Declaration::where('gps_validated', false)->count(),
                'taux' => ($totalCount > 0) ? (Declaration::where('statut', 'conforme')->count() / $totalCount) * 100 : 0,
            ],
            'recentAlerts' => Declaration::with('office')
                ->where('statut', 'alerte')
                ->latest()
                ->take(6)
                ->get(),
        ];
    }
}; ?>

<div class="p-8 bg-[#020617] min-h-screen text-slate-200">
    <header class="flex justify-between items-center mb-10 border-b border-slate-800 pb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                </span>
                <span class="text-xs font-mono tracking-[0.4em] text-blue-500 uppercase font-bold">RDC - Surveillance
                    Souveraine</span>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter">WAR ROOM <span
                    class="text-blue-600">TAX-TPS</span></h1>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-3 rounded-lg flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Mise à jour</p>
                <p class="text-sm font-mono text-blue-400">{{ now()->format('H:i:s') }}</p>
            </div>
            <div class="h-8 w-[1px] bg-slate-800"></div>
            <div class="flex -space-x-2">
                <div
                    class="h-8 w-8 rounded-full border-2 border-slate-900 bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white">
                    AD</div>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Potentiel Sydonia</p>
            <h3 class="text-3xl font-black text-white">${{ number_format($stats['potentiel'], 0, ',', ' ') }}</h3>
            <p class="text-[10px] text-blue-500 mt-2 font-bold tracking-tighter">PRÉVISIONS AUTOMATISÉES</p>
        </div>

        <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Recouvré TPS</p>
            <h3 class="text-3xl font-black text-green-400">${{ number_format($stats['reel'], 0, ',', ' ') }}</h3>
            <p class="text-[10px] text-green-600 mt-2 font-bold">RECETTES SÉCURISÉES</p>
        </div>

        <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-3xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Efficacité</p>
            <h3 class="text-3xl font-black text-indigo-400">{{ round($stats['taux'], 1) }}%</h3>
            <div class="w-full bg-slate-800 h-1 mt-4 rounded-full overflow-hidden">
                <div class="bg-indigo-500 h-full" style="width: {{ $stats['taux'] }}%"></div>
            </div>
        </div>

        <div class="bg-red-950/10 border border-red-900/30 p-6 rounded-3xl relative">
            <p class="text-xs font-bold text-red-500/70 uppercase tracking-widest mb-2">Manque à gagner</p>
            <h3 class="text-3xl font-black text-red-500">${{ number_format($stats['gap'], 0, ',', ' ') }}</h3>
            <p class="text-[10px] text-red-600/60 mt-2 font-mono italic">Fraudes suspectées</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div
                class="bg-slate-900/60 border border-slate-800 rounded-3xl p-8 h-[500px] flex flex-col items-center justify-center text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-5 pointer-events-none">
                    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 0 L100 100 M0 100 L100 0" stroke="currentColor" stroke-width="0.1"></path>
                    </svg>
                </div>
                <div class="p-4 rounded-full bg-blue-500/10 mb-4">
                    <svg class="w-12 h-12 text-blue-500 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-white mb-2 font-mono">INITIALISATION GÉO-RADAR</h4>
                <p class="text-slate-500 text-sm max-w-sm">Prêt pour l'affichage Leaflet.js des
                    {{ App\Models\CustomsOffice::count() }} bureaux et 2000 points de contrôle.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em]">Flux Critiques</h4>
                <span
                    class="text-[10px] bg-red-600 text-white px-2 py-0.5 rounded font-bold uppercase animate-pulse">Live</span>
            </div>

            @foreach($recentAlerts as $alert)
            <div
                class="bg-slate-900/30 border border-slate-800 p-4 rounded-2xl border-l-4 border-l-red-600 group hover:bg-slate-800/40 transition-colors">
                <div class="flex justify-between items-start mb-2 font-mono">
                    <span class="text-xs text-blue-500 font-bold tracking-tighter">{{ $alert->numero_dcl }}</span>
                    <span class="text-[9px] text-slate-600 uppercase">{{ $alert->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm font-bold text-slate-300 truncate">{{ $alert->importateur }}</p>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-[10px] text-slate-500">{{ $alert->office->name }}</span>
                    <span class="text-[10px] font-black text-red-500">HORS ZONE</span>
                </div>
            </div>
            @endforeach

            <button
                class="w-full py-4 rounded-2xl bg-slate-900 border border-slate-800 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:border-slate-600 transition-all">
                Accéder au registre complet des litiges
            </button>
        </div>
    </div>
</div>