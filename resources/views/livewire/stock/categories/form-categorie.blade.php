<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="flex items-start gap-3 mb-8">
            <a href="{{ route('stock.categories.index') }}"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $categorie ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                </h1>
                <p class="text-gray-500 mt-0.5">
                    {{ $categorie ? 'Modifiez les informations de la catégorie' : 'Créez une nouvelle catégorie de produits' }}
                </p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 10V5a2 2 0 012-2z"/>
                        </svg>
                        Informations de la catégorie
                    </h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Libellé --}}
                    <div>
                        <label for="libelle" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="libelle"
                               wire:model="libelle"
                               placeholder="Ex : Fournitures de bureau, Matériel informatique…"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                      @error('libelle') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('libelle')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div>
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Observations
                            <span class="font-normal text-gray-400 normal-case">(optionnel)</span>
                        </label>
                        <textarea id="observations"
                                  wire:model="observations"
                                  rows="4"
                                  placeholder="Notes ou informations supplémentaires…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    </div>

                    {{-- Info nb produits en édition --}}
                    @if($categorie)
                        @php $nbProduits = $categorie->produits()->count(); @endphp
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $nbProduits }} produit{{ $nbProduits > 1 ? 's' : '' }} dans cette catégorie
                                </p>
                                @if($nbProduits > 0)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        La suppression est impossible tant que des produits y sont rattachés.
                                    </p>
                                @endif
                            </div>
                            <a href="{{ route('stock.produits.index', ['filterCategorie' => $categorie->id]) }}"
                               class="ml-auto text-xs text-blue-600 hover:text-blue-800 font-medium flex-shrink-0">
                                Voir les produits →
                            </a>
                        </div>
                    @endif
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
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $categorie ? 'Mettre à jour' : 'Créer la catégorie' }}
                </button>
            </div>

        </form>
    </div>
</div>
