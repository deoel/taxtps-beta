<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ $title ?? 'TaxTPS' }}</title>
    @fluxStyles
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <x-layouts.app.sidebar>
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
