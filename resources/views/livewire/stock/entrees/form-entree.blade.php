<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="flex items-start gap-3 mb-8">
            <a href="{{ route('stock.entrees.index') }}"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Nouvelle entrée de stock</h1>
                <p class="text-gray-500 mt-0.5">Enregistrez un approvisionnement — plusieurs articles possibles</p>
            </div>
        </div>

        {{-- Erreur globale --}}
        @if(session('error'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @error('lignes')
            <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
        @enderror

        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Identification du bon --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Identification du bon
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Date d'entrée <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="date_entree"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition
                                      @error('date_entree') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('date_entree')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Référence --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Référence commande
                        </label>
                        <input type="text" wire:model="reference_commande"
                               placeholder="Ex : BC-2026-001"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition font-mono">
                    </div>

                    {{-- Fournisseur --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Fournisseur <span class="font-normal text-gray-400 normal-case">(optionnel)</span>
                        </label>
                        <livewire:components.searchable-select
                            wire:model.live="fournisseur_id"
                            :options="$this->fournisseurOptions"
                            placeholder="Sélectionner un fournisseur"
                            search-placeholder="Rechercher un fournisseur..."
                            no-results-text="Aucun fournisseur trouvé"
                            :key="'fournisseur-select'"
                        />
                        @error('fournisseur_id')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Observations --}}
                <div class="px-6 pb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Observations</label>
                    <textarea wire:model="observations" rows="2"
                              placeholder="Notes, remarques sur cet approvisionnement…"
                              class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition resize-none"></textarea>
                </div>
            </div>

            {{-- Articles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Articles reçus
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            {{ count($lignes) }}
                        </span>
                    </h2>
                    <button type="button" wire:click="ajouterLigne"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un article
                    </button>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($lignes as $index => $ligne)
                        <div class="p-5 flex gap-4 items-start" wire:key="ligne-{{ $index }}">

                            {{-- Numéro ligne --}}
                            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold mt-1">
                                {{ $index + 1 }}
                            </div>

                            {{-- Produit --}}
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Produit <span class="text-red-500">*</span>
                                </label>
                                <livewire:components.searchable-select
                                    wire:model.live="lignes.{{ $index }}.produit_id"
                                    :options="$this->produitOptions"
                                    placeholder="Sélectionner un produit"
                                    search-placeholder="Rechercher…"
                                    no-results-text="Aucun produit trouvé"
                                    :key="'produit-' . $index"
                                />
                                @error("lignes.$index.produit_id")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @if(!empty($ligne['produit_libelle']))
                                    <p class="mt-1 text-xs text-gray-400">
                                        Stock actuel : <span class="font-semibold text-gray-600">{{ $ligne['stock_actuel'] }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Quantité --}}
                            <div class="flex-shrink-0 w-32">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Qté reçue <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="lignes.{{ $index }}.quantite" min="1"
                                       class="w-full px-3 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition text-center font-semibold
                                              @error("lignes.$index.quantite") border-red-400 bg-red-50 @else border-gray-200 @enderror">
                                @error("lignes.$index.quantite")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Supprimer ligne --}}
                            <div class="flex-shrink-0 mt-6">
                                @if(count($lignes) > 1)
                                    <button type="button" wire:click="supprimerLigne({{ $index }})"
                                            title="Supprimer cette ligne"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <div class="w-8 h-8"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Récap total --}}
                @if(count($lignes) > 1)
                    <div class="px-6 py-3 border-t border-gray-50 bg-emerald-50 flex items-center justify-between rounded-b-xl">
                        <span class="text-xs text-emerald-700 font-medium">{{ count($lignes) }} articles</span>
                        <span class="text-xs text-emerald-700 font-semibold">
                            Total : {{ array_sum(array_column($lignes, 'quantite')) }} unités
                        </span>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer l'entrée
                </button>
            </div>

        </form>
    </div>
</div>