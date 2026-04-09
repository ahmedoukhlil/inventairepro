<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="flex items-start gap-3 mb-8">
            <a href="{{ route('stock.demandeurs.index') }}"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $demandeur ? 'Modifier le demandeur' : 'Nouveau demandeur' }}
                </h1>
                <p class="text-gray-500 mt-0.5">
                    {{ $demandeur ? 'Modifiez les informations du demandeur' : 'Créez un nouveau demandeur' }}
                </p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations du demandeur
                    </h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Nom --}}
                    <div>
                        <label for="nom" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nom"
                               wire:model="nom"
                               placeholder="Ex : Mohamed Ahmed, Fatima Mint…"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                      @error('nom') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('nom')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Poste / Service --}}
                    <div>
                        <label for="poste_service" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Poste / Service / Direction <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="poste_service"
                               wire:model="poste_service"
                               placeholder="Ex : Responsable IT, Direction Générale…"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                      @error('poste_service') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('poste_service')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-xs text-gray-400">Poste, service ou direction du demandeur</p>
                        @enderror
                    </div>

                    {{-- Info sorties en édition --}}
                    @if($demandeur)
                        @php $nbSorties = $demandeur->sorties()->count(); @endphp
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $nbSorties }} sortie{{ $nbSorties > 1 ? 's' : '' }} liée{{ $nbSorties > 1 ? 's' : '' }} à ce demandeur
                                </p>
                                @if($nbSorties > 0)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        La suppression est impossible tant que des sorties y sont rattachées.
                                    </p>
                                @endif
                            </div>
                            @if($nbSorties > 0)
                                <a href="{{ route('stock.sorties.index') }}"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium flex-shrink-0">
                                    Voir les sorties →
                                </a>
                            @endif
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
                    {{ $demandeur ? 'Mettre à jour' : 'Créer le demandeur' }}
                </button>
            </div>

        </form>
    </div>
</div>
