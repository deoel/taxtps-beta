<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAXTPS | Unified Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #f8fafc; }
        .glass-panel { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); }
        .glitch-text { font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
        #map { height: 100%; width: 100%; border-radius: 1rem; background: #0f172a; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body class="antialiased overflow-hidden h-screen p-4 lg:p-6" x-data="{ activeTab: 'map', loading: false }">

    <header class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-ping"></div>
                <h1 class="text-2xl font-black tracking-tighter glitch-text text-blue-500 uppercase">
                    TAXTPS : Unified Command Center <span class="text-white text-sm opacity-50 font-sans">v6.5</span>
                </h1>
            </div>
            <p class="text-[10px] text-gray-500 font-mono">RECOUVREMENT NATIONAL - MINISTÈRE DE LA SANTÉ</p>
        </div>
        <div class="flex gap-4">
            <div class="text-right hidden md:block">
                <span class="block text-[10px] text-gray-500 uppercase">Synchronisation SYDONIA</span>
                <span class="text-emerald-400 font-mono text-xs animate-pulse">● TRANSMISSION EN COURS</span>
            </div>
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold text-xs transition-all shadow-lg shadow-blue-500/20">
                EXPORTER RAPPORT PDF
            </button>
        </div>
    </header>

    <div class="grid grid-cols-12 gap-6 h-[85vh]">
        
        <div class="col-span-12 lg:col-span-3 space-y-4 overflow-y-auto pr-2">
            <div class="glass-panel p-4 rounded-xl border-l-4 border-blue-500">
                <span class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Total Potentiel SYDONIA</span>
                <div class="text-2xl font-black text-white mt-1">{{ number_format($stats['total_tps'], 0, '.', ' ') }} <small class="text-blue-400 text-xs">USD</small></div>
            </div>

            <div class="glass-panel p-4 rounded-xl border-l-4 border-emerald-500">
                <span class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Recettes Sécurisées</span>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($stats['total_valide'], 0, '.', ' ') }} <small class="text-xs">USD</small></div>
                <div class="w-full bg-white/5 h-1 mt-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full" style="width: {{ $stats['taux_validation'] }}%"></div>
                </div>
            </div>

            <div class="glass-panel p-4 rounded-xl border-l-4 border-red-600 animate-pulse">
                <span class="text-red-400 text-[10px] uppercase font-bold tracking-widest">Écart / Évasion Estimée</span>
                <div class="text-2xl font-black text-red-500 mt-1">{{ number_format($stats['ecart'], 0, '.', ' ') }} <small class="text-xs">USD</small></div>
            </div>

            <div class="glass-panel rounded-xl p-4">
                <h3 class="text-red-500 text-[10px] font-black uppercase mb-4 tracking-tighter">Ciblage Haute Priorité</h3>
                <div class="space-y-3">
                    @foreach($highRiskFiles as $risk)
                    <div class="p-2 bg-red-500/5 border border-red-500/10 rounded-lg group hover:bg-red-500/10 transition-all cursor-pointer">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-mono font-bold text-gray-300">{{ $risk->numero_dcl }}</span>
                            <span class="text-[10px] font-black text-red-500">Score: {{ $risk->priority_score }}</span>
                        </div>
                        <p class="text-[9px] text-gray-500 truncate mt-1">{{ $risk->importateur }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6 flex flex-col gap-4">
            <div class="flex gap-6 border-b border-white/5 pb-2">
                <button @click="activeTab = 'map'" :class="activeTab === 'map' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-500'" class="text-[11px] font-black uppercase pb-2 transition-all">Cartographie Temps Réel</button>
                <button @click="activeTab = 'provinces'" :class="activeTab === 'provinces' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-500'" class="text-[11px] font-black uppercase pb-2 transition-all">Analyse Provinciale</button>
            </div>

            <div class="flex-grow glass-panel rounded-2xl overflow-hidden relative">
                <div x-show="activeTab === 'map'" class="h-full w-full" x-transition>
                    <div id="map"></div>
                </div>

                <div x-show="activeTab === 'provinces'" class="p-6 space-y-6 overflow-y-auto h-full" x-transition>
                    <h3 class="text-lg font-bold mb-4">Recettes par Province</h3>
                    @foreach($provincePerformance as $prov)
                    <div class="group">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-gray-400 uppercase font-bold">{{ $prov['name'] }}</span>
                            <span class="text-blue-400 font-mono">{{ number_format($prov['total'], 0) }} $</span>
                        </div>
                        <div class="h-2 w-full bg-white/5 rounded-full">
                            <div class="h-full bg-gradient-to-r from-blue-600 to-blue-400 rounded-full group-hover:brightness-125 transition-all" 
                                 style="width: {{ $stats['total_tps'] > 0 ? ($prov['total'] / $stats['total_tps']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-3 glass-panel rounded-xl flex flex-col h-full overflow-hidden">
            <div class="p-4 border-b border-white/5 bg-white/5">
                <h3 class="text-[10px] font-black uppercase text-emerald-400 tracking-widest flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Audit Trail Certification
                </h3>
            </div>
            <div class="flex-grow overflow-y-auto p-4 space-y-4">
                @foreach($latestAudits as $audit)
                <div class="border-b border-white/5 pb-3 group">
                    <div class="flex justify-between text-[9px] text-gray-500 mb-2">
                        <span class="font-mono">{{ $audit->updated_at->diffForHumans() }}</span>
                        <span class="text-blue-500 font-bold">{{ $audit->office->code_bureau }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-[10px] font-bold text-blue-400">
                            {{ substr($audit->agent->name, 0, 2) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-[11px] font-bold text-gray-200 truncate">{{ $audit->agent->name }}</p>
                            <p class="text-[10px] text-gray-500 font-mono truncate">{{ $audit->numero_dcl }}</p>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[8px] uppercase font-black {{ $audit->statut == 'valide' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                            {{ $audit->statut }}
                        </span>
                        @if($audit->gps_validated)
                        <span class="text-emerald-400 text-[9px] flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                            GPS CERTIFIED
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialisation Leaflet sur la RDC (Centré Katanga par défaut)
            const map = L.map('map', { zoomControl: false }).setView([-11.66, 27.47], 6);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: 'TAXTPS Intelligence'
            }).addTo(map);

            const offices = @json($offices);

            offices.forEach(office => {
                if(office.lat && office.lng) {
                    L.circleMarker([office.lat, office.lng], {
                        radius: office.alertes > 5 ? 10 : 6,
                        fillColor: office.color,
                        color: "#fff",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.6
                    })
                    .addTo(map)
                    .bindPopup(`<b class="text-slate-900">${office.name}</b><br><span class="text-red-500">${office.alertes} alertes</span>`);
                }
            });

            // Forcer le redimensionnement de la carte lors du changement d'onglet
            window.addEventListener('resize', () => map.invalidateSize());
        });
    </script>
</body>
</html>