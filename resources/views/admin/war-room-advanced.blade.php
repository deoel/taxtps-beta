<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAXTPS | Intelligence Hub v6.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #f8fafc; overflow: hidden; }
        .cyber-panel { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(59, 130, 246, 0.1); }
        .glitch-text { font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
        #map { height: 100%; width: 100%; background: #0f172a; border-radius: 0.75rem; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
    </style>
</head>
<body x-data="{ 
    selectedOffice: null, 
    activeTab: 'map', 
    sidebarOpen: true,
    showRiskDetail: false,
    selectedRisk: {} 
}">

    <nav class="h-16 border-b border-white/5 flex items-center justify-between px-6 bg-slate-950/50 backdrop-blur-md relative z-50">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tighter glitch-text text-blue-400">TAXTPS <span class="text-white">COMMAND HUB</span></h1>
                <p class="text-[10px] font-mono text-gray-500 uppercase tracking-widest">Souveraineté Financière & Santé Publique</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Statut Serveur Sydonia</p>
                <p class="text-xs text-emerald-400 font-mono flex items-center gap-2 justify-end">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> CONNECTÉ
                </p>
            </div>
            <button class="bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/30 px-4 py-2 rounded-md text-xs font-bold transition-all">
                GÉNÉRER RAPPORT IGF
            </button>
        </div>
    </nav>

    <main class="h-[calc(100vh-64px)] grid grid-cols-12 gap-4 p-4">
        
        <div class="col-span-12 lg:col-span-3 flex flex-col gap-4 overflow-hidden">
            <div class="grid grid-cols-2 gap-3">
                <div class="cyber-panel p-3 rounded-xl border-l-2 border-blue-500">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Potentiel Sydonia</p>
                    <p class="text-lg font-black font-mono">{{ number_format($stats['total_sydonia'], 0, '.', ' ') }} <small class="text-[10px]">$</small></p>
                </div>
                <div class="cyber-panel p-3 rounded-xl border-l-2 border-emerald-500">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Validé TAXTPS</p>
                    <p class="text-lg font-black font-mono text-emerald-400">{{ number_format($stats['total_valide'], 0, '.', ' ') }}</p>
                </div>
                <div class="cyber-panel p-3 rounded-xl border-l-2 border-red-500">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Écart Évasion</p>
                    <p class="text-lg font-black font-mono text-red-500">{{ number_format($stats['ecart'], 0, '.', ' ') }}</p>
                </div>
                <div class="cyber-panel p-3 rounded-xl border-l-2 border-amber-500">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Taux de Couverture</p>
                    <p class="text-lg font-black font-mono text-amber-500">{{ round($stats['taux_couverture'], 1) }}%</p>
                </div>
            </div>

            <div class="cyber-panel flex-grow rounded-xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
                    <h3 class="text-xs font-black uppercase text-red-400 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Ciblage Prioritaire
                    </h3>
                </div>
                <div class="flex-grow overflow-y-auto custom-scrollbar p-2 space-y-2">
                    @foreach($highRiskFiles as $risk)
                    <div 
                        @click="showRiskDetail = true; selectedRisk = {{ $risk }}"
                        class="p-3 rounded-lg bg-red-500/5 border border-red-500/10 hover:bg-red-500/10 cursor-pointer transition-all group"
                    >
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-mono font-bold text-gray-400">{{ $risk->numero_dcl }}</span>
                            <span class="text-[10px] font-black px-1.5 py-0.5 bg-red-500/20 text-red-400 rounded">SCORE: {{ $risk->priority_score }}</span>
                        </div>
                        <p class="text-xs font-bold mt-1 text-gray-200 group-hover:text-white transition-colors truncate">{{ $risk->importateur }}</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[9px] text-gray-500 uppercase">{{ $risk->office->name }}</span>
                            <span class="text-[10px] font-mono text-red-300">{{ number_format($risk->taxe_due, 0) }} $</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6 flex flex-col gap-4">
            <div class="flex gap-2 p-1 bg-slate-900 rounded-lg w-fit border border-white/5">
                <button @click="activeTab = 'map'" :class="activeTab === 'map' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-300'" class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all">Flux Géographique</button>
                <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-300'" class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all">Analyse Provinciale</button>
            </div>

            <div class="flex-grow cyber-panel rounded-xl overflow-hidden relative">
                <div x-show="activeTab === 'map'" class="h-full w-full" x-transition>
                    <div id="map"></div>
                    <div class="absolute bottom-4 left-4 z-[1000] cyber-panel p-3 rounded-lg border border-white/10 pointer-events-none">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Légende Risque</p>
                        <div class="flex gap-4 mt-2">
                            <span class="flex items-center gap-1.5 text-[9px] font-bold"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span> SÛR</span>
                            <span class="flex items-center gap-1.5 text-[9px] font-bold"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> VIGILANCE</span>
                            <span class="flex items-center gap-1.5 text-[9px] font-bold"><span class="w-2 h-2 bg-red-500 rounded-full"></span> CRITIQUE</span>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'stats'" class="h-full p-6 overflow-y-auto custom-scrollbar" x-transition>
                    <h2 class="text-xl font-black mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                        RECOUVRERMENT PAR PROVINCE
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($provincePerformance as $prov)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-blue-500/30 transition-all">
                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">{{ $prov['name'] }}</p>
                                    <p class="text-lg font-black text-white font-mono mt-1">{{ number_format($prov['total'], 0) }} <small class="text-blue-400">$</small></p>
                                </div>
                                <div class="text-right text-[10px] font-bold text-gray-400">
                                    {{ $prov['valide'] }} / {{ $prov['count'] }} <span class="text-emerald-500">DOSSIERS</span>
                                </div>
                            </div>
                            <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-600 to-cyan-400" 
                                     style="width: {{ $stats['total_sydonia'] > 0 ? ($prov['total'] / $stats['total_sydonia']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-3 cyber-panel rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 border-b border-white/5 bg-blue-500/5">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <h3 class="text-xs font-black uppercase text-emerald-400 tracking-widest">Flux de Certification Terrain</h3>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-slate-900 text-[9px] uppercase text-gray-500 font-black border-b border-white/5">
                        <tr>
                            <th class="px-4 py-3">Agent / Bureau</th>
                            <th class="px-4 py-3">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($latestAudits as $audit)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-[11px] font-bold text-gray-200">{{ $audit->agent->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-blue-400 font-mono">{{ $audit->office->code_bureau }}</span>
                                    <span class="text-[9px] text-gray-600">•</span>
                                    <span class="text-[9px] text-gray-500 italic">{{ $audit->updated_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $audit->statut === 'valide' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ $audit->statut }}
                                    </span>
                                    @if($audit->gps_validated)
                                    <span class="text-[8px] text-emerald-500 font-mono flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                        GPS OK
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div x-show="showRiskDetail" x-transition class="fixed inset-0 z-[2000] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="showRiskDetail = false" class="cyber-panel max-w-2xl w-full rounded-2xl overflow-hidden shadow-2xl border-blue-500/30">
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-blue-600/10">
                <h2 class="text-lg font-black tracking-tighter uppercase flex items-center gap-3">
                    <span class="px-3 py-1 bg-red-500 text-white rounded text-xs">DOSSIER CRITIQUE</span>
                    <span x-text="selectedRisk.numero_dcl"></span>
                </h2>
                <button @click="showRiskDetail = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8 grid grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Importateur</p>
                        <p class="text-md font-bold text-white" x-text="selectedRisk.importateur"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">CIF Global</p>
                            <p class="font-mono text-blue-400" x-text="new Intl.NumberFormat('fr-FR').format(selectedRisk.montant_cif) + ' $'"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">TPS Dûe</p>
                            <p class="font-mono text-red-400" x-text="new Intl.NumberFormat('fr-FR').format(selectedRisk.taxe_due) + ' $'"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white/5 p-4 rounded-xl border border-white/5 space-y-4">
                    <h4 class="text-xs font-black uppercase text-gray-400">Indicateurs de Menace</h4>
                    <div class="space-y-2">
                        <template x-if="selectedRisk.priority_score > 8">
                            <div class="flex items-center gap-2 text-[10px] text-red-400 bg-red-500/10 p-2 rounded">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"></path></svg>
                                VALEUR ATYPIQUE DÉTECTÉE
                            </div>
                        </template>
                        <div class="flex items-center gap-2 text-[10px] text-amber-400 bg-amber-500/10 p-2 rounded">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V7z"></path></svg>
                            ZONE À HAUT RISQUE FRONTALIER
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-white/5 bg-slate-900/50 flex justify-end gap-4">
                <button class="px-6 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold transition-all">VOIR SYDONIA</button>
                <button class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-black shadow-lg shadow-blue-500/20 transition-all uppercase">Déployer Inspection</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // MAP CONFIGURATION
            const map = L.map('map', { zoomControl: false }).setView([-11.66, 27.47], 6);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: 'TAXTPS Intelligence'
            }).addTo(map);

            const offices = @json($offices);

            offices.forEach(office => {
                if(office.lat && office.lng) {
                    const marker = L.circleMarker([office.lat, office.lng], {
                        radius: office.alertes > 10 ? 12 : (office.alertes > 0 ? 8 : 5),
                        fillColor: office.color,
                        color: "#fff",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.7
                    }).addTo(map);

                    // INTERACTIVITÉ : Popup riche au clic
                    marker.bindPopup(`
                        <div class="bg-slate-900 text-white p-3 rounded-lg border border-white/10 min-w-[200px]">
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">${office.code}</p>
                            <h4 class="text-md font-black mt-1">${office.name}</h4>
                            <div class="h-px bg-white/10 my-2"></div>
                            <div class="flex justify-between text-xs mb-1">
                                <span>Collecte:</span>
                                <span class="font-mono font-bold text-emerald-400">${new Intl.NumberFormat().format(office.total_collecte)} $</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span>Alertes Fraude:</span>
                                <span class="font-mono font-bold text-red-500">${office.alertes}</span>
                            </div>
                            <button onclick="window.location.href='/admin/offices/${office.id}'" class="w-full mt-3 bg-blue-600 py-1.5 rounded text-[10px] font-black uppercase tracking-widest">Analyses Détaillées</button>
                        </div>
                    `, { className: 'custom-popup', closeButton: false });
                }
            });

            // Re-render map when tab changes
            window.addEventListener('resize', () => map.invalidateSize());
        });
    </script>
</body>
</html>