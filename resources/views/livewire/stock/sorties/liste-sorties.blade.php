<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sorties de stock</h1>
            <p class="text-gray-500 mt-1">Historique des distributions</p>
        </div>
        <a href="{{ route('stock.sorties.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle sortie
        </a>
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

    {{-- KPI total --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center gap-5">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-violet-50 flex items-center justify-center">
            <svg class="w-7 h-7 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total distribué sur la période</p>
            <p class="text-3xl font-bold text-violet-600 mt-0.5">{{ number_format($totalQuantite, 0, ',', ' ') }} unités</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div wire:loading.delay wire:target="search,filterProduit,filterDemandeur,dateDebut,dateFin"
             class="mb-4 flex items-center gap-2 text-xs text-violet-700 bg-violet-50 border border-violet-100 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Mise à jour en cours…
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Recherche --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recherche</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           wire:loading.attr="disabled"
                           wire:target="search"
                           placeholder="Produit, demandeur…"
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Produit --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Produit</label>
                <select wire:model.live="filterProduit" wire:loading.attr="disabled" wire:target="filterProduit"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
                    <option value="">Tous les produits</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}">{{ $produit->libelle }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Demandeur --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Demandeur</label>
                <select wire:model.live="filterDemandeur" wire:loading.attr="disabled" wire:target="filterDemandeur"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
                    <option value="">Tous les demandeurs</option>
                    @foreach($demandeurs as $demandeur)
                        <option value="{{ $demandeur->id }}">{{ $demandeur->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date début --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date début</label>
                <input type="date" wire:model.live="dateDebut" wire:loading.attr="disabled" wire:target="dateDebut"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
            </div>

            {{-- Date fin --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date fin</label>
                <input type="date" wire:model.live="dateFin" wire:loading.attr="disabled" wire:target="dateFin"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
            </div>
        </div>

        {{-- Badges filtres actifs --}}
        @if($search || $filterProduit || $filterDemandeur)
            <div class="flex items-center gap-2 mt-4 flex-wrap">
                <span class="text-xs text-gray-400">Filtres actifs :</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="hover:text-blue-900">&times;</button>
                    </span>
                @endif
                @if($filterProduit)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-medium">
                        {{ $produits->find($filterProduit)?->libelle ?? 'Produit' }}
                        <button wire:click="$set('filterProduit', '')" class="hover:text-violet-900">&times;</button>
                    </span>
                @endif
                @if($filterDemandeur)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">
                        {{ $demandeurs->find($filterDemandeur)?->nom ?? 'Demandeur' }}
                        <button wire:click="$set('filterDemandeur', '')" class="hover:text-indigo-900">&times;</button>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Liste des commandes --}}
    <div class="space-y-3">
        @forelse($groupeIds as $groupe)
            @php
                $lignes = $toutesLesSorties[$groupe->groupe_key] ?? collect();
                $premiere = $lignes->first();
                $totalQte = $lignes->sum('quantite');
                $nbArticles = $lignes->count();
                $isGroupe = str_contains($groupe->groupe_key, '-');
            @endphp

            @if($premiere)
            <div x-data="{ open: false }"
                 class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- En-tête commande (cliquable) --}}
                <div class="px-5 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition-colors"
                     @click="open = !open">

                    {{-- Chevron --}}
                    <div class="flex-shrink-0 text-gray-400 transition-transform duration-200"
                         :class="open ? 'rotate-90' : ''">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    {{-- N° commande + Date --}}
                    <div class="flex-shrink-0 w-36">
                        @if($premiere->numero_commande)
                            <p class="text-xs font-bold text-violet-600 font-mono">N°{{ $premiere->numero_commande }}</p>
                        @endif
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($groupe->max_date)->format('d/m/Y') }}
                        </p>
                    </div>

                    {{-- Demandeur --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $premiere->demandeur->nom ?? '—' }}
                        </p>
                        @if($premiere->demandeur?->poste_service)
                            <p class="text-xs text-gray-400 truncate">{{ $premiere->demandeur->poste_service }}</p>
                        @endif
                    </div>

                    {{-- Nb articles --}}
                    <div class="flex-shrink-0 hidden sm:block text-center w-20">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $nbArticles }} article{{ $nbArticles > 1 ? 's' : '' }}
                        </span>
                    </div>

                    {{-- Total quantité --}}
                    <div class="flex-shrink-0 w-20 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-violet-100 text-violet-700">
                            -{{ $totalQte }}
                        </span>
                    </div>

                    {{-- Créateur --}}
                    <div class="flex-shrink-0 hidden lg:block w-32 text-right">
                        <p class="text-xs text-gray-500 truncate">{{ $premiere->nom_createur }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex items-center gap-2" @click.stop>
                        {{-- Bon de sortie --}}
                        @if($isGroupe)
                            <a href="{{ route('stock.sorties.bon.groupe', $groupe->groupe_key) }}" target="_blank"
                               title="Imprimer le bon de sortie"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Bon
                            </a>
                        @else
                            <a href="{{ route('stock.sorties.bon', $premiere->id) }}" target="_blank"
                               title="Imprimer le bon de sortie"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Bon
                            </a>
                        @endif

                        {{-- Supprimer commande --}}
                        @if(auth()->user()->canDeleteStockOperations())
                            @if($isGroupe)
                                <button
                                    wire:click="supprimerCommande('{{ $groupe->groupe_key }}')"
                                    wire:confirm="Supprimer toute cette commande ({{ $nbArticles }} article{{ $nbArticles > 1 ? 's' : '' }}) ? Les stocks seront rétablis automatiquement."
                                    wire:loading.attr="disabled"
                                    title="Supprimer toute la commande"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @else
                                <button
                                    wire:click="supprimerSortie({{ $premiere->id }})"
                                    wire:confirm="Supprimer cette sortie ? Le stock sera rétabli automatiquement."
                                    wire:loading.attr="disabled"
                                    title="Supprimer cette sortie"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Articles (expandable) --}}
                <div x-show="open" x-transition class="border-t border-gray-100">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide w-8"></th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Produit</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Quantité</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Observations</th>
                                @if(auth()->user()->canDeleteStockOperations() && $isGroupe)
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($lignes as $ligne)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-2.5 text-gray-300">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-sm font-medium text-gray-800">{{ $ligne->produit->libelle ?? '—' }}</p>
                                    </td>
                                    <td class="px-4 py-2.5 hidden md:table-cell">
                                        <p class="text-xs text-gray-500">{{ $ligne->produit?->categorie?->libelle ?? '—' }}</p>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-bold bg-violet-50 text-violet-600">
                                            -{{ $ligne->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 hidden lg:table-cell">
                                        <p class="text-xs text-gray-400 truncate max-w-[200px]">
                                            {{ $ligne->observations ? \Illuminate\Support\Str::limit($ligne->observations, 50) : '—' }}
                                        </p>
                                    </td>
                                    @if(auth()->user()->canDeleteStockOperations() && $isGroupe)
                                        <td class="px-4 py-2.5 text-center">
                                            <button
                                                wire:click="supprimerSortie({{ $ligne->id }})"
                                                wire:confirm="Supprimer cet article de la commande ? Le stock sera rétabli."
                                                wire:loading.attr="disabled"
                                                title="Supprimer cet article"
                                                class="inline-flex items-center px-1.5 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        @if($nbArticles > 1)
                        <tfoot>
                            <tr class="bg-violet-50">
                                <td class="px-6 py-2"></td>
                                <td class="px-4 py-2 text-xs font-semibold text-violet-700" colspan="2">Total</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-sm font-bold text-violet-700">-{{ $totalQte }}</span>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell"></td>
                                @if(auth()->user()->canDeleteStockOperations() && $isGroupe)
                                    <td class="px-4 py-2"></td>
                                @endif
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Aucune sortie trouvée</p>
                    @if($search || $filterProduit || $filterDemandeur)
                        <button wire:click="$set('search', ''); $set('filterProduit', ''); $set('filterDemandeur', '')"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Effacer les filtres
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($groupeIds->hasPages())
        <div class="mt-4 px-1 flex items-center justify-between gap-4">
            <p class="text-xs text-gray-500">
                {{ $groupeIds->firstItem() }}–{{ $groupeIds->lastItem() }} sur {{ $groupeIds->total() }} commande{{ $groupeIds->total() > 1 ? 's' : '' }}
            </p>
            {{ $groupeIds->links() }}
        </div>
    @else
        <div class="mt-3 px-1">
            <p class="text-xs text-gray-400">{{ $groupeIds->total() }} commande{{ $groupeIds->total() > 1 ? 's' : '' }}</p>
        </div>
    @endif
</div>
