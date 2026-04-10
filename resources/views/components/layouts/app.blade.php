<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    isDesktop: window.innerWidth >= 1024,
    init() {
        this.isDesktop = window.innerWidth >= 1024;
        try {
            const stored = localStorage.getItem('sidebar');
            if (stored !== null) this.sidebarOpen = stored === '1';
            else this.sidebarOpen = this.isDesktop;
        } catch(e) {}
        window.addEventListener('resize', () => {
            this.isDesktop = window.innerWidth >= 1024;
        });
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        try { localStorage.setItem('sidebar', this.sidebarOpen ? '1' : '0'); } catch(e) {}
    }
}" :class="{ 'overflow-hidden': sidebarOpen && !isDesktop }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA --}}
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="mobile-web-app-capable" content="yes">

    <title>{{ config('app.name', 'Gesimmos') }} — @yield('title', 'Dashboard')</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900"
      @keydown.escape.window="if (!isDesktop) sidebarOpen = false">

<div class="flex h-screen overflow-hidden">

    {{-- ══════════════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════════════ --}}
    <aside
        x-show="sidebarOpen || isDesktop"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed lg:static inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900"
        x-cloak
    >
        {{-- Logo --}}
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-700/60 bg-slate-950 px-5">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                <img src="{{ asset('images/Image1.jpg') }}" alt="Logo" class="h-9 w-auto object-contain rounded">
            </a>
            <button @click="toggleSidebar()" class="lg:hidden rounded-md p-1 text-slate-400 hover:text-white focus:outline-none" aria-label="Fermer le menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <x-layouts.sidebar-menu />

        {{-- User info --}}
        @auth
        <div class="shrink-0 border-t border-slate-700/60 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->users ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-200">{{ auth()->user()->users ?? 'Utilisateur' }}</p>
                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->role_name }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion" class="rounded p-1 text-slate-500 hover:text-slate-200 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- Overlay mobile --}}
    <div
        x-show="sidebarOpen && !isDesktop"
        @click="toggleSidebar()"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        x-cloak
    ></div>

    {{-- ══════════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════════ --}}
    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

        {{-- Header --}}
        <header class="flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm lg:px-6">
            {{-- Hamburger --}}
            <button @click="toggleSidebar()" class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors" aria-label="Menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <div class="flex-1"></div>

            {{-- Actions header --}}
            @auth
            <div class="flex items-center gap-2">
                {{-- Badge rôle --}}
                <span class="hidden sm:inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                       (auth()->user()->role === 'admin_stock' ? 'bg-indigo-100 text-indigo-700' :
                       (auth()->user()->role === 'agent_stock' ? 'bg-cyan-100 text-cyan-700' :
                       'bg-slate-100 text-slate-600')) }}">
                    {{ auth()->user()->role_name }}
                </span>
                <span class="hidden sm:block text-sm font-medium text-slate-700">{{ auth()->user()->users }}</span>
            </div>
            @endauth
        </header>

        {{-- Notifications flash --}}
        @foreach(['success' => ['border-l-green-500', 'bg-green-50', 'text-green-800', 'border-green-200'], 'error' => ['border-l-red-500', 'bg-red-50', 'text-red-800', 'border-red-200'], 'warning' => ['border-l-amber-500', 'bg-amber-50', 'text-amber-800', 'border-amber-200'], 'info' => ['border-l-blue-500', 'bg-blue-50', 'text-blue-800', 'border-blue-200']] as $type => $classes)
            @if(session($type))
            <div class="border-l-4 {{ $classes[0] }} {{ $classes[1] }} {{ $classes[2] }} border {{ $classes[3] }} px-4 py-3 text-sm" role="{{ $type === 'success' || $type === 'info' ? 'status' : 'alert' }}">
                {{ session($type) }}
            </div>
            @endif
        @endforeach

        {{-- Page content --}}
        <main id="main-content" class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

@livewireScripts

<script>
    // Intercepter les réponses 401 (session expirée) des requêtes Livewire
    // et rediriger vers la page de login au lieu de crasher.
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 401) {
                    preventDefault();
                    window.location.href = '{{ route('login') }}';
                }
            });
        });
    });
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('sw.js') }}?v={{ filemtime(public_path('sw.js')) }}')
                .then(r => { setInterval(() => r.update(), 60000); })
                .catch(() => {});
        });
    }
</script>

@stack('scripts')
</body>
</html>
