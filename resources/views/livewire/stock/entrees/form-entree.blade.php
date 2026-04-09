<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

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
                <p class="text-gray-500 mt-0.5">Enregistrez un approvisionnement</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Identification --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Identification du bon
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Date --}}
                    <div>
                        <label for="date_entree" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Date d'entrée <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_entree" wire:model="date_entree"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition
                                      @error('date_entree') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('date_entree')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Référence --}}
                    <div>
                        <label for="reference_commande" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Référence commande
                        </label>
                        <input type="text" id="reference_commande" wire:model="reference_commande"
                               placeholder="Ex : BC-2026-001"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition font-mono">
                    </div>
                </div>
            </div>

            {{-- Produit --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Produit approvisionné
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
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
                        @error('produit_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Fiche produit sélectionné --}}
                    @if($this->produitSelectionne)
                        @php
                            $p = $this->produitSelectionne;
                            $isAlerte = $p->en_alerte;
                            $isFaible = $p->stock_faible;
                            $stockColor = $isAlerte ? 'text-red-600' : ($isFaible ? 'text-amber-500' : 'text-emerald-600');
                            $barColor   = $isAlerte ? 'bg-red-400'   : ($isFaible ? 'bg-amber-400'   : 'bg-emerald-400');
                            $pct = $p->seuil_alerte > 0 ? min(round(($p->stock_actuel / $p->seuil_alerte) * 100, 1), 100) : null;
                        @endphp
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 space-y-3">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Stock actuel</p>
                                    <p class="text-xl font-bold {{ $stockColor }} mt-0.5">{{ number_format($p->stock_actuel, 0, ',', ' ') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Seuil alerte</p>
                                    <p class="text-xl font-bold text-gray-700 mt-0.5">{{ number_format($p->seuil_alerte, 0, ',', ' ') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Magasin</p>
                                    <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $p->magasin->magasin ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Catégorie</p>
                                    <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $p->categorie->libelle ?? '—' }}</p>
                                </div>
                            </div>

                            @if($pct !== null)
                                <div>
                                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                                        <span>Niveau de stock</span>
                                        <span class="{{ $stockColor }} font-semibold">{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if($p->stock_initial == 0)
                                <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-800">
                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span><strong>Première entrée :</strong> Cette opération définira le stock initial du produit.</span>
                                </div>
                            @endif

                            @if($isAlerte)
                                <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-100 rounded-lg text-xs text-red-800">
                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    <span><strong>Alerte active :</strong> Le stock actuel est en dessous du seuil d'alerte.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fournisseur & quantité --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Fournisseur &amp; quantité
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Fournisseur --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Fournisseur <span class="text-red-500">*</span>
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
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Quantité --}}
                    <div>
                        <label for="quantite" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Quantité reçue <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantite" wire:model="quantite" min="1"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition
                                      @error('quantite') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('quantite')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div>
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Observations
                        </label>
                        <textarea id="observations" wire:model="observations" rows="3"
                                  placeholder="Notes, remarques…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                </div>
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
