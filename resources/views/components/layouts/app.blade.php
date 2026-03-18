<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ 
    sidebarOpen: true, 
    profileOpen: false,
    isDesktop: window.innerWidth >= 768,
    init() {
        // Initialiser isDesktop au chargement
        this.isDesktop = window.innerWidth >= 768;
        if (this.isDesktop) {
            this.sidebarOpen = true;
        }
        // Écouter les changements de taille d'écran
        window.addEventListener('resize', () => {
            this.isDesktop = window.innerWidth >= 768;
            if (this.isDesktop) {
                this.sidebarOpen = true;
            }
        });
    }
}" :class="{ 'overflow-hidden': sidebarOpen && !isDesktop }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Inventaire Pro">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="Application de gestion d'inventaire professionnelle">

    <title>{{ config('app.name', 'Inventaire Pro') }} - @yield('title', 'Dashboard')</title>
    
    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    {{-- PWA Icons --}}
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/icons/icon-512x512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Alpine.js est déjà inclus dans Livewire 3, ne pas le charger séparément --}}

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-scroll-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .sidebar-scroll-hide::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }
        :where(a, button, input, select, textarea, [role="button"]):focus-visible {
            outline: 2px solid #4f46e5;
            outline-offset: 2px;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" @keydown.escape.window="if (!isDesktop) sidebarOpen = false">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-[9999] bg-indigo-600 text-white px-4 py-2 rounded-lg">
        Aller au contenu principal
    </a>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen || isDesktop"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white flex flex-col"
            :class="{ 'translate-x-0': isDesktop || sidebarOpen }"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-gray-900 border-b border-gray-700">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">📦</span>
                    <span class="font-bold text-lg">{{ config('app.name', 'Inventaire Pro') }}</span>
                </div>
                <button @click="sidebarOpen = false" aria-label="Fermer le menu" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-scroll-hide flex-1 overflow-y-auto py-4 px-3" x-data="{ openMenu: '{{ request()->routeIs('biens.*') || request()->routeIs('localisations.*') || request()->routeIs('affectations.*') || request()->routeIs('emplacements.*') || request()->routeIs('designations.*') || request()->routeIs('corbeille.immobilisations.*') ? 'immobilisations' : (request()->routeIs('stock.*') ? 'stock' : '') }}', openImmobilisationsSettings: {{ request()->routeIs('localisations.*') || request()->routeIs('affectations.*') || request()->routeIs('emplacements.*') || request()->routeIs('designations.*') ? 'true' : 'false' }}, openStockSettings: {{ request()->routeIs('stock.magasins.*') || request()->routeIs('stock.categories.*') || request()->routeIs('stock.fournisseurs.*') || request()->routeIs('stock.demandeurs.*') ? 'true' : 'false' }} }">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->canManageInventaire())
                            <!-- IMMOBILISATIONS - Menu avec sous-menus -->
                            <li>
                                <button @click="openMenu = (openMenu === 'immobilisations') ? '' : 'immobilisations'" 
                                        :aria-expanded="(openMenu === 'immobilisations').toString()"
                                        aria-controls="menu-immobilisations"
                                        class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors"
                                        :class="{ 'bg-gray-700 text-white': openMenu === 'immobilisations' }">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <span>Immobilisations</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenu === 'immobilisations' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <ul id="menu-immobilisations" x-show="openMenu === 'immobilisations'" x-transition class="mt-2 space-y-1 pl-4">
                                    <!-- Liste des Immobilisations -->
                                    <li>
                                        <a href="{{ route('biens.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('biens.index') || request()->routeIs('biens.show') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">📋</span>
                                            <span>Liste des Immobilisations</span>
                                        </a>
                                    </li>

                                    <!-- Ajouter Immobilisation -->
                                    <li>
                                        <a href="{{ route('biens.create') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('biens.create') || request()->routeIs('biens.edit') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">➕</span>
                                            <span>Ajouter Immobilisation</span>
                                        </a>
                                    </li>

                                    <!-- Transfert Immobilisation -->
                                    <li>
                                        <a href="{{ route('biens.transfert') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('biens.transfert') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">🔄</span>
                                            <span>Transfert Immobilisation</span>
                                        </a>
                                    </li>

                                    <!-- Historique Transferts -->
                                    <li>
                                        <a href="{{ route('biens.transfert.historique') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('biens.transfert.historique') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">📜</span>
                                            <span>Historique Transferts</span>
                                        </a>
                                    </li>

                                    <!-- Corbeille immos -->
                                    <li>
                                        <a href="{{ route('corbeille.immobilisations.index') }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('corbeille.immobilisations.*') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">🗑️</span>
                                            <span>Corbeille immos</span>
                                        </a>
                                    </li>

                                    <!-- Paramètres - Accordéon -->
                                    <li class="pt-2 mt-2 border-t border-gray-700">
                                        <button @click="openImmobilisationsSettings = !openImmobilisationsSettings"
                                                :aria-expanded="openImmobilisationsSettings.toString()"
                                                aria-controls="menu-immobilisations-settings"
                                                class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                                            <div class="flex items-center">
                                                <span class="mr-2">⚙️</span>
                                                <span class="text-xs font-semibold uppercase">Paramètres</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openImmobilisationsSettings }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <ul id="menu-immobilisations-settings" x-show="openImmobilisationsSettings" x-transition class="mt-1 space-y-1 pl-4">
                                            <!-- Localisations -->
                                            <li>
                                                <a href="{{ route('localisations.index') }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('localisations.*') ? 'bg-gray-700 text-white' : '' }}">
                                                    <span class="mr-2">📍</span>
                                                    <span>Localisations</span>
                                                </a>
                                            </li>

                                            <!-- Affectations -->
                                            <li>
                                                <a href="{{ route('affectations.index') }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('affectations.*') ? 'bg-gray-700 text-white' : '' }}">
                                                    <span class="mr-2">🏢</span>
                                                    <span>Affectations</span>
                                                </a>
                                            </li>

                                            <!-- Emplacements -->
                                            <li>
                                                <a href="{{ route('emplacements.index') }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('emplacements.*') ? 'bg-gray-700 text-white' : '' }}">
                                                    <span class="mr-2">🏠</span>
                                                    <span>Emplacements</span>
                                                </a>
                                            </li>

                                            <!-- Désignations -->
                                            <li>
                                                <a href="{{ route('designations.index') }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('designations.*') ? 'bg-gray-700 text-white' : '' }}">
                                                    <span class="mr-2">📝</span>
                                                    <span>Désignations</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <!-- Inventaires -->
                            <li>
                                <a href="{{ route('inventaires.index') }}" 
                                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('inventaires.*') ? 'bg-gray-700 text-white' : '' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span>Inventaires</span>
                                </a>
                            </li>
                        @endif

                        <!-- STOCK - Menu avec sous-menus -->
                        @if(auth()->check() && auth()->user()->canAccessStock())
                            <li>
                                <button @click="openMenu = (openMenu === 'stock') ? '' : 'stock'" 
                                        :aria-expanded="(openMenu === 'stock').toString()"
                                        aria-controls="menu-stock"
                                        class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors"
                                        :class="{ 'bg-gray-700 text-white': openMenu === 'stock' }">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <span>Stock</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenu === 'stock' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <ul id="menu-stock" x-show="openMenu === 'stock'" x-transition class="mt-2 space-y-1 pl-4">
                                    <!-- Dashboard Stock -->
                                    <li>
                                        <a href="{{ route('stock.dashboard') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">📊</span>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>

                                    <!-- Produits -->
                                    <li>
                                        <a href="{{ route('stock.produits.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.produits.*') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">📦</span>
                                            <span>Produits</span>
                                        </a>
                                    </li>

                                    <!-- Entrées -->
                                    @if(auth()->check() && auth()->user()->canCreateEntree())
                                        <li>
                                            <a href="{{ route('stock.entrees.index') }}" 
                                               class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.entrees.*') ? 'bg-gray-700 text-white' : '' }}">
                                                <span class="mr-2">📥</span>
                                                <span>Entrées</span>
                                            </a>
                                        </li>
                                    @endif

                                    <!-- Sorties -->
                                    <li>
                                        <a href="{{ route('stock.sorties.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.sorties.*') ? 'bg-gray-700 text-white' : '' }}">
                                            <span class="mr-2">📤</span>
                                            <span>Sorties</span>
                                        </a>
                                    </li>

                                    <!-- Paramètres (Admin + Admin_stock) - Accordéon -->
                                    @if(auth()->check() && auth()->user()->canManageStock())
                                        <li class="pt-2 mt-2 border-t border-gray-700">
                                            <button @click="openStockSettings = !openStockSettings"
                                                    :aria-expanded="openStockSettings.toString()"
                                                    aria-controls="menu-stock-settings"
                                                    class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                                                <div class="flex items-center">
                                                    <span class="mr-2">⚙️</span>
                                                    <span class="text-xs font-semibold uppercase">Paramètres</span>
                                                </div>
                                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openStockSettings }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <ul id="menu-stock-settings" x-show="openStockSettings" x-transition class="mt-1 space-y-1 pl-4">
                                                <li>
                                                    <a href="{{ route('stock.magasins.index') }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.magasins.*') ? 'bg-gray-700 text-white' : '' }}">
                                                        <span class="mr-2">🏪</span>
                                                        <span>Magasins</span>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('stock.categories.index') }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.categories.*') ? 'bg-gray-700 text-white' : '' }}">
                                                        <span class="mr-2">🏷️</span>
                                                        <span>Catégories</span>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('stock.fournisseurs.index') }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.fournisseurs.*') ? 'bg-gray-700 text-white' : '' }}">
                                                        <span class="mr-2">🏢</span>
                                                        <span>Fournisseurs</span>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('stock.demandeurs.index') }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('stock.demandeurs.*') ? 'bg-gray-700 text-white' : '' }}">
                                                        <span class="mr-2">👤</span>
                                                        <span>Demandeurs</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if(auth()->check() && auth()->user()->isAdmin())
                            <!-- Utilisateurs -->
                            <li>
                                <a href="{{ route('users.index') }}" 
                                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('users.*') ? 'bg-gray-700 text-white' : '' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span>Utilisateurs</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>

            <!-- Footer Sidebar -->
            <div class="px-4 py-4 border-t border-gray-700">
                <div class="text-xs text-gray-400 mb-2">
                    Version 1.0.0
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div 
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/40 z-40 md:hidden"
            x-cloak
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="sticky top-0 bg-white border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-4 md:px-6 z-30">
                <!-- Left: Hamburger -->
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" aria-label="Ouvrir le menu" class="md:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Right: Notifications + Profile -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr(auth()->user()->users ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->users ?? 'Utilisateur' }}</div>
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-800' : (auth()->user()->role === 'admin_stock' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ auth()->user()->role_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div 
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
                                x-cloak
                            >
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main id="main-content" class="flex-1 overflow-y-auto bg-gray-50">
                <!-- Main Content -->
                <div class="p-4 md:p-6">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg border border-green-200 border-l-4 border-l-green-500 bg-green-50 p-3 text-sm text-green-800" role="status" aria-live="polite">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 rounded-lg border border-red-200 border-l-4 border-l-red-500 bg-red-50 p-3 text-sm text-red-800" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="mb-4 rounded-lg border border-yellow-200 border-l-4 border-l-yellow-500 bg-yellow-50 p-3 text-sm text-yellow-800" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="mb-4 rounded-lg border border-blue-200 border-l-4 border-l-blue-500 bg-blue-50 p-3 text-sm text-blue-800" role="status" aria-live="polite">
                            {{ session('info') }}
                        </div>
                    @endif
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 px-4 md:px-6 py-4 mt-auto">
                    <p class="text-sm text-gray-500 text-center">© {{ now()->year }} {{ config('app.name', 'Inventaire Pro') }}</p>
                </footer>
            </main>
        </div>
    </div>

    @livewireScripts
    
    @stack('scripts')
    
    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then((registration) => {
                        console.log('✅ Service Worker enregistré:', registration.scope);
                        setInterval(() => registration.update(), 60000);
                    })
                    .catch((error) => {
                        console.error('❌ Erreur Service Worker:', error);
                    });
            });
        }
    </script>
</body>
</html>

