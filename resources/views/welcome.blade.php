<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Connexion') }} | TAXTPS DATA SYSTEM</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617; /* Slate 950 */
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(30, 58, 138, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 0% 0%, rgba(30, 58, 138, 0.1) 0%, transparent 30%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cyber-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.05);
        }

        .glitch-text {
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .input-cyber {
            background: rgba(2, 6, 23, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transition: all 0.3s ease;
        }

        .input-cyber:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.2) !important;
        }

        .btn-cyber {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-cyber:hover {
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="antialiased">

    <div class="w-full max-w-md p-6">
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-2xl shadow-blue-500/20 mb-4 border border-white/10 overflow-hidden">
                <img src="{{ asset('images/fps-logo.jpg') }}" alt="Logo FPS" class="w-full h-full object-contain p-2">
            </div>
            <h1 class="text-2xl font-black tracking-tighter glitch-text text-blue-400">
                TAXTPS <span class="text-white">DATA SYSTEM</span>
            </h1>
            <div class="flex items-center gap-2 mt-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                <p class="text-[10px] font-mono text-gray-500 uppercase tracking-[0.3em]">Accès Sécurisé / Portail Audit</p>
            </div>
        </div>

        <div class="cyber-panel rounded-3xl p-8">
            <x-auth-session-status class="mb-4 text-center text-emerald-400 text-sm font-mono" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest ml-1">
                        {{ __('Identifiant (Email)') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        class="w-full input-cyber rounded-xl px-4 py-3 text-sm outline-none"
                        placeholder="Saisir l'email">
                    @error('email')
                        <p class="text-red-500 text-[10px] mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label for="password" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ __('Clé d\'accès') }}
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] text-blue-500 hover:text-blue-400 transition-colors uppercase font-bold">
                                {{ __('Oubliée ?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full input-cyber rounded-xl px-4 py-3 text-sm outline-none"
                        placeholder="Mot de passe">
                    @error('password')
                        <p class="text-red-500 text-[10px] mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/10 bg-slate-900 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                        <span class="ml-2 text-xs text-gray-500 group-hover:text-gray-300 transition-colors uppercase tracking-tighter">
                            {{ __('Maintenir la session active') }}
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full btn-cyber text-white py-4 rounded-xl shadow-lg shadow-blue-900/40 text-xs">
                    {{ __('Initialiser Connexion') }}
                </button>
            </form>
        </div>

        <div class="mt-8 flex justify-between items-center px-4">
            <div class="text-[9px] font-mono text-gray-600 uppercase">
                RDC - Finances
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_5px_rgba(16,185,129,0.5)]"></span>
                <span class="text-[9px] font-mono text-gray-600 uppercase">Terminal Sécurisé</span>
            </div>
        </div>
    </div>

</body>
</html>