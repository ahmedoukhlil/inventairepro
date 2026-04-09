<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Stock</h1>
            <p class="text-gray-500 mt-1">Vue d'ensemble de la gestion des consommables &mdash; {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <button wire:click="refresh"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 shadow-sm transition">
            <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Actualiser
        </button>
    </div>

    {{-- Cartes KPI --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        {{-- Total produits --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total produits</p>
                <p class="text-3xl font-bold text-gray-900 mt-0.5">{{ $totalProduits }}</p>
                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                    <span>{{ $totalMagasins }} magasin{{ $totalMagasins > 1 ? 's' : '' }}</span>
                    <span>&bull;</span>
                    <span>{{ $totalCategories }} catégorie{{ $totalCategories > 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>

        {{-- Alertes stock --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl {{ $produitsEnAlerte > 0 ? 'bg-red-50' : 'bg-green-50' }} flex items-center justify-center">
                @if($produitsEnAlerte > 0)
                    <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                @else
                    <svg class="w-7 h-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Alertes stock</p>
                <p class="text-3xl font-bold {{ $produitsEnAlerte > 0 ? 'text-red-600' : 'text-green-600' }} mt-0.5">{{ $produitsEnAlerte }}</p>
                <p class="text-xs mt-1 {{ $produitsEnAlerte > 0 ? 'text-red-500' : 'text-green-500' }}">
                    @if($produitsEnAlerte > 0)
                        + {{ $produitsFaibles }} faibles &bull; {{ $tauxAlerte }}% du stock
                    @else
                        Stock en bonne santé
                    @endif
                </p>
            </div>
        </div>

        {{-- Entrées du mois --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Entrées ce mois</p>
                <p class="text-3xl font-bold text-emerald-600 mt-0.5">{{ number_format($entreesduMois, 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $nbEntreesMois }} bon{{ $nbEntreesMois > 1 ? 's' : '' }} de réception</p>
            </div>
        </div>

        {{-- Sorties du mois --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sorties ce mois</p>
                <p class="text-3xl font-bold text-violet-600 mt-0.5">{{ number_format($sortiesDuMois, 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $nbSortiesMois }} bon{{ $nbSortiesMois > 1 ? 's' : '' }} de sortie</p>
            </div>
        </div>
    </div>

    {{-- Bande de flux mensuel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-8 flex flex-col sm:flex-row sm:items-center gap-6">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-lg {{ $soldeFluxMois >= 0 ? 'bg-emerald-50' : 'bg-red-50' }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $soldeFluxMois >= 0 ? 'text-emerald-600' : 'text-red-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="{{ $soldeFluxMois >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Solde du mois</p>
                <p class="text-xl font-bold {{ $soldeFluxMois >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $soldeFluxMois >= 0 ? '+' : '' }}{{ number_format($soldeFluxMois, 0, ',', ' ') }} unités
                </p>
            </div>
        </div>

        {{-- Répartition santé stock --}}
        @if($totalProduits > 0)
        <div class="flex-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Santé du stock</p>
            <div class="flex h-4 rounded-full overflow-hidden gap-0.5">
                @if($tauxAlerte > 0)
                    <div class="bg-red-400 transition-all" style="width: {{ $tauxAlerte }}%" title="En alerte: {{ $tauxAlerte }}%"></div>
                @endif
                @if($tauxFaible > 0)
                    <div class="bg-amber-400 transition-all" style="width: {{ $tauxFaible }}%" title="Faibles: {{ $tauxFaible }}%"></div>
                @endif
                @if($tauxSuffisant > 0)
                    <div class="bg-emerald-400 transition-all" style="width: {{ $tauxSuffisant }}%" title="Suffisants: {{ $tauxSuffisant }}%"></div>
                @endif
            </div>
            <div class="flex gap-4 mt-2 text-xs text-gray-600">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span> Alerte {{ $tauxAlerte }}%</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Faible {{ $tauxFaible }}%</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> OK {{ $tauxSuffisant }}%</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Produits à surveiller --}}
    @if(!empty($produitsASurveillerDetails))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Produits à surveiller
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">{{ count($produitsASurveillerDetails) }}</span>
            </h3>
            <a href="{{ route('stock.produits.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Voir tous →</a>
        </div>

        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Produit</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Magasin</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide w-40 hidden sm:table-cell">Niveau</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
                        <th class="px-6 py-2 text-right text-xs font-semibold text-gray-400 uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($produitsASurveillerDetails as $produit)
                    @php
                        $isAlerte = $produit['stock_actuel'] <= $produit['seuil_alerte'];
                        $ratio = $produit['ratio'] ?? 0;
                        $barColor = $isAlerte ? 'bg-red-400' : 'bg-amber-400';
                        $barWidth = min($ratio, 150);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $produit['libelle'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $produit['categorie'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 hidden lg:table-cell">{{ $produit['magasin'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold {{ $isAlerte ? 'text-red-600' : 'text-amber-600' }}">{{ $produit['stock_actuel'] }}</span>
                            <span class="text-gray-400 text-xs"> / {{ $produit['seuil_alerte'] }}</span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($produit['ratio'] !== null)
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($barWidth, 100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 mt-0.5 block">{{ $produit['ratio'] }}%</span>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($isAlerte)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Alerte</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Faible</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('stock.produits.show', $produit['id']) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-900">Détails</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Stock par magasin & catégorie --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Stock par magasin --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Magasins
                    @if($magasinsEnAlerte > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $magasinsEnAlerte }} en alerte</span>
                    @endif
                </h3>
                <a href="{{ route('stock.magasins.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Gérer →</a>
            </div>
            <div class="space-y-3">
                @forelse($stockParMagasin as $stock)
                @php
                    $risque = $stock['risque_ratio'];
                    $borderColor = $risque > 0.5 ? 'border-l-red-400' : ($risque > 0 ? 'border-l-amber-400' : 'border-l-emerald-400');
                @endphp
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border-l-4 {{ $borderColor }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $stock['magasin'] }}</p>
                        @if($stock['localisation'])
                            <p class="text-xs text-gray-400 truncate">{{ $stock['localisation'] }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-lg font-bold text-blue-600">{{ $stock['nombre_produits'] }}</p>
                        <p class="text-xs text-gray-400">produits</p>
                        @if($stock['produits_en_alerte'] > 0)
                            <p class="text-xs font-semibold text-red-600 mt-0.5">{{ $stock['produits_en_alerte'] }} en alerte</p>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">Aucun magasin configuré</div>
                @endforelse
            </div>
        </div>

        {{-- Stock par catégorie --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 10V5a2 2 0 012-2z"/>
                    </svg>
                    Catégories
                    @if($categoriesEnAlerte > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $categoriesEnAlerte }} en alerte</span>
                    @endif
                </h3>
                <a href="{{ route('stock.categories.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Gérer →</a>
            </div>
            <div class="space-y-3">
                @forelse($stockParCategorie as $stock)
                @php
                    $risque = $stock['risque_ratio'];
                    $borderColor = $risque > 0.5 ? 'border-l-red-400' : ($risque > 0 ? 'border-l-amber-400' : 'border-l-emerald-400');
                @endphp
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border-l-4 {{ $borderColor }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $stock['categorie'] }}</p>
                        <p class="text-xs text-gray-400">Total stock : {{ number_format($stock['stock_total'], 0, ',', ' ') }} unités</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-lg font-bold text-violet-600">{{ $stock['nombre_produits'] }}</p>
                        <p class="text-xs text-gray-400">produits</p>
                        @if($stock['produits_en_alerte'] > 0)
                            <p class="text-xs font-semibold text-red-600 mt-0.5">{{ $stock['produits_en_alerte'] }} en alerte</p>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">Aucune catégorie configurée</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Derniers mouvements --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Derniers mouvements
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('stock.entrees.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Entrées →</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('stock.sorties.index') }}" class="text-xs text-violet-600 hover:text-violet-800 font-medium">Sorties →</a>
            </div>
        </div>

        <div class="space-y-2">
            @forelse($derniersMovements as $mouvement)
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
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $mouvement['produit'] }}</p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ $isEntree ? 'Fourni par' : 'Demandé par' }} {{ $mouvement['tiers'] }}
                        &bull; {{ $mouvement['createur'] }}
                    </p>
                </div>
                <div class="flex-shrink-0 text-right">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold {{ $isEntree ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700' }}">
                        {{ $isEntree ? '+' : '-' }}{{ $mouvement['quantite'] }}
                    </span>
                    <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y') }}</p>
                </div>
            </div>
            @empty
                <div class="text-center py-10 text-gray-400 text-sm">Aucun mouvement récent</div>
            @endforelse
        </div>
    </div>

    {{-- Actions rapides --}}
    @if(auth()->check() && auth()->user()->canManageStock())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Actions rapides</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('stock.produits.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors text-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm font-medium">Ajouter produit</span>
            </a>
            <a href="{{ route('stock.entrees.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors text-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span class="text-sm font-medium">Nouvelle entrée</span>
            </a>
            <a href="{{ route('stock.sorties.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-700 transition-colors text-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span class="text-sm font-medium">Nouvelle sortie</span>
            </a>
            <a href="{{ route('stock.magasins.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 transition-colors text-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium">Paramètres</span>
            </a>
        </div>
    </div>
    @endif
</div>
