<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

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
                <p class="text-gray-500 mt-0.5">Enregistrez une distribution de produit</p>
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

        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Date --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Date de sortie
                    </h2>
                </div>
                <div class="p-6">
                    <div class="max-w-xs">
                        <label for="date_sortie" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_sortie" wire:model="date_sortie"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition
                                      @error('date_sortie') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('date_sortie')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
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
                        Produit à distribuer
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

                    {{-- Fiche stock disponible --}}
                    @if($produitSelectionne)
                        @php
                            $p = $produitSelectionne;
                            $isAlerte = $p->en_alerte;
                            $isFaible = $p->stock_faible;
                            $epuise = $stockDisponible <= 0;
                            $stockColor = $epuise ? 'text-red-600' : ($isAlerte ? 'text-red-600' : ($isFaible ? 'text-amber-500' : 'text-emerald-600'));
                            $barColor   = $epuise ? 'bg-red-400' : ($isAlerte ? 'bg-red-400' : ($isFaible ? 'bg-amber-400' : 'bg-emerald-400'));
                            $pct = $p->seuil_alerte > 0 ? min(round(($p->stock_actuel / $p->seuil_alerte) * 100, 1), 100) : null;
                        @endphp
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Stock disponible</p>
                                    <p class="text-3xl font-bold {{ $stockColor }} mt-0.5">
                                        {{ number_format($stockDisponible, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Seuil d'alerte : <span class="font-semibold text-gray-600">{{ $p->seuil_alerte }}</span>
                                        &bull; {{ $p->magasin->magasin ?? '—' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $epuise ? 'bg-red-100' : ($isAlerte ? 'bg-red-50' : ($isFaible ? 'bg-amber-50' : 'bg-emerald-50')) }} flex items-center justify-center">
                                    <svg class="w-6 h-6 {{ $epuise || $isAlerte ? 'text-red-500' : ($isFaible ? 'text-amber-500' : 'text-emerald-500') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if($epuise || $isAlerte)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        @endif
                                    </svg>
                                </div>
                            </div>

                            @if($pct !== null)
                                <div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $pct }}% du seuil d'alerte</p>
                                </div>
                            @endif

                            @if($epuise)
                                <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    <span><strong>Stock épuisé</strong> — Impossible d'enregistrer une sortie pour ce produit.</span>
                                </div>
                            @elseif($isAlerte)
                                <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-100 rounded-lg text-xs text-red-800">
                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    <span><strong>Alerte active :</strong> Le stock est déjà en dessous du seuil d'alerte. Un réapprovisionnement est recommandé.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Demandeur & quantité --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Demandeur &amp; quantité
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Demandeur --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
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
                        @error('demandeur_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Quantité --}}
                    <div>
                        <label for="quantite" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantite" wire:model="quantite" min="1"
                               @if($stockDisponible > 0) max="{{ $stockDisponible }}" @endif
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition
                                      @error('quantite') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('quantite')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @else
                            @if($stockDisponible > 0)
                                <p class="mt-1 text-xs text-gray-400">
                                    Maximum disponible : <span class="font-semibold text-violet-600">{{ number_format($stockDisponible, 0, ',', ' ') }}</span>
                                </p>
                            @endif
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div>
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Observations
                        </label>
                        <textarea id="observations" wire:model="observations" rows="3"
                                  placeholder="Notes, motif de la distribution…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>

                @if(!$produitSelectionne || $stockDisponible <= 0)
                    <button type="button" disabled
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gray-300 rounded-lg cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                        </svg>
                        {{ !$produitSelectionne ? 'Sélectionner un produit' : 'Stock épuisé' }}
                    </button>
                @else
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors disabled:opacity-60">
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                        </svg>
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer la sortie
                    </button>
                @endif
            </div>

        </form>
    </div>
</div>
