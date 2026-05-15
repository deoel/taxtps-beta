<!DOCTYPE html>
<html lang="fr" class="dark" x-data="{ sidebarOpen: true, filtersOpen: true, refreshing: false }">
<head>
    <meta charset="UTF-8">
    <title>TAXTPS | Ultimate Intelligence Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #f8fafc; overflow: hidden; }
        .cyber-panel { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.1); transition: all 0.3s ease; }
        .cyber-panel:hover { border-color: rgba(59, 130, 246, 0.3); box-shadow: 0 0 20px rgba(59, 130, 246, 0.05); }
        .filter-input { background: #0f172a; border: 1px solid #1e293b; border-radius: 6px; padding: 6px 12px; font-size: 11px; color: white; outline: none; }
        .filter-input:focus { border-color: #3b82f6; }
        .refresh-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <nav class="h-14 border-b border-white/5 flex items-center justify-between px-6 bg-slate-950/80 sticky top-0 z-[1000]">
        <div class="flex items-center gap-8">
            <h1 class="text-lg font-black tracking-tighter text-blue-400 font-['Orbitron'] uppercase">Intelligence <span class="text-white">v6.2</span></h1>
            
            <div class="flex items-center gap-4 bg-white/5 px-3 py-1.5 rounded-full border border-white/5">
                <span class="text-[10px] font-mono text-gray-500 uppercase tracking-widest">Auto-Refresh</span>
                <button @click="refreshing = true; setTimeout(() => { window.location.reload() }, 1000)" 
                        class="text-blue-400 hover:text-blue-300 transition-all" :class="refreshing ? 'refresh-spin' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button @click="filtersOpen = !filtersOpen" class="flex items-center gap-2 px-4 py-2 bg-blue-600 rounded text-[10px] font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtres Avancés
            </button>
        </div>
    </nav>

    <div x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900 border-b border-white/5 p-4 shadow-2xl relative z-[999]">
        <form action="{{ route('war-room.hub') }}" method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] uppercase font-bold text-gray-500">Période</label>
                <input type="text" name="date_range" placeholder="AAAA-MM-JJ - AAAA-MM-JJ" value="{{ $request->date_range }}" class="filter-input">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] uppercase font-bold text-gray-500">Province</label>
                <select name="province_id" class="filter-input">
                    <option value="">Toutes</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}" {{ $request->province_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] uppercase font-bold text-gray-500">Bureau</label>
                <select name="office_id" class="filter-input">
                    <option value="">Tous les bureaux</option>
                    @foreach($allOffices as $o)
                        <option value="{{ $o->id }}" {{ $request->office_id == $o->id ? 'selected' : '' }}>{{ $o->name }} ({{ $o->code_bureau }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] uppercase font-bold text-gray-500">Statut Dossier</label>
                <select name="status" class="filter-input">
                    <option value="">Tous les statuts</option>
                    <option value="valide">Validé (TAXTPS)</option>
                    <option value="suspect">En attente / Suspect</option>
                    <option value="alerte">Critique</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[9px] uppercase font-bold text-gray-500">Risque Min.</label>
                <input type="number" name="min_risk" min="1" max="10" value="{{ $request->min_risk }}" placeholder="1-10" class="filter-input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-grow bg-blue-600 py-1.5 rounded text-[10px] font-black uppercase">Appliquer</button>
                <a href="{{ route('war-room.hub') }}" class="bg-white/5 px-3 py-1.5 rounded text-[10px] font-black uppercase text-gray-400">RAZ</a>
            </div>
        </form>
    </div>

    <main class="h-[calc(100vh-56px)] grid grid-cols-12 gap-4 p-4 overflow-hidden">
        
        <div class="col-span-12 lg:col-span-3 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-2">
            
            <div class="grid grid-cols-1 gap-4">
                <div class="cyber-panel p-4 rounded-xl border-l-4 border-blue-500">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Total Potentiel (Filtres)</p>
                    <p class="text-2xl font-black font-mono text-white mt-1">{{ number_format($stats['total_sydonia'], 0, '.', ' ') }} $</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-[10px] px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded-full font-bold">SYDONIA DATA</span>
                    </div>
                </div>
            </div>

            <div class="cyber-panel rounded-xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-white/5 bg-white/5">
                    <h3 class="text-[11px] font-black uppercase text-amber-400 flex items-center gap-2 tracking-widest underline decoration-2 underline-offset-4 decoration-amber-500/30">
                        Top 5 Importateurs
                    </h3>
                </div>
                <div class="p-2 space-y-2">
                    @foreach($topImportateurs as $imp)
                    <div class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all border border-transparent hover:border-white/10 cursor-pointer group">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-xs font-bold text-gray-200 group-hover:text-blue-400 truncate w-3/4">{{ $imp->importateur }}</span>
                            <span class="text-[10px] font-mono text-gray-500">#{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $imp->nbr_dossiers }} dossiers</span>
                            <span class="text-xs font-black text-amber-400">{{ number_format($imp->total_du, 0) }} $</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="cyber-panel rounded-xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-white/5 bg-white/5">
                    <h3 class="text-[11px] font-black uppercase text-emerald-400 flex items-center gap-2 tracking-widest underline decoration-2 underline-offset-4 decoration-emerald-500/30">
                        Agents les plus actifs
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($agentPerformance as $agent)
                    <div class="relative">
                        <div class="flex justify-between text-[10px] mb-1 font-bold">
                            <span class="text-gray-300">{{ $agent->name }}</span>
                            <span class="text-emerald-400">{{ $agent->dossiers_traites }} CERTIF.</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ ($agent->dossiers_traites / max($stats['dossiers_count'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6 flex flex-col gap-4">
            <div class="flex-grow cyber-panel rounded-2xl overflow-hidden relative group">
                <div id="map" class="h-full w-full"></div>
                
                <div class="absolute top-4 left-4 z-[1000] p-4 cyber-panel rounded-xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Aperçu Stratégique</p>
                    <h4 class="text-sm font-black text-blue-400 mt-1">SANTÉ PUBLIQUE RDC</h4>
                    <div class="mt-2 space-y-1">
                        <div class="flex justify-between gap-8 text-[10px]">
                            <span class="text-gray-400">Taux Performance:</span>
                            <span class="font-mono font-bold text-emerald-400">{{ round($stats['performance'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-1/3 cyber-panel rounded-2xl p-6 flex flex-col justify-center">
                <div class="flex items-end justify-between gap-8">
                    <div class="flex-grow">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black uppercase text-gray-500 tracking-tighter">Flux de Recouvrement (Potentiel vs Réel)</span>
                            <span class="text-xs font-black text-red-500 font-mono">- {{ number_format($stats['ecart'], 0) }} $ (MANQUE À GAGNER)</span>
                        </div>
                        <div class="h-4 w-full bg-white/5 rounded-full overflow-hidden flex border border-white/5">
                            <div class="h-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.4)]" style="width: {{ $stats['performance'] }}%"></div>
                            <div class="h-full bg-red-600/30 animate-pulse" style="width: {{ 100 - $stats['performance'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-3 cyber-panel rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 border-b border-white/5 bg-red-500/5 flex justify-between items-center">
                <h3 class="text-xs font-black uppercase text-red-400 flex items-center gap-2 tracking-widest">
                    Cibles Prioritaires ({{ count($highRiskFiles ?? []) }})
                </h3>
                <span class="text-[9px] font-mono text-gray-500 animate-pulse">LIVE UPDATE</span>
            </div>
            
            <div class="flex-grow overflow-y-auto custom-scrollbar p-3 space-y-3">
                @foreach($highRiskFiles ?? [] as $risk)
                <div class="p-4 rounded-xl bg-slate-900 border border-white/5 hover:border-red-500/40 transition-all cursor-pointer group shadow-lg"
                     @click="selectedRisk = {{ $risk }}; showRiskDetail = true">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-mono font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded">{{ $risk->numero_dcl }}</span>
                        <div class="flex gap-1">
                            @for($i=0; $i < floor($risk->priority_score / 2); $i++)
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            @endfor
                        </div>
                    </div>
                    <p class="text-[11px] font-black text-white truncate group-hover:text-red-400 transition-colors uppercase tracking-tight">{{ $risk->importateur }}</p>
                    <div class="mt-3 flex justify-between items-end border-t border-white/5 pt-2">
                        <div>
                            <p class="text-[8px] text-gray-500 uppercase font-bold">Bureau</p>
                            <p class="text-[9px] text-gray-300 font-bold uppercase">{{ $risk->office->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-white font-mono">{{ number_format($risk->taxe_due, 0) }} $</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-11.66, 27.47], 6);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

            const offices = @json($offices);
            offices.forEach(office => {
                if(office.latitude && office.longitude) {
                    const color = office.alertes > 10 ? '#ef4444' : '#3b82f6';
                    const marker = L.circleMarker([office.latitude, office.longitude], {
                        radius: office.alertes > 5 ? 10 : 6,
                        fillColor: color,
                        color: "#fff",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);

                    marker.bindPopup(`
                        <div class="bg-slate-900 text-white p-3 rounded-lg border border-white/10 min-w-[180px]">
                            <h4 class="text-xs font-black uppercase text-blue-400">${office.name}</h4>
                            <p class="text-[9px] text-gray-500 mt-1">CODE: ${office.code_bureau}</p>
                            <div class="mt-3 space-y-2">
                                <div class="flex justify-between text-[10px]">
                                    <span>Alertes Fraude:</span>
                                    <b class="text-red-500">${office.alertes}</b>
                                </div>
                                <button class="w-full bg-blue-600 py-1.5 rounded text-[9px] font-black uppercase mt-2">Détails Bureau</button>
                            </div>
                        </div>
                    `, { className: 'custom-popup', closeButton: false });
                }
            });
        });
    </script>
</body>
</html>