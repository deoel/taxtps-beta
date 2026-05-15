<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAXTPS | War Room Ministérielle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); }
        .neon-border-blue { border: 1px solid rgba(59, 130, 246, 0.3); box-shadow: 0 0 15px rgba(59, 130, 246, 0.1); }
        body { background-color: #020617; color: #f8fafc; }
    </style>
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen bg-[#05070a] text-gray-200 font-sans antialiased p-4 lg:p-8" 
         x-data="{ 
            activeTab: 'overview', 
            loading: false,
            lastSync: new Date().toLocaleTimeString()
         }">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4 border-b border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-ping"></div>
                    <h1 class="text-2xl font-black tracking-widest uppercase bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">
                        TAXTPS : Command Center v6.0
                    </h1>
                </div>
                <p class="text-xs text-gray-500 font-mono mt-1">SOUVERAINETÉ NUMÉRIQUE - MINISTÈRE DE LA SANTÉ</p>
            </div>

            <div class="flex gap-4">
                <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-lg backdrop-blur-md">
                    <p class="text-[10px] text-gray-400 uppercase">Dernière Sync SYDONIA</p>
                    <p class="text-sm font-mono text-emerald-400" x-text="lastSync"></p>
                </div>
                <button @click="loading = true; setTimeout(() => { loading = false; lastSync = new Date().toLocaleTimeString() }, 2000)" 
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    RAFRAÎCHIR
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <div class="bg-gradient-to-br from-blue-900/20 to-transparent border border-blue-500/30 rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all"></div>
                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-4">Recettes Sécurisées (Total)</h3>
                    <div class="flex items-end gap-2">
                        <span class="text-5xl font-black text-white">{{ number_format($stats['total_tps'], 0, '.', ' ') }}</span>
                        <span class="text-blue-400 font-bold mb-1">USD</span>
                    </div>
                    <div class="mt-4 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 transition-all duration-1000" :style="'width: ' + {{ $stats['taux_validation'] }} + '%'"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2 italic">Basé sur les flux SYDONIA World certifiés </p>
                </div>

                <div class="bg-[#0c0e12] border border-red-500/20 rounded-2xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-red-500 text-xs font-bold uppercase tracking-widest">Ciblage Risque </h3>
                        <span class="px-2 py-1 bg-red-500/10 text-red-500 text-[10px] rounded animate-pulse font-mono">CRITICAL_LEVEL</span>
                    </div>
                    <div class="space-y-4">
                        @foreach($highRiskFiles as $risk)
                        <div class="flex items-center justify-between p-3 bg-red-500/5 border border-red-500/10 rounded-xl hover:bg-red-500/10 transition-colors cursor-pointer">
                            <div>
                                <p class="text-xs font-bold text-gray-300">{{ $risk->numero_dcl }}</p>
                                <p class="text-[10px] text-gray-500">{{ Str::limit($risk->importateur, 20) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-mono font-black text-red-500">{{ $risk->priority_score }}/10</span>
                                <p class="text-[9px] text-gray-600 uppercase">{{ $risk->office->code_bureau }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-8 bg-[#0c0e12] border border-white/5 rounded-3xl p-1 relative overflow-hidden">
                <div class="flex border-b border-white/5 p-4 gap-6">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-500'" class="text-xs font-bold pb-2 transition-all">CARTOGRAPHIE</button>
                    <button @click="activeTab = 'provinces'" :class="activeTab === 'provinces' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-500'" class="text-xs font-bold pb-2 transition-all">ANALYSE PROVINCIALE</button>
                </div>

                <div class="p-6 h-[500px]">
                    <div x-show="activeTab === 'overview'" x-transition class="h-full flex flex-col justify-center items-center">
                         <div class="relative w-full h-full bg-slate-900/50 rounded-xl border border-dashed border-white/10 flex items-center justify-center">
                            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                                <div class="absolute top-1/4 left-1/3 w-2 h-2 bg-emerald-400 rounded-full shadow-[0_0_15px_rgba(52,211,153,0.8)]"></div>
                                <div class="absolute top-1/2 left-1/2 w-2 h-2 bg-blue-400 rounded-full shadow-[0_0_15px_rgba(96,165,250,0.8)]"></div>
                                <div class="absolute bottom-1/4 right-1/4 w-2 h-2 bg-red-400 rounded-full animate-pulse shadow-[0_0_15px_rgba(248,113,113,0.8)]"></div>
                            </div>
                            <span class="text-gray-600 font-mono text-xs uppercase tracking-[0.5em]">Initialisation du moteur Leaflet...</span>
                         </div>
                    </div>

                    <div x-show="activeTab === 'provinces'" x-transition class="space-y-4">
                        @foreach($provincePerformance as $prov)
                        <div class="group">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-400 uppercase">{{ $prov['name'] }}</span>
                                <span class="text-blue-400 font-mono">{{ number_format($prov['total'], 0) }} $</span>
                            </div>
                            <div class="h-1.5 w-full bg-white/5 rounded-full">
                                <div class="h-full bg-gradient-to-r from-blue-600 to-blue-400 rounded-full group-hover:brightness-125 transition-all" 
                                     style="width: {{ ($prov['total'] / max($stats['total_tps'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-span-12 bg-[#0c0e12] border border-white/5 rounded-2xl overflow-hidden">
                <div class="bg-white/5 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04 inter-2.12 2.12 0 00-2.086 2.086c0 5.8 4.662 10.535 10.608 10.535 5.946 0 10.608-4.735 10.608-10.535 0-1.12-.874-2.086-2.086-2.086z"></path></svg>
                        Journal de Certification Terrain (Audit Trail)
                    </h3>
                    <span class="text-[10px] font-mono text-gray-500">REAL_TIME_STREAM_ACTIVE</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] text-gray-500 uppercase border-b border-white/5">
                                <th class="px-6 py-3">Référence DCL</th>
                                <th class="px-6 py-3">Bureau / Province</th>
                                <th class="px-6 py-3">Agent Certificateur</th>
                                <th class="px-6 py-3">Preuve Documentaire</th>
                                <th class="px-6 py-3">Statut GPS [cite: 28]</th>
                                <th class="px-6 py-3 text-right">Horodatage</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @foreach($latestAudits as $audit)
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 font-mono font-bold text-blue-400">{{ $audit->numero_dcl }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-300 font-bold">{{ $audit->office->name }}</p>
                                    <p class="text-[9px] text-gray-600 uppercase">{{ $audit->office->province->name }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-400">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-[8px] font-bold text-blue-400 border border-blue-500/30">
                                            {{ $audit->agent->initials() }}
                                        </div>
                                        {{ $audit->agent->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="flex items-center gap-1 text-[10px] text-emerald-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        PHOTO_RRJ_UPLOADED
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded-full text-[9px] font-bold border border-emerald-500/20">
                                        {{ $audit->gps_validated ? 'CERTIFIÉ (GEOFENCING)' : 'HORS_ZONE' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-gray-600 text-[10px]">{{ $audit->updated_at->format('H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>