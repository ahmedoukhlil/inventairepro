<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Produits en stock</h1>
            <p class="text-gray-500 mt-1">Gestion des consommables et fournitures</p>
        </div>
        @if(auth()->check() && auth()->user()->canManageStock())
            <a href="{{ route('stock.produits.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau produit
            </a>
        @endif
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div wire:loading.delay wire:target="search,filterCategorie,filterMagasin,filterStatut,confirmDelete,delete"
             class="mb-4 flex items-center gap-2 text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Mise à jour en cours…
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Recherche --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recherche</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           wire:loading.attr="disabled"
                           wire:target="search"
                           placeholder="Libellé, descriptif…"
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Catégorie --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Catégorie</label>
                <select wire:model.live="filterCategorie"
                        wire:loading.attr="disabled"
                        wire:target="filterCategorie"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $categorie)
                        <option value="{{ $categorie->id }}">{{ $categorie->libelle }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Magasin --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Magasin</label>
                <select wire:model.live="filterMagasin"
                        wire:loading.attr="disabled"
                        wire:target="filterMagasin"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition">
                    <option value="">Tous les magasins</option>
                    @foreach($magasins as $magasin)
                        <option value="{{ $magasin->id }}">{{ $magasin->magasin }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Statut --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Statut</label>
                <div class="flex gap-1.5 flex-wrap">
                    @foreach([
                        ''          => ['label' => 'Tous',    'bg' => 'bg-gray-100',    'text' => 'text-gray-600',   'active' => 'bg-gray-600 text-white'],
                        'alerte'    => ['label' => 'Alerte',  'bg' => 'bg-red-50',      'text' => 'text-red-600',    'active' => 'bg-red-500 text-white'],
                        'faible'    => ['label' => 'Faible',  'bg' => 'bg-amber-50',    'text' => 'text-amber-600',  'active' => 'bg-amber-400 text-white'],
                        'suffisant' => ['label' => 'OK',      'bg' => 'bg-emerald-50',  'text' => 'text-emerald-600','active' => 'bg-emerald-500 text-white'],
                    ] as $val => $opt)
                        <button wire:click="$set('filterStatut', '{{ $val }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                                {{ $filterStatut === $val ? $opt['active'] : $opt['bg'].' '.$opt['text'] }}">
                            {{ $opt['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Badges filtres actifs --}}
        @if($search || $filterCategorie || $filterMagasin || $filterStatut)
            <div class="flex items-center gap-2 mt-4 flex-wrap">
                <span class="text-xs text-gray-400">Filtres actifs :</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="hover:text-blue-900">&times;</button>
                    </span>
                @endif
                @if($filterCategorie)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-medium">
                        {{ $categories->find($filterCategorie)?->libelle ?? 'Catégorie' }}
                        <button wire:click="$set('filterCategorie', '')" class="hover:text-violet-900">&times;</button>
                    </span>
                @endif
                @if($filterMagasin)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">
                        {{ $magasins->find($filterMagasin)?->magasin ?? 'Magasin' }}
                        <button wire:click="$set('filterMagasin', '')" class="hover:text-indigo-900">&times;</button>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Magasin</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide w-36 hidden sm:table-cell">Niveau</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($produits as $produit)
                        @php
                            $isAlerte = $produit->en_alerte;
                            $isFaible = $produit->stock_faible;
                            $stockColor = $isAlerte ? 'text-red-600' : ($isFaible ? 'text-amber-500' : 'text-emerald-600');
                            $barColor   = $isAlerte ? 'bg-red-400'   : ($isFaible ? 'bg-amber-400'   : 'bg-emerald-400');
                            $pct = $produit->seuil_alerte > 0
                                ? min(round(($produit->stock_actuel / $produit->seuil_alerte) * 100, 1), 100)
                                : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Produit --}}
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate max-w-[200px]">{{ $produit->libelle }}</p>
                                        @if($produit->stockage)
                                            <p class="text-xs text-gray-400 truncate">{{ $produit->stockage }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Catégorie --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-sm text-gray-600">{{ $produit->categorie->libelle ?? '—' }}</span>
                            </td>

                            {{-- Magasin --}}
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-gray-700">{{ $produit->magasin->magasin ?? '—' }}</p>
                                @if($produit->magasin?->localisation)
                                    <p class="text-xs text-gray-400">{{ $produit->magasin->localisation }}</p>
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="text-lg font-bold {{ $stockColor }}">{{ $produit->stock_actuel }}</span>
                                <span class="text-xs text-gray-400"> / {{ $produit->seuil_alerte }}</span>
                            </td>

                            {{-- Barre niveau --}}
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @if($pct !== null)
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-0.5 block">{{ $pct }}%</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($isAlerte)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Alerte</span>
                                @elseif($isFaible)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Faible</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">OK</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('stock.produits.show', $produit->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </a>
                                    @if(auth()->check() && auth()->user()->canManageStock())
                                        <a href="{{ route('stock.produits.edit', $produit->id) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Modifier
                                        </a>
                                        <button wire:click="confirmDelete({{ $produit->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="confirmDelete,delete"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors disabled:opacity-40">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Suppr.
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Aucun produit trouvé</p>
                                    @if($search || $filterCategorie || $filterMagasin || $filterStatut)
                                        <p class="text-xs text-gray-400">Essayez de modifier vos filtres</p>
                                        <button wire:click="$set('search', ''); $set('filterCategorie', ''); $set('filterMagasin', ''); $set('filterStatut', '')"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Effacer tous les filtres
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer pagination --}}
        @if($produits->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-500">
                    {{ $produits->firstItem() }}–{{ $produits->lastItem() }} sur {{ $produits->total() }} produit{{ $produits->total() > 1 ? 's' : '' }}
                </p>
                {{ $produits->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400">{{ $produits->total() }} produit{{ $produits->total() > 1 ? 's' : '' }}</p>
            </div>
        @endif
    </div>

    {{-- Modal suppression --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">Confirmer la suppression</h3>
                        <p class="text-sm text-gray-500 mt-1">Êtes-vous sûr de vouloir supprimer ce produit ?</p>
                        <div class="mt-3 flex items-start gap-2 p-3 bg-red-50 rounded-lg text-xs text-red-700">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <span>Cette action est irréversible et supprimera également tout l'historique des mouvements (entrées et sorties).</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="cancelDelete"
                            wire:loading.attr="disabled"
                            wire:target="delete"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            wire:loading.attr="disabled"
                            wire:target="delete"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 inline-flex items-center gap-2">
                        <svg wire:loading wire:target="delete" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                        </svg>
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
