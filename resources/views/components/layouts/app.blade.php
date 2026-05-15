<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ $title ?? 'TaxTPS' }}</title>
    @fluxStyles
</head>

<body class="min-h-screen bg-zinc-950 text-white antialiased">
    <!-- Ultra-Minimal Navigation Bar -->
    <div
        class="fixed top-0 left-0 right-0 z-40 border-b border-zinc-800/50 bg-zinc-950/95 backdrop-blur-lg h-16 flex items-center px-6">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-4">
                <button class="p-2 hover:bg-zinc-800 rounded-lg transition-colors" data-toggle-sidebar>
                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="hidden md:block">
                    <h1 class="text-lg font-bold tracking-tight">TAXTPS</h1>
                    <p class="text-xs text-zinc-500">Centre de Commandement</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-zinc-500 font-mono">{{ now()->format('H:i') }}</span>
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <span class="text-xs font-bold">M</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsible Minimal Sidebar -->
    <aside
        class="fixed left-0 top-16 bottom-0 w-64 bg-zinc-950 border-r border-zinc-800/50 overflow-y-auto transition-transform duration-300 -translate-x-full md:translate-x-0 z-30 sidebar-panel">
        <nav class="p-6 space-y-8">
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-zinc-900 text-sm font-medium transition-colors group"
                    wire:navigate>
                    <svg class="w-5 h-5 text-zinc-500 group-hover:text-blue-400 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 4h4" />
                    </svg>
                    <span>Tableau de Bord</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-zinc-900 text-sm font-medium transition-colors group">
                    <svg class="w-5 h-5 text-zinc-500 group-hover:text-blue-400 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Rapports</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="fixed left-0 md:left-64 top-16 right-0 bottom-0 overflow-hidden bg-zinc-950">
        <div class="w-full h-full overflow-y-auto">
            {{ $slot }}
        </div>
    </main>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
    <script>
        document.querySelector('[data-toggle-sidebar]')?.addEventListener('click', () => {
            document.querySelector('.sidebar-panel').classList.toggle('-translate-x-full');
        });
    </script>
</body>

</html>
