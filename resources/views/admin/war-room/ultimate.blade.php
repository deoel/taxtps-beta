<!DOCTYPE html>
<html lang="fr" class="dark" x-data="{ sidebarOpen: true, filtersOpen: true }">
<head>
    <meta charset="UTF-8">
    <title>TAXTPS | Ultimate War Room v6.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #f8fafc; overflow: hidden; }
        .cyber-panel { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.1); }
        .stat-value { font-family: 'Orbitron', sans-serif; }
    </style>
</head>
<body class="antialiased">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-80 cyber-panel border-r border-white/5 p-6 overflow-y-auto" x-show="filtersOpen">
            <h2 class="text-blue-400 font-black uppercase tracking-tighter mb-6 flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span> Paramètres de l'Unité
            </h2>
            
            <form action="{{ route('war-room.ultimate') }}" method="GET" class="space-y-6">
                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">Secteur Provincial</label>
                    <select name="province_id" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Toutes les Provinces</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p->id }}" {{ request('province_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">Bureau de Douane</label>
                    <select name="office_id" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Tous les Bureaux</option>
                        @foreach($allOffices as $o)
                            <option value="{{ $o->id }}" {{ request('office_id') == $o->id ? 'selected' : '' }}>{{ $o->name }} ({{ $o->code_bureau }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">État de Vigilance</label>
                    <select name="status" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="valide">Validé (Payé)</option>
                        <option value="alerte">Alerte de Fraude</option>
                        <option value="suspect">Suspect</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl transition shadow-lg shadow-blue-900/20 uppercase text-xs tracking-widest">
                    Mettre à jour la vue
                </button>
            </form>
        </aside>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-blue-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Potentiel SYDONIA</p>
                    <h3 class="stat-value text-2xl text-blue-400 font-black">{{ number_format($stats['total_sydonia'], 2) }} $</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-emerald-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Recouvrement Réel</p>
                    <h3 class="stat-value text-2xl text-emerald-400 font-black">{{ number_format($stats['total_valide'], 2) }} $</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-red-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Alertes Fraude</p>
                    <h3 class="stat-value text-2xl text-red-500 font-black">{{ $stats['dossiers_suspects'] }}</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-amber-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Performance</p>
                    <h3 class="stat-value text-2xl text-amber-500 font-black">{{ number_format($stats['performance'], 1) }}%</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[500px]">
                <div class="lg:col-span-2 cyber-panel rounded-3xl overflow-hidden relative" id="map"></div>
                
                <div class="cyber-panel rounded-3xl p-6 overflow-y-auto">
                    <h3 class="text-xs font-black uppercase mb-4 text-blue-400">Top 5 Importateurs</h3>
                    <div class="space-y-4">
                        @foreach($topImportateurs as $imp)
                        <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                            <div class="flex justify-between items-start">
                                <span class="text-[11px] font-bold truncate w-32 uppercase">{{ $imp->importateur }}</span>
                                <span class="text-[10px] font-mono text-emerald-400">{{ number_format($imp->total_du, 0) }}$</span>
                            </div>
                            <div class="w-full bg-white/5 h-1.5 rounded-full mt-2">
                                <div class="bg-blue-500 h-full rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="cyber-panel rounded-3xl p-6">
                    <h3 class="text-xs font-black uppercase mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                        Ciblage Haute Priorité
                    </h3>
                    <div class="space-y-3">
                        @foreach($highRiskFiles as $file)
                        <div class="flex items-center justify-between p-3 bg-red-500/5 rounded-xl border border-red-500/10">
                            <div>
                                <p class="text-[11px] font-black">{{ $file->num_declaration }}</p>
                                <p class="text-[9px] text-gray-500">{{ $file->office->name }}</p>
                            </div>
                            <span class="px-3 py-1 bg-red-500 text-white text-[10px] font-black rounded-lg">Score: {{ $file->priority_score }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="cyber-panel rounded-3xl p-6">
                    <h3 class="text-xs font-black uppercase mb-4 text-blue-400">Top Performance Agents</h3>
                    @foreach($agentPerformance as $agent)
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-black text-xs">
                            {{ substr($agent->name, 0, 2) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between text-[11px] mb-1">
                                <span>{{ $agent->name }}</span>
                                <span class="font-bold">{{ $agent->dossiers_traites }} dossiers</span>
                            </div>
                            <div class="w-full bg-white/5 h-1 rounded-full">
                                <div class="bg-blue-500 h-full rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map', { zoomControl: false }).setView([-11.66, 27.47], 6);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

            const offices = @json($offices);
            offices.forEach(office => {
                if(office.latitude && office.longitude) {
                    const color = office.alertes > 10 ? '#ef4444' : '#3b82f6';
                    const marker = L.circleMarker([office.latitude, office.longitude], {
                        radius: 8,
                        fillColor: color,
                        color: "#fff",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);

                    marker.bindPopup(`
                        <div class="bg-slate-900 text-white p-3 rounded-lg min-w-[160px]">
                            <h4 class="text-xs font-black uppercase">${office.name}</h4>
                            <div class="h-px bg-white/10 my-2"></div>
                            <p class="text-[10px]">Collecte: <b class="text-emerald-400">${new Intl.NumberFormat().format(office.total_collecte)} $</b></p>
                            <p class="text-[10px]">Alertes: <b class="text-red-500">${office.alertes}</b></p>
                        </div>
                    `);
                }
            });
        });
    </script>
</body>
</html>