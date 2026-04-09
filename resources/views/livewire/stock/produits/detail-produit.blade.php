<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-8 gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('stock.produits.index') }}"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $produit->libelle }}</h1>
                    @if($produit->en_alerte)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Alerte stock</span>
                    @elseif($produit->stock_faible)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Stock faible</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Stock OK</span>
                    @endif
                </div>
                <p class="text-gray-500 mt-0.5">
                    {{ $produit->categorie->libelle ?? 'Sans catégorie' }}
                    @if($produit->magasin)
                        &bull; {{ $produit->magasin->magasin }}
                        @if($produit->magasin->localisation)
                            <span class="text-gray-400">({{ $produit->magasin->localisation }})</span>
                        @endif
                    @endif
                </p>
            </div>
        </div>
        @if(auth()->check() && auth()->user()->canManageStock())
            <a href="{{ route('stock.produits.edit', $produit->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        @endif
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        {{-- Stock actuel --}}
        @php
            $stockColor = $produit->en_alerte ? 'text-red-600' : ($produit->stock_faible ? 'text-amber-500' : 'text-emerald-600');
            $stockBg    = $produit->en_alerte ? 'bg-red-50'   : ($produit->stock_faible ? 'bg-amber-50'   : 'bg-emerald-50');
            $stockIcon  = $produit->en_alerte ? 'text-red-500' : ($produit->stock_faible ? 'text-amber-500' : 'text-emerald-500');
            $pct = $produit->seuil_alerte > 0 ? round(($produit->stock_actuel / $produit->seuil_alerte) * 100, 1) : null;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl {{ $stockBg }} flex items-center justify-center">
                <svg class="w-7 h-7 {{ $stockIcon }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock actuel</p>
                <p class="text-3xl font-bold {{ $stockColor }} mt-0.5">{{ number_format($produit->stock_actuel, 0, ',', ' ') }}</p>
                @if($pct !== null)
                    <div class="mt-1.5 w-full bg-gray-100 rounded-full h-1.5">
                        <div class="{{ $produit->en_alerte ? 'bg-red-400' : ($produit->stock_faible ? 'bg-amber-400' : 'bg-emerald-400') }} h-1.5 rounded-full"
                             style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $pct }}% du seuil</p>
                @endif
            </div>
        </div>

        {{-- Seuil d'alerte --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Seuil d'alerte</p>
                <p class="text-3xl font-bold text-gray-700 mt-0.5">{{ $produit->seuil_alerte }}</p>
                <p class="text-xs mt-1 {{ $produit->en_alerte ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                    {{ $produit->en_alerte ? 'Alerte active' : 'Seuil non atteint' }}
                </p>
            </div>
        </div>

        {{-- Total entrées --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total entrées</p>
                <p class="text-3xl font-bold text-emerald-600 mt-0.5">{{ number_format($produit->total_entrees, 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $produit->entrees()->count() }} bon{{ $produit->entrees()->count() > 1 ? 's' : '' }} de réception</p>
            </div>
        </div>

        {{-- Total sorties --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total sorties</p>
                <p class="text-3xl font-bold text-violet-600 mt-0.5">{{ number_format($produit->total_sorties, 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $produit->sorties()->count() }} bon{{ $produit->sorties()->count() > 1 ? 's' : '' }} de sortie</p>
            </div>
        </div>
    </div>

    {{-- Informations produit --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Informations
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock initial</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ number_format($produit->stock_initial, 0, ',', ' ') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock actuel</p>
                <p class="text-2xl font-bold {{ $stockColor }} mt-1">{{ number_format($produit->stock_actuel, 0, ',', ' ') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Seuil alerte</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ $produit->seuil_alerte }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">% du seuil</p>
                <p class="text-2xl font-bold {{ $stockColor }} mt-1">{{ $pct !== null ? $pct : '—' }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Magasin</p>
                    <p class="text-gray-800 font-medium mt-0.5">{{ $produit->magasin->magasin ?? '—' }}</p>
                    @if($produit->magasin?->localisation)
                        <p class="text-xs text-gray-400">{{ $produit->magasin->localisation }}</p>
                    @endif
                </div>
            </div>
            @if($produit->stockage)
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Emplacement</p>
                    <p class="text-gray-800 font-medium mt-0.5">{{ $produit->stockage }}</p>
                </div>
            </div>
            @endif
            @if($produit->descriptif)
            <div class="flex items-start gap-3 md:col-span-2">
                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Descriptif</p>
                    <p class="text-gray-700 mt-0.5 leading-relaxed">{{ $produit->descriptif }}</p>
                </div>
            </div>
            @endif
            @if($produit->observations)
            <div class="flex items-start gap-3 md:col-span-2">
                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Observations</p>
                    <p class="text-gray-700 mt-0.5 leading-relaxed">{{ $produit->observations }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Onglets mouvements --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Navigation onglets --}}
        <div class="border-b border-gray-100 px-2">
            <nav class="flex gap-1 -mb-px">
                @php
                    $tabs = [
                        'info'       => ['label' => 'Résumé',    'count' => null, 'color' => 'blue'],
                        'entrees'    => ['label' => 'Entrées',   'count' => $produit->entrees()->count(), 'color' => 'emerald'],
                        'sorties'    => ['label' => 'Sorties',   'count' => $produit->sorties()->count(), 'color' => 'violet'],
                        'historique' => ['label' => 'Historique','count' => null, 'color' => 'gray'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    <button wire:click="setOnglet('{{ $key }}')"
                        class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors
                            {{ $onglet === $key
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                        {{ $tab['label'] }}
                        @if($tab['count'] !== null)
                            <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                {{ $onglet === $key ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">

            {{-- Onglet Résumé --}}
            @if($onglet === 'info')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 font-medium">Niveau de stock</span>
                                @if($pct !== null)
                                    <span class="{{ $stockColor }} font-semibold">{{ $pct }}%</span>
                                @endif
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3">
                                @if($pct !== null)
                                <div class="{{ $produit->en_alerte ? 'bg-red-400' : ($produit->stock_faible ? 'bg-amber-400' : 'bg-emerald-400') }} h-3 rounded-full transition-all"
                                     style="width: {{ min($pct, 100) }}%"></div>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0</span>
                                <span>Seuil : {{ $produit->seuil_alerte }}</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Flux cumulés</p>
                            <div class="flex gap-8">
                                <div>
                                    <p class="text-sm text-gray-500">Entrées totales</p>
                                    <p class="text-xl font-bold text-emerald-600">+{{ number_format($produit->total_entrees, 0, ',', ' ') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Sorties totales</p>
                                    <p class="text-xl font-bold text-violet-600">-{{ number_format($produit->total_sorties, 0, ',', ' ') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Solde</p>
                                    @php $solde = $produit->total_entrees - $produit->total_sorties; @endphp
                                    <p class="text-xl font-bold {{ $solde >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde, 0, ',', ' ') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if(auth()->check() && auth()->user()->canManageStock())
                        <a href="{{ route('stock.entrees.create', ['produit_id' => $produit->id]) }}"
                           class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span class="text-sm font-medium">Enregistrer une entrée</span>
                        </a>
                        <a href="{{ route('stock.sorties.create', ['produit_id' => $produit->id]) }}"
                           class="flex items-center gap-3 p-3 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-700 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="text-sm font-medium">Enregistrer une sortie</span>
                        </a>
                        @endif
                        <button wire:click="setOnglet('historique')"
                            class="w-full flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Voir l'historique complet</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Onglet Entrées --}}
            @if($onglet === 'entrees')
                <div class="overflow-x-auto -mx-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Fournisseur</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Référence</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Quantité</th>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($entrees as $entree)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 text-sm text-gray-700">{{ $entree->date_entree->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $entree->fournisseur->libelle ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $entree->reference_commande ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700">
                                            +{{ $entree->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500 hidden sm:table-cell">{{ $entree->nom_createur }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Aucune entrée enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Onglet Sorties --}}
            @if($onglet === 'sorties')
                <div class="overflow-x-auto -mx-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Demandeur</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Quantité</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Par</th>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Observations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($sorties as $sortie)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 text-sm text-gray-700">{{ $sortie->date_sortie->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-800">{{ $sortie->demandeur->nom ?? '—' }}</p>
                                        @if($sortie->demandeur?->poste_service)
                                            <p class="text-xs text-gray-400">{{ $sortie->demandeur->poste_service }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-violet-100 text-violet-700">
                                            -{{ $sortie->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 hidden sm:table-cell">{{ $sortie->nom_createur }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $sortie->observations ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Aucune sortie enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Onglet Historique complet --}}
            @if($onglet === 'historique')
                <div class="space-y-2">
                    @forelse($historique as $mouvement)
                    @php $isEntree = $mouvement['type'] === 'entree'; @endphp
                    <div class="flex items-center gap-4 px-4 py-3 {{ $isEntree ? 'bg-emerald-50' : 'bg-violet-50' }} rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $isEntree ? 'bg-emerald-100' : 'bg-violet-100' }} flex items-center justify-center">
                            @if($isEntree)
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold {{ $isEntree ? 'text-emerald-700' : 'text-violet-700' }} uppercase">
                                    {{ $isEntree ? 'Entrée' : 'Sortie' }}
                                </span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 truncate">
                                {{ $isEntree ? 'Fourni par' : 'Demandé par' }} <span class="font-medium">{{ $mouvement['tiers'] }}</span>
                                &bull; {{ $mouvement['createur'] }}
                            </p>
                            @if(isset($mouvement['observations']) && $mouvement['observations'])
                                <p class="text-xs text-gray-400 truncate">{{ $mouvement['observations'] }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold
                                {{ $isEntree ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700' }}">
                                {{ $isEntree ? '+' : '-' }}{{ $mouvement['quantite'] }}
                            </span>
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 text-sm">Aucun mouvement enregistré</div>
                    @endforelse
                </div>
            @endif

        </div>
    </div>
</div>
