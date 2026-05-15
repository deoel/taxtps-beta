<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>War Room | TAXTPS V6.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #030712; }
        .cyber-panel { background: rgba(17, 24, 39, 0.8); border: 1px solid #1f2937; backdrop-filter: blur(10px); }
        .glitch-text { font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
        #map { height: 500px; border-radius: 0.75rem; border: 1px solid #374151; }
    </style>
</head>
<body class="text-gray-100 p-6 overflow-hidden h-screen" x-data="{ activeTab: 'map' }">

    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold glitch-text text-blue-500 uppercase tracking-widest">War Room - TAXTPS</h1>
            <p class="text-gray-400 text-sm italic">Surveillance en temps réel du recouvrement provincial</p>
        </div>
        <div class="flex space-x-4">
            <div class="text-right">
                <span class="block text-xs text-gray-500 uppercase">Statut Système</span>
                <span class="text-green-400 font-mono text-sm animate-pulse">● CONNECTÉ À SYDONIA</span>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-sm font-bold transition">EXPORTER PDF MINISTRE</button>
        </div>
    </header>

    <div class="grid grid-cols-12 gap-6 h-[85vh]">
        
        <div class="col-span-3 space-y-4 overflow-y-auto">
            <div class="cyber-panel p-4 rounded-xl border-l-4 border-blue-500">
                <span class="text-gray-400 text-xs uppercase font-semibold">Total Potentiel (SYDONIA)</span>
                <div class="text-2xl font-bold text-white mt-1" x-data="{ val: 0 }" x-init="setTimeout(() => val = {{ $stats['total_sydonia'] }}, 500)">
                    <span x-text="val.toLocaleString()"></span> <small class="text-blue-400">USD</small>
                </div>
            </div>

            <div class="cyber-panel p-4 rounded-xl border-l-4 border-green-500">
                <span class="text-gray-400 text-xs uppercase font-semibold">Recettes Sécurisées (TAXTPS)</span>
                <div class="text-2xl font-bold text-green-400 mt-1">
                    {{ number_format($stats['total_valide'], 2) }} <small>USD</small>
                </div>
            </div>

            <div class="cyber-panel p-4 rounded-xl border-l-4 border-red-600 animate-pulse">
                <span class="text-gray-400 text-xs uppercase font-semibold text-red-400">Évasion / Écart de Recettes</span>
                <div class="text-2xl font-bold text-red-500 mt-1">
                    {{ number_format($stats['ecart'], 2) }} <small>USD</small>
                </div>
            </div>

            <div class="cyber-panel p-4 rounded-xl border-l-4 border-orange-500">
                <span class="text-gray-400 text-xs uppercase font-semibold">Alertes de Fraude Actives</span>
                <div class="text-3xl font-bold text-orange-500 mt-1">{{ $stats['dossiers_suspects'] }}</div>
            </div>

            <div class="cyber-panel p-4 rounded-xl">
                <span class="text-gray-400 text-xs uppercase font-semibold">Taux de Couverture Terrain</span>
                <div class="w-full bg-gray-700 rounded-full h-2.5 mt-3">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $stats['taux_couverture'] }}%"></div>
                </div>
                <div class="text-right text-xs mt-1 text-gray-400">{{ round($stats['taux_couverture'], 1) }}%</div>
            </div>
        </div>

        <div class="col-span-6 space-y-4">
            <div class="flex space-x-4 mb-2">
                <button @click="activeTab = 'map'" :class="activeTab === 'map' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-gray-500'" class="pb-2 text-sm font-bold uppercase tracking-widest">Cartographie</button>
                <button @click="activeTab = 'analysis'" :class="activeTab === 'analysis' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-gray-500'" class="pb-2 text-sm font-bold uppercase tracking-widest">Analyse SH</button>
            </div>

            <div x-show="activeTab === 'map'" class="relative h-full">
                <div id="map"></div>
            </div>

            <div x-show="activeTab === 'analysis'" class="cyber-panel p-6 rounded-xl h-[500px]">
                <h3 class="text-lg font-bold mb-4">Top 5 Marchandises Suspectes (Codes SH)</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span>2203.00.00 (Alcools)</span>
                        <span class="text-red-400 font-mono">12 Alertes</span>
                    </div>
                    <div class="w-full bg-gray-800 h-2 rounded overflow-hidden">
                        <div class="bg-red-500 h-full" style="width: 85%"></div>
                    </div>
                    </div>
            </div>
        </div>

        <div class="col-span-3 cyber-panel rounded-xl flex flex-col h-full">
            <div class="p-4 border-b border-gray-800">
                <h3 class="text-sm font-bold uppercase text-blue-400 tracking-tighter">Flux de Validation Mobile</h3>
            </div>
            <div class="flex-grow overflow-y-auto p-4 space-y-4">
                @foreach($recentActions as $action)
                <div class="border-b border-gray-800 pb-3">
                    <div class="flex justify-between text-[10px] text-gray-500 mb-1">
                        <span>{{ $action->updated_at->diffForHumans() }}</span>
                        <span class="text-blue-500">{{ $action->office->code_bureau }}</span>
                    </div>
                    <p class="text-xs font-semibold">Agent {{ $action->agent->name }}</p>
                    <p class="text-[11px] text-gray-400 truncate">DCL: {{ $action->numero_dcl }} | {{ $action->importateur }}</p>
                    <div class="flex items-center mt-2">
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold {{ $action->statut == 'valide' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                            {{ $action->statut }}
                        </span>
                        @if($action->gps_validated)
                            <span class="ml-2 text-green-500 text-[10px]">✔ GPS Certifié</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Initialisation de la carte Leaflet
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map').setView([-11.66, 27.47], 6); // Centré sur le Haut-Katanga

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Données passées par le contrôleur
            const offices = @json($offices);

            offices.forEach(office => {
                if(office.lat && office.lng) {
                    const marker = L.circleMarker([office.lat, office.lng], {
                        radius: 8,
                        fillColor: office.color,
                        color: "#fff",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);

                    marker.bindPopup(`
                        <div class="text-gray-900 font-bold">
                            ${office.name} <br>
                            <span class="text-red-600">${office.alertes} alertes de fraude</span>
                        </div>
                    `);
                }
            });
        });
    </script>
</body>
</html>