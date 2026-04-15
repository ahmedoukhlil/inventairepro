<div
    x-data="{
        confirm: {
            show: false,
            titre: '',
            message: '',
            action: null,
        },
        demanderConfirmation(titre, message, action) {
            this.confirm.titre   = titre;
            this.confirm.message = message;
            this.confirm.action  = action;
            this.confirm.show    = true;
        },
        valider() {
            if (this.confirm.action) this.confirm.action();
            this.confirm.show = false;
        },
        annuler() {
            this.confirm.show = false;
        }
    }"
>

    {{-- Modale de confirmation --}}
    <div
        x-show="confirm.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none"
    >
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="annuler()"></div>
        <div
            x-show="confirm.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10"
        >
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900" x-text="confirm.titre"></h3>
                    <p class="text-sm text-gray-500 mt-0.5" x-text="confirm.message"></p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button @click="annuler()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button @click="valider()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Entrées de stock</h1>
            <p class="text-gray-500 mt-1">Historique des approvisionnements</p>
        </div>
        <a href="{{ route('stock.entrees.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle entrée
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
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total reçu sur la période</p>
            <p class="text-3xl font-bold text-emerald-600 mt-0.5">{{ number_format($totalQuantite, 0, ',', ' ') }} unités</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div wire:loading.delay wire:target="search,filterProduit,filterFournisseur,dateDebut,dateFin"
             class="mb-4 flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Mise à jour en cours…
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recherche</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search"
                           placeholder="Produit, référence…"
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Produit</label>
                <select wire:model.live="filterProduit" wire:loading.attr="disabled" wire:target="filterProduit"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <option value="">Tous les produits</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}">{{ $produit->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Fournisseur</label>
                <select wire:model.live="filterFournisseur" wire:loading.attr="disabled" wire:target="filterFournisseur"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <option value="">Tous les fournisseurs</option>
                    @foreach($fournisseurs as $fournisseur)
                        <option value="{{ $fournisseur->id }}">{{ $fournisseur->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date début</label>
                <input type="date" wire:model.live="dateDebut" wire:loading.attr="disabled" wire:target="dateDebut"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date fin</label>
                <input type="date" wire:model.live="dateFin" wire:loading.attr="disabled" wire:target="dateFin"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        @if($search || $filterProduit || $filterFournisseur)
            <div class="flex items-center gap-2 mt-4 flex-wrap">
                <span class="text-xs text-gray-400">Filtres actifs :</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="hover:text-blue-900">&times;</button>
                    </span>
                @endif
                @if($filterProduit)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                        {{ $produits->find($filterProduit)?->libelle ?? 'Produit' }}
                        <button wire:click="$set('filterProduit', '')" class="hover:text-emerald-900">&times;</button>
                    </span>
                @endif
                @if($filterFournisseur)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-teal-100 text-teal-700 text-xs font-medium">
                        {{ $fournisseurs->find($filterFournisseur)?->libelle ?? 'Fournisseur' }}
                        <button wire:click="$set('filterFournisseur', '')" class="hover:text-teal-900">&times;</button>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Liste groupée --}}
    <div class="space-y-3">
        @forelse($groupeIds as $groupe)
            @php
                $lignes = $toutesLesEntrees[$groupe->groupe_key] ?? collect();
                $premiere = $lignes->first();
                $totalQte = $lignes->sum('quantite');
                $nbArticles = $lignes->count();
                $isGroupe = str_contains($groupe->groupe_key, '-');
            @endphp

            @if($premiere)
            <div x-data="{ open: false }"
                 class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-5 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition-colors"
                     @click="open = !open">

                    <div class="flex-shrink-0 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-90' : ''">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    <div class="flex-shrink-0 w-36">
                        @if($premiere->numero_entree)
                            <p class="text-xs font-bold text-emerald-600 font-mono">{{ $premiere->numero_entree }}</p>
                        @endif
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($groupe->max_date)->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $premiere->fournisseur->libelle ?? '—' }}
                        </p>
                        @if($premiere->reference_commande)
                            <p class="text-xs text-gray-400 font-mono truncate">Réf : {{ $premiere->reference_commande }}</p>
                        @endif
                    </div>

                    <div class="flex-shrink-0 hidden sm:block text-center w-20">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $nbArticles }} article{{ $nbArticles > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="flex-shrink-0 w-20 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700">
                            +{{ $totalQte }}
                        </span>
                    </div>

                    <div class="flex-shrink-0 hidden lg:block w-32 text-right">
                        <p class="text-xs text-gray-500 truncate">{{ $premiere->nom_createur }}</p>
                    </div>

                    <div class="flex-shrink-0 flex items-center gap-2" @click.stop>
                        @if(auth()->user()->canDeleteStockOperations())
                            @if($isGroupe)
                                <button
                                    @click="demanderConfirmation(
                                        'Supprimer l\'entrée',
                                        'Cette entrée contient {{ $nbArticles }} article{{ $nbArticles > 1 ? 's' : '' }}. Les stocks seront ajustés automatiquement.',
                                        () => $wire.supprimerGroupe('{{ $groupe->groupe_key }}')
                                    )"
                                    title="Supprimer toute l'entrée"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @else
                                <button
                                    @click="demanderConfirmation(
                                        'Supprimer l\'entrée',
                                        'Le stock sera ajusté automatiquement.',
                                        () => $wire.supprimerEntree({{ $premiere->id }})
                                    )"
                                    title="Supprimer cette entrée"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                <div x-show="open" x-transition class="border-t border-gray-100">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-2 w-8"></th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Produit</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Quantité</th>
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-bold bg-emerald-50 text-emerald-600">
                                            +{{ $ligne->quantite }}
                                        </span>
                                    </td>
                                    @if(auth()->user()->canDeleteStockOperations() && $isGroupe)
                                        <td class="px-4 py-2.5 text-center">
                                            <button
                                                @click="demanderConfirmation(
                                                    'Supprimer l\'article',
                                                    '{{ addslashes($ligne->produit->libelle ?? 'cet article') }} sera retiré de l\'entrée et le stock ajusté.',
                                                    () => $wire.supprimerEntree({{ $ligne->id }})
                                                )"
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
                            <tr class="bg-emerald-50">
                                <td class="px-6 py-2"></td>
                                <td class="px-4 py-2 text-xs font-semibold text-emerald-700" colspan="2">Total</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-sm font-bold text-emerald-700">+{{ $totalQte }}</span>
                                </td>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Aucune entrée trouvée</p>
                    @if($search || $filterProduit || $filterFournisseur)
                        <button wire:click="$set('search', ''); $set('filterProduit', ''); $set('filterFournisseur', '')"
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
                {{ $groupeIds->firstItem() }}–{{ $groupeIds->lastItem() }} sur {{ $groupeIds->total() }} entrée{{ $groupeIds->total() > 1 ? 's' : '' }}
            </p>
            {{ $groupeIds->links() }}
        </div>
    @else
        <div class="mt-3 px-1">
            <p class="text-xs text-gray-400">{{ $groupeIds->total() }} entrée{{ $groupeIds->total() > 1 ? 's' : '' }}</p>
        </div>
    @endif
</div>