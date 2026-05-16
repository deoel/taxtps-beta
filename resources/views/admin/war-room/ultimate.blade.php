<!DOCTYPE html>
<html lang="fr" class="dark" x-data="{ sidebarOpen: true, filtersOpen: true, activeTab: 'map', refreshing: false }">

<head>
    <meta charset="UTF-8">
    <title>TAXTPS | Data System</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="TAXTPS" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617;
            color: #f8fafc;
            overflow: hidden;
        }

        .cyber-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
        }

        .glitch-text {
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .refresh-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 10px;
        }
    </style>
</head>

<body class="antialiased">

    <nav
        class="h-16 border-b border-white/5 flex items-center justify-between px-6 bg-slate-950/50 backdrop-blur-md relative z-50">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/10 overflow-hidden border border-white/10">
                <img src="{{ asset('images/fps-logo.jpg') }}" alt="Logo FPS" class="w-full h-full object-contain p-1">
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tighter glitch-text text-blue-400">
                    <a href="{{ route('war-room.ultimate') }}">TAXTPS <span class="text-white">DATA SYSTEM</span></a>
                </h1>
                <p class="text-[10px] font-mono text-gray-500 uppercase tracking-widest">Souveraineté Financière & Santé
                    Publique</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div
                class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-lg border border-blue-500/20 hover:border-blue-500/40 transition-all">
                <button @click="refreshing = true; setTimeout(() => { window.location.reload() }, 1000)"
                    class="text-blue-400 hover:text-blue-300 transition-all" :class="refreshing ? 'refresh-spin' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                </button>
                <div class="text-left border-l border-white/10 pl-3">
                    <p class="text-[9px] font-mono text-gray-400 uppercase tracking-widest">Dernière synchro</p>
                    <p class="text-xs font-mono font-bold text-blue-400">{{ $lastSyncTime->format('H:i:s') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Statut Serveur Sydonia</p>
                <p class="text-xs text-emerald-400 font-mono flex items-center gap-2 justify-end">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> CONNECTÉ
                </p>
            </div>
            <button
                class="bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/30 px-4 py-2 rounded-md text-xs font-bold transition-all">
                GÉNÉRER RAPPORT IGF
            </button>
        </div>
    </nav>

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        <aside class="w-80 cyber-panel border-r border-white/5 p-6 overflow-y-auto custom-scrollbar"
            x-show="filtersOpen">
            <h2 class="flex items-center gap-3 mb-8">
                <span class="flex items-center justify-center w-8 h-8 rounded border border-blue-500/30 bg-blue-500/5">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                </span>
                <div class="flex flex-col">
                    <span class="text-blue-400 font-black uppercase text-sm tracking-widest">Configuration</span>
                    <span
                        class="text-[9px] text-blue-500/60 font-mono uppercase tracking-[0.2em] -mt-1">Opérationnelle</span>
                </div>
            </h2>

            <form action="{{ route('war-room.ultimate') }}" method="GET" class="space-y-6">
                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">Plage de Dates</label>
                    <div class="flex gap-1 items-center"> <input type="date" name="date_from"
                            value="{{ request('date_from') }}"
                            class="min-w-0 flex-1 bg-slate-900 border border-white/10 rounded-lg px-1.5 py-2 text-[11px] focus:border-blue-500 outline-none text-gray-300">

                        <span class="text-gray-600 text-[10px]">à</span>

                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="min-w-0 flex-1 bg-slate-900 border border-white/10 rounded-lg px-1.5 py-2 text-[11px] focus:border-blue-500 outline-none text-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">Secteur Provincial</label>
                    <select name="province_id"
                        class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Toutes les Provinces</option>
                        @foreach ($provinces as $p)
                            <option value="{{ $p->id }}"
                                {{ request('province_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">Bureau de Douane</label>
                    <select name="office_id"
                        class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Tous les Bureaux</option>
                        @foreach ($allOffices as $o)
                            <option value="{{ $o->id }}"
                                {{ request('office_id') == $o->id ? 'selected' : '' }}>
                                {{ $o->name }} ({{ $o->code_bureau }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-500 font-bold uppercase mb-2">État de Vigilance</label>
                    <select name="status" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="valide">Crédit d'enlèvement</option>
                        <option value="en_attente">Non liquidé</option>
                        <option value="conforme">Exonéré</option>
                        <option value="litige">Contentieux</option>
                        <option value="alerte">Alerte</option>
                        <option value="suspect">Suspect</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl transition shadow-lg shadow-blue-900/20 uppercase text-xs tracking-widest">
                    Appliquer les filtres
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-white/10">
                <div class="flex items-center gap-2 mb-4">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <h3 class="text-[10px] font-black uppercase text-emerald-400 tracking-widest">Audit Trail
                        Certification</h3>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    @foreach ($latestAudits ?? [] as $audit)
                        <div class="border-b border-white/5 pb-3 group">
                            <div class="flex justify-between text-[9px] text-gray-500 mb-2">
                                <span class="font-mono">{{ $audit->updated_at->diffForHumans() }}</span>
                                <span class="text-blue-500 font-bold">{{ $audit->office->code_bureau }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-[10px] font-bold text-blue-400">
                                    {{ substr($audit->agent->name, 0, 2) }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-[11px] font-bold text-gray-200 truncate">{{ $audit->agent->name }}
                                    </p>
                                    <p class="text-[10px] text-gray-500 font-mono truncate">{{ $audit->numero_dcl }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[8px] uppercase font-black {{ $audit->statut == 'valide' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                                    {{ $audit->statut }}
                                </span>
                                @if ($audit->gps_validated)
                                    <span class="text-emerald-400 text-[9px] flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                            </path>
                                        </svg>
                                        GPS CERTIFIED
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-blue-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Potentiel SYDONIA</p>
                    <h3 class="stat-value text-xl text-blue-400 font-black">
                        {{ number_format($stats['total_sydonia'], 2) }} FC</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-emerald-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Recouvrement Réel</p>
                    <h3 class="stat-value text-xl text-emerald-400 font-black">
                        {{ number_format($stats['total_valide'], 2) }} FC</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-red-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Alertes Risques</p>
                    <h3 class="stat-value text-2xl text-red-500 font-black">{{ $stats['dossiers_suspects'] }}</h3>
                </div>
                <div class="cyber-panel p-4 rounded-2xl border-l-4 border-amber-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Performance</p>
                    <h3 class="stat-value text-2xl text-amber-500 font-black">
                        {{ number_format($stats['performance'], 1) }}%</h3>
                </div>
            </div>

            <div class="flex gap-2 p-1 bg-slate-900 rounded-lg w-fit border border-white/5 mb-6">
                <button @click="activeTab = 'map'"
                    :class="activeTab === 'map' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-300'"
                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all">Flux
                    Géographique</button>
                <button @click="activeTab = 'stats'"
                    :class="activeTab === 'stats' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-300'"
                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all">Analyse
                    Provinciale</button>
                <button @click="activeTab = 'banques'"
                    :class="activeTab === 'banques' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-300'"
                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all">Banque</button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[500px]">
                <div class="lg:col-span-2">
                    <div class="cyber-panel rounded-3xl overflow-hidden relative h-full" x-show="activeTab === 'map'"
                        x-transition>
                        <div id="map" class="h-full w-full"></div>
                    </div>
                    <div class="cyber-panel rounded-3xl overflow-hidden relative p-6 space-y-6 h-full"
                        x-show="activeTab === 'stats'" x-transition>
                        <h3 class="text-lg font-black mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                            RECOUVREMENT PAR PROVINCE
                        </h3>
                        <div class="space-y-4 overflow-y-auto custom-scrollbar max-h-[400px]">
                            @foreach ($provincePerformance ?? [] as $prov)
                                <div class="group">
                                    <div class="flex justify-between text-xs mb-2">
                                        <span
                                            class="text-gray-400 uppercase font-bold">{{ $prov['name'] ?? 'Province' }}</span>
                                        <span
                                            class="text-blue-400 font-mono">{{ number_format($prov['total'] ?? 0, 0) }}
                                            FC</span>
                                    </div>
                                    <div class="h-2 w-full bg-white/5 rounded-full">
                                        <div class="h-full bg-gradient-to-r from-blue-600 to-blue-400 rounded-full group-hover:brightness-125 transition-all"
                                            style="width: {{ ($stats['total_sydonia'] ?? 0) > 0 ? (($prov['total'] ?? 0) / $stats['total_sydonia']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="cyber-panel rounded-3xl overflow-hidden relative p-6 h-full"
                        x-show="activeTab === 'banques'" x-transition>
                        <h3 class="text-lg font-black mb-6 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                            RÉPARTITION PAR BANQUE
                        </h3>
                        @php
                            $banks = [
                                'Raw Bank',
                                'Equity BCDC',
                                'BOA',
                                'SOFIBANQUE',
                                'SOLIDAIRE BANQUE',
                                'FIRST BANK',
                                'STANDARD BANK',
                                'TMB',
                                'CADECO',
                            ];
                            $total = $stats['total_valide'] ?? 0;
                            $bankDistribution = [];

                            // CAS 1 : S'il n'y a aucune collecte réelle, tout le monde reste à 0
                            if ($total <= 0) {
                                foreach ($banks as $bank) {
                                    $bankDistribution[$bank] = [
                                        'percentage' => 0,
                                        'amount' => 0,
                                    ];
                                }
                            } else {
                                // CAS 2 : Il y a de l'argent récolté, on génère la distribution factice
                                $percentages = [];
                                $randomSum = 0;

                                foreach ($banks as $bank) {
                                    $percentages[$bank] = rand(1, 20);
                                    $randomSum += $percentages[$bank];
                                }

                                foreach ($banks as $bank) {
                                    $percentage = ($percentages[$bank] / $randomSum) * 100;
                                    $amount = ($percentage / 100) * $total;
                                    
                                    $bankDistribution[$bank] = [
                                        'percentage' => $percentage,
                                        'amount' => $amount,
                                    ];
                                }
                            }
                        @endphp
                        <div class="space-y-4 overflow-y-auto custom-scrollbar max-h-[400px]">
                            @foreach ($bankDistribution as $bankName => $data)
                                <div class="group">
                                    <div class="flex justify-between text-xs mb-2">
                                        <span class="text-gray-400 uppercase font-bold">{{ $bankName }}</span>
                                        <span class="text-blue-400 font-mono">{{ number_format($data['amount'], 0) }}
                                            FC</span>
                                    </div>
                                    <div class="h-2 w-full bg-white/5 rounded-full">
                                        <div class="h-full bg-gradient-to-r from-blue-600 to-blue-400 rounded-full group-hover:brightness-125 transition-all"
                                            style="width: {{ $data['percentage'] }}%">
                                        </div>
                                    </div>
                                    <div class="text-[9px] text-gray-500 mt-1">
                                        {{ number_format($data['percentage'], 1) }}%</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="cyber-panel rounded-3xl p-6 overflow-y-auto">
                    <h3 class="text-xs font-black uppercase mb-4 text-blue-400">Top 5 Importateurs</h3>
                    <div class="space-y-4">
                        @foreach ($topImportateurs as $imp)
                            <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                                <div class="flex justify-between items-start">
                                    <span
                                        class="text-[11px] font-bold truncate w-32 uppercase">{{ $imp->importateur }}</span>
                                    <span
                                        class="text-[10px] font-mono text-emerald-400">{{ number_format($imp->total_du, 0) }}FC</span>
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
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                        Ciblage Haute Priorité
                    </h3>
                    <div class="space-y-3">
                        @foreach ($highRiskFiles as $file)
                            <div
                                class="flex items-center justify-between p-3 bg-red-500/5 rounded-xl border border-red-500/10">
                                <div>
                                    <p class="text-[11px] font-black">{{ $file->num_declaration }}</p>
                                    <p class="text-[9px] text-gray-500">{{ $file->office->name }}</p>
                                </div>
                                <span class="px-3 py-1 bg-red-500 text-white text-[10px] font-black rounded-lg">Score:
                                    {{ $file->priority_score }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cyber-panel rounded-3xl p-6">
                    <h3 class="text-xs font-black uppercase mb-4 text-blue-400">Top Performance Agents</h3>
                    @foreach ($agentPerformance as $agent)
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-black text-xs">
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
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map', {
                zoomControl: false
            }).setView([-4.03, 21.75], 5);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

            const offices = @json($offices);
            offices.forEach(office => {
                if (office.latitude && office.longitude) {
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
                            <p class="text-[10px]">Collecte: <b class="text-emerald-400">${new Intl.NumberFormat().format(office.total_collecte)} FC</b></p>
                            <p class="text-[10px]">Alertes: <b class="text-red-500">${office.alertes}</b></p>
                        </div>
                    `);
                }
            });
        });
    </script>
</body>

</html>
