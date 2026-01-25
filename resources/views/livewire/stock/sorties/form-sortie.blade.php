<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle sortie de stock</h1>
            <p class="text-gray-500 mt-1">Enregistrez une distribution de produit</p>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 space-y-6">
                
                <div>
                    <label for="date_sortie" class="block text-sm font-medium text-gray-700 mb-1">
                        Date de sortie <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date_sortie" wire:model="date_sortie" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('date_sortie') border-red-500 @enderror">
                    @error('date_sortie') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Produit <span class="text-red-500">*</span>
                    </label>
                    <livewire:components.searchable-select
                        wire:model.live="produit_id"
                        :options="$this->produitOptions"
                        placeholder="Sélectionner un produit"
                        search-placeholder="Rechercher un produit..."
                        no-results-text="Aucun produit trouvé"
                        :key="'produit-select'"
                    />
                    @error('produit_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Affichage du stock disponible -->
                @if($produitSelectionne)
                    <div class="@if($produitSelectionne->en_alerte) bg-red-50 border-2 border-red-300 @elseif($produitSelectionne->stock_faible) bg-yellow-50 border-2 border-yellow-300 @else bg-indigo-50 border-2 border-indigo-300 @endif rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Stock disponible</p>
                                <p class="text-3xl font-bold @if($produitSelectionne->en_alerte) text-red-700 @elseif($produitSelectionne->stock_faible) text-yellow-700 @else text-indigo-700 @endif mt-1">
                                    {{ number_format($stockDisponible, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-700 mt-1">
                                    Seuil d'alerte : <span class="font-semibold">{{ $produitSelectionne->seuil_alerte }}</span>
                                    @if($produitSelectionne->en_alerte)
                                        <span class="text-red-700 font-bold ml-2">⚠️ ALERTE ACTIVE</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-4xl">
                                @if($produitSelectionne->en_alerte) 🔴
                                @elseif($produitSelectionne->stock_faible) 🟡
                                @else 🟢
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Demandeur <span class="text-red-500">*</span>
                    </label>
                    <livewire:components.searchable-select
                        wire:model.live="demandeur_id"
                        :options="$this->demandeurOptions"
                        placeholder="Sélectionner un demandeur"
                        search-placeholder="Rechercher un demandeur..."
                        no-results-text="Aucun demandeur trouvé"
                        :key="'demandeur-select'"
                    />
                    @error('demandeur_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantité <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantite" wire:model="quantite" min="1" @if($stockDisponible > 0) max="{{ $stockDisponible }}" @endif
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('quantite') border-red-500 @enderror">
                    @error('quantite') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    @if($stockDisponible > 0)
                        <p class="mt-1 text-sm text-gray-600 font-medium">Maximum disponible : <span class="text-indigo-600 font-bold">{{ number_format($stockDisponible, 0, ',', ' ') }}</span></p>
                    @elseif($produitSelectionne)
                        <p class="mt-1 text-sm text-red-700 font-bold">⚠️ Stock épuisé - Impossible de créer une sortie</p>
                    @endif
                </div>

                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea id="observations" wire:model="observations" rows="3"
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="Notes..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors font-medium">Annuler</button>
                    @if(!$produitSelectionne || $stockDisponible <= 0)
                        <button type="submit" 
                                disabled
                                style="background-color: #9ca3af !important; color: #ffffff !important; border: 2px solid #6b7280 !important;"
                                class="px-4 py-2 rounded-lg inline-flex items-center font-medium shadow-md cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: #ffffff !important;">Enregistrer la sortie</span>
                        </button>
                    @else
                        <button type="submit" 
                                style="background-color: #4f46e5 !important; color: #ffffff !important; border: 2px solid #4338ca !important;"
                                class="px-4 py-2 rounded-lg inline-flex items-center font-medium shadow-md transition-all duration-200 focus:outline-none"
                                onmouseover="this.style.backgroundColor='#4338ca'"
                                onmouseout="this.style.backgroundColor='#4f46e5'"
                                onclick="this.style.backgroundColor='#3730a3'">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: #ffffff !important; font-weight: 600;">Enregistrer la sortie</span>
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
