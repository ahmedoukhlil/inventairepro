<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="flex items-start gap-3 mb-8">
            <a href="{{ route('stock.sorties.index') }}"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Nouvelle sortie de stock</h1>
                <p class="text-gray-500 mt-0.5">Enregistrez une distribution pour un demandeur</p>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('error'))
            <div class="mb-6 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @error('lignes')
            <div class="mb-6 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
                <span>{{ $message }}</span>
            </div>
        @enderror

        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Date + Demandeur + Observations --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations générales
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Date --}}
                    <div>
                        <label for="date_sortie" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_sortie" wire:model="date_sortie"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition
                                      @error('date_sortie') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('date_sortie')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Demandeur --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Demandeur (service) <span class="text-red-500">*</span>
                        </label>
                        <livewire:components.searchable-select
                            wire:model.live="demandeur_id"
                            :options="$this->demandeurOptions"
                            placeholder="Sélectionner un demandeur"
                            search-placeholder="Rechercher un demandeur..."
                            no-results-text="Aucun demandeur trouvé"
                            :key="'demandeur-select'"
                        />
                        @error('demandeur_id')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div class="md:col-span-3">
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Observations
                        </label>
                        <textarea id="observations" wire:model="observations" rows="2"
                                  placeholder="Notes, motif de la distribution…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Lignes d'articles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Articles à distribuer
                        <span class="ml-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-violet-700 bg-violet-100 rounded-full">
                            {{ count($lignes) }}
                        </span>
                    </h2>
                    <button type="button" wire:click="ajouterLigne"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-violet-700 bg-violet-50 hover:bg-violet-100 border border-violet-200 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un article
                    </button>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($lignes as $index => $ligne)
                        <div class="p-6" wire:key="ligne-{{ $index }}">
                            <div class="flex items-start gap-4">

                                {{-- Numéro de ligne --}}
                                <div class="flex-shrink-0 w-7 h-7 rounded-full bg-violet-100 text-violet-700 text-xs font-bold flex items-center justify-center mt-1">
                                    {{ $index + 1 }}
                                </div>

                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">

                                    {{-- Produit --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                            Produit <span class="text-red-500">*</span>
                                        </label>
                                        <livewire:components.searchable-select
                                            wire:model.live="lignes.{{ $index }}.produit_id"
                                            :options="$this->produitOptions"
                                            placeholder="Sélectionner un produit"
                                            search-placeholder="Rechercher..."
                                            no-results-text="Aucun produit trouvé"
                                            :key="'produit-select-' . $index"
                                        />
                                        @error("lignes.$index.produit_id")
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Quantité --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                            Quantité <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               wire:model.live="lignes.{{ $index }}.quantite"
                                               min="1"
                                               @if($ligne['stock_disponible'] > 0) max="{{ $ligne['stock_disponible'] }}" @endif
                                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition
                                                      @error("lignes.$index.quantite") border-red-400 bg-red-50 @else border-gray-200 @enderror">
                                        @error("lignes.$index.quantite")
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @else
                                            @if($ligne['stock_disponible'] > 0)
                                                <p class="mt-1 text-xs text-gray-400">
                                                    Dispo : <span class="font-semibold text-violet-600">{{ number_format($ligne['stock_disponible'], 0, ',', ' ') }}</span>
                                                </p>
                                            @elseif($ligne['produit_id'])
                                                <p class="mt-1 text-xs text-red-500 font-semibold">Stock épuisé</p>
                                            @endif
                                        @enderror
                                    </div>
                                </div>

                                {{-- Bouton supprimer --}}
                                @if(count($lignes) > 1)
                                    <button type="button" wire:click="supprimerLigne({{ $index }})"
                                            class="flex-shrink-0 mt-6 w-7 h-7 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <div class="flex-shrink-0 w-7 mt-6"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pied : bouton ajouter (répété en bas si plusieurs lignes) --}}
                @if(count($lignes) > 1)
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                        <button type="button" wire:click="ajouterLigne"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-violet-600 hover:text-violet-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un autre article
                        </button>
                    </div>
                @endif
            </div>

            {{-- Récapitulatif --}}
            @if(count($lignes) > 1)
                <div class="bg-violet-50 border border-violet-100 rounded-xl px-6 py-4 text-sm text-violet-800">
                    <span class="font-semibold">{{ count($lignes) }} articles</span> seront enregistrés pour ce demandeur.
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save,ajouterLigne,supprimerLigne"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer la sortie
                </button>
            </div>

        </form>
    </div>
</div>