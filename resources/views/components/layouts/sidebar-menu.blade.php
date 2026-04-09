@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    $canManageInventaire = $user?->canManageInventaire() ?? false;
    $canAccessStock      = $user?->canAccessStock() ?? false;
    $canCreateEntree     = $user?->canCreateEntree() ?? false;
    $canCreateSortie     = $user?->canCreateSortie() ?? false;
    $canManageStock      = $user?->canManageStock() ?? false;
    $isAdmin             = $user?->isAdmin() ?? false;

    // Détermine l'accordéon ouvert par défaut selon la route active
    $defaultMenu = '';
    if (request()->routeIs('biens.*', 'localisations.*', 'affectations.*', 'emplacements.*', 'designations.*', 'corbeille.*')) {
        $defaultMenu = 'immos';
    } elseif (request()->routeIs('stock.*')) {
        $defaultMenu = 'stock';
    }

    $openImmoSettings  = request()->routeIs('localisations.*', 'affectations.*', 'emplacements.*', 'designations.*');
    $openStockSettings = request()->routeIs('stock.magasins.*', 'stock.categories.*', 'stock.fournisseurs.*', 'stock.demandeurs.*');
@endphp

{{-- ─── Helper macro : nav-link ─── --}}
@php
    function sideNavLink(string $route, string $label, string $icon, array $patterns = []): array {
        return compact('route', 'label', 'icon', 'patterns');
    }
@endphp

<nav
    class="flex-1 overflow-y-auto py-4 px-2 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
    x-data="{
        open: '{{ $defaultMenu }}',
        openImmoSub: {{ $openImmoSettings ? 'true' : 'false' }},
        openStockSub: {{ $openStockSettings ? 'true' : 'false' }},
        toggle(key) {
            this.open = (this.open === key) ? '' : key;
            try { localStorage.setItem('nav_open', this.open); } catch(e) {}
        },
        init() {
            const saved = (() => { try { return localStorage.getItem('nav_open'); } catch(e) { return null; } })();
            // Priorité : route active > sauvegardé
            if ('{{ $defaultMenu }}' !== '') return;
            if (saved) this.open = saved;
        }
    }"
>

    {{-- ── Dashboard ── --}}
    @php $active = request()->routeIs('dashboard'); @endphp
    <a wire:navigate href="{{ route('dashboard') }}"
       class="nav-item {{ $active ? 'nav-active' : 'nav-inactive' }}"
       @if($active) aria-current="page" @endif>
        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
        </svg>
        <span>Dashboard</span>
    </a>

    @auth

    {{-- ══════════════════════════════════════
         IMMOBILISATIONS
    ══════════════════════════════════════ --}}
    @if($canManageInventaire)

        {{-- Accordéon Immobilisations --}}
        <div>
            <button @click="toggle('immos')" type="button"
                class="nav-item w-full {{ $defaultMenu === 'immos' ? 'nav-active' : 'nav-inactive' }}"
                :class="{ 'nav-active': open === 'immos', 'nav-inactive': open !== 'immos' }"
                :aria-expanded="(open === 'immos').toString()">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="flex-1 text-left">Immobilisations</span>
                <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open === 'immos' }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open === 'immos'" x-transition x-cloak class="mt-0.5 ml-3 space-y-0.5 border-l border-slate-700/50 pl-3">

                {{-- Liste --}}
                @php $a = request()->routeIs('biens.index', 'biens.show'); @endphp
                <a wire:navigate href="{{ route('biens.index') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    Liste des immobilisations
                </a>

                {{-- Ajouter --}}
                @php $a = request()->routeIs('biens.create', 'biens.edit'); @endphp
                <a wire:navigate href="{{ route('biens.create') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ajouter
                </a>

                {{-- Transfert --}}
                @php $a = request()->routeIs('biens.transfert'); @endphp
                <a wire:navigate href="{{ route('biens.transfert') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                    Transfert
                </a>

                {{-- Historique --}}
                @php $a = request()->routeIs('biens.transfert.historique'); @endphp
                <a wire:navigate href="{{ route('biens.transfert.historique') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Historique transferts
                </a>

                {{-- Corbeille --}}
                @php $a = request()->routeIs('corbeille.immobilisations.*'); @endphp
                <a wire:navigate href="{{ route('corbeille.immobilisations.index') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Corbeille
                </a>

                {{-- Sous-section Paramètres --}}
                <div class="pt-2">
                    <button @click="openImmoSub = !openImmoSub" type="button"
                        class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Paramètres</span>
                        <svg class="ml-auto h-3 w-3 transition-transform" :class="{ 'rotate-180': openImmoSub }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="openImmoSub" x-transition class="mt-0.5 space-y-0.5">
                        @foreach([
                            ['route' => 'localisations.index', 'patterns' => ['localisations.*'], 'label' => 'Localisations', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                            ['route' => 'affectations.index', 'patterns' => ['affectations.*'], 'label' => 'Affectations', 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
                            ['route' => 'emplacements.index', 'patterns' => ['emplacements.*'], 'label' => 'Emplacements', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                            ['route' => 'designations.index', 'patterns' => ['designations.*'], 'label' => 'Désignations', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3zM6 6h.008v.008H6V6z'],
                        ] as $item)
                        @php $a = request()->routeIs($item['patterns']); @endphp
                        <a wire:navigate href="{{ route($item['route']) }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventaires (lien direct) --}}
        @php $active = request()->routeIs('inventaires.*'); @endphp
        <a href="{{ route('inventaires.index') }}"
           class="nav-item {{ $active ? 'nav-active' : 'nav-inactive' }}"
           @if($active) aria-current="page" @endif>
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
            </svg>
            <span>Inventaires</span>
        </a>

    @endif {{-- canManageInventaire --}}


    {{-- ══════════════════════════════════════
         STOCK
    ══════════════════════════════════════ --}}
    @if($canAccessStock)

        {{-- Séparateur visuel --}}
        <div class="mx-3 my-2 border-t border-slate-700/40"></div>

        <button @click="toggle('stock')" type="button"
            class="nav-item w-full"
            :class="{ 'nav-active': open === 'stock', 'nav-inactive': open !== 'stock' }"
            :aria-expanded="(open === 'stock').toString()">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            <span class="flex-1 text-left">Stock</span>
            <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open === 'stock' }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open === 'stock'" x-transition x-cloak class="mt-0.5 ml-3 space-y-0.5 border-l border-slate-700/50 pl-3">

            {{-- Dashboard stock --}}
            @if(auth()->user()->canViewDashboardStock())
            @php $a = request()->routeIs('stock.dashboard'); @endphp
            <a wire:navigate href="{{ route('stock.dashboard') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                Dashboard
            </a>
            @endif

            {{-- Produits --}}
            @php $a = request()->routeIs('stock.produits.*'); @endphp
            <a wire:navigate href="{{ route('stock.produits.index') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                Produits
            </a>

            {{-- Entrées --}}
            @if($canCreateEntree)
            @php $a = request()->routeIs('stock.entrees.*'); @endphp
            <a wire:navigate href="{{ route('stock.entrees.index') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Entrées
            </a>
            @endif

            {{-- Sorties --}}
            @if($canCreateSortie)
            @php $a = request()->routeIs('stock.sorties.*'); @endphp
            <a wire:navigate href="{{ route('stock.sorties.index') }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Sorties
            </a>
            @endif

            {{-- Paramètres stock --}}
            @if($canManageStock)
            <div class="pt-2">
                <button @click="openStockSub = !openStockSub" type="button"
                    class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-300 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Paramètres</span>
                    <svg class="ml-auto h-3 w-3 transition-transform" :class="{ 'rotate-180': openStockSub }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="openStockSub" x-transition class="mt-0.5 space-y-0.5">
                    @foreach([
                        ['route' => 'stock.magasins.index', 'patterns' => ['stock.magasins.*'], 'label' => 'Magasins', 'icon' => 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z'],
                        ['route' => 'stock.categories.index', 'patterns' => ['stock.categories.*'], 'label' => 'Catégories', 'icon' => 'M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z'],
                        ['route' => 'stock.fournisseurs.index', 'patterns' => ['stock.fournisseurs.*'], 'label' => 'Fournisseurs', 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
                        ['route' => 'stock.demandeurs.index', 'patterns' => ['stock.demandeurs.*'], 'label' => 'Demandeurs', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                    ] as $item)
                    @php $a = request()->routeIs($item['patterns']); @endphp
                    <a wire:navigate href="{{ route($item['route']) }}" class="nav-sub {{ $a ? 'nav-sub-active' : '' }}" @if($a) aria-current="page" @endif>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    @endif {{-- canAccessStock --}}


    {{-- ══════════════════════════════════════
         ADMINISTRATION
    ══════════════════════════════════════ --}}
    @if($isAdmin)

        <div class="mx-3 my-2 border-t border-slate-700/40"></div>

        @php $active = request()->routeIs('users.*'); @endphp
        <a wire:navigate href="{{ route('users.index') }}"
           class="nav-item {{ $active ? 'nav-active' : 'nav-inactive' }}"
           @if($active) aria-current="page" @endif>
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            <span>Utilisateurs</span>
        </a>

    @endif {{-- isAdmin --}}

    @endauth

</nav>

{{-- ─── Styles nav utilitaires ─── --}}
<style>
.nav-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.15s, color 0.15s;
}
.nav-active {
    background-color: #312e81; /* indigo-900 */
    color: #fff;
    border-left: 2px solid #818cf8; /* indigo-400 */
}
.nav-inactive {
    color: #94a3b8; /* slate-400 */
    border-left: 2px solid transparent;
}
.nav-inactive:hover {
    background-color: #1e293b; /* slate-800 */
    color: #e2e8f0; /* slate-200 */
}
.nav-icon {
    width: 1.125rem;
    height: 1.125rem;
    flex-shrink: 0;
}
.nav-sub {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 0.375rem;
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
    color: #94a3b8;
    transition: background-color 0.15s, color 0.15s;
}
.nav-sub:hover {
    background-color: #1e293b;
    color: #e2e8f0;
}
.nav-sub-active {
    background-color: #1e1b4b; /* indigo-950 */
    color: #a5b4fc; /* indigo-300 */
    font-weight: 500;
}
</style>
