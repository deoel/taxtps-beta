<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'TaxTPS' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxStyles
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900 antialiased">
        {{-- Le starter kit utilise généralement ce composant pour la structure --}}
        <x-layouts.app.sidebar>
            <flux:main>
                {{ $slot }}
            </flux:main>
        </x-layouts.app.sidebar>

        @fluxScripts
    </body>
</html>