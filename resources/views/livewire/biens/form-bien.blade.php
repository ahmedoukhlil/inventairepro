<div>
    {{-- Header avec titre et breadcrumb --}}
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('biens.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Immobilisations</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                            {{ $this->isEdit ? 'Modifier' : 'Ajouter' }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900">
            {{ $this->isEdit ? 'Modifier un bien' : 'Ajouter un bien' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ $this->isEdit ? 'Modifiez les informations du bien' : 'Remplissez le formulaire pour ajouter un nouveau bien à l\'inventaire' }}
        </p>
    </div>

    {{-- Formulaire --}}
    <form wire:submit.prevent="save" class="space-y-6">
        <div 
            wire:loading.class="opacity-50 pointer-events-none"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            
            {{-- Section 1 : Informations générales --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                    Informations générales
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Désignation --}}
                    <div>
                        <label for="idDesignation" class="block text-sm font-medium text-gray-700 mb-1">
                            Désignation <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="idDesignation"
                            wire:model.live="idDesignation"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idDesignation') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez une désignation</option>
                            @foreach($this->designations as $designation)
                                <option value="{{ $designation->id }}">
                                    {{ $designation->designation }}
                                    @if($designation->categorie)
                                        ({{ $designation->categorie->Categorie }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('idDesignation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div>
                        <label for="idCategorie" class="block text-sm font-medium text-gray-700 mb-1">
                            Catégorie <span class="text-red-500">*</span>
                            @if($idDesignation)
                                <span class="text-xs text-gray-500 font-normal ml-2">(automatiquement remplie)</span>
                            @endif
                        </label>
                        <select 
                            id="idCategorie"
                            wire:model.defer="idCategorie"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idCategorie') border-red-300 @enderror {{ $idDesignation ? 'bg-gray-50' : '' }}"
                            wire:loading.attr="disabled"
                            @if($idDesignation) disabled @endif>
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($this->categories as $categorie)
                                <option value="{{ $categorie->idCategorie }}">{{ $categorie->Categorie }}</option>
                            @endforeach
                        </select>
                        @error('idCategorie')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($idDesignation)
                            <p class="mt-1 text-xs text-gray-500">
                                La catégorie est automatiquement définie selon la désignation sélectionnée.
                            </p>
                        @endif
                    </div>

                    {{-- État --}}
                    <div>
                        <label for="idEtat" class="block text-sm font-medium text-gray-700 mb-1">
                            État <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="idEtat"
                            wire:model.defer="idEtat"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idEtat') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez un état</option>
                            @foreach($this->etats as $etat)
                                <option value="{{ $etat->idEtat }}">{{ $etat->Etat }}</option>
                            @endforeach
                        </select>
                        @error('idEtat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Emplacement --}}
                    <div>
                        <label for="idEmplacement" class="block text-sm font-medium text-gray-700 mb-1">
                            Emplacement <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="idEmplacement"
                            wire:model.defer="idEmplacement"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idEmplacement') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez un emplacement</option>
                            @foreach($this->emplacements as $emplacement)
                                <option value="{{ $emplacement->idEmplacement }}">
                                    {{ $emplacement->display_name ?? $emplacement->Emplacement }}
                                </option>
                            @endforeach
                        </select>
                        @error('idEmplacement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nature Juridique --}}
                    <div>
                        <label for="idNatJur" class="block text-sm font-medium text-gray-700 mb-1">
                            Nature Juridique <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="idNatJur"
                            wire:model.defer="idNatJur"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idNatJur') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez une nature juridique</option>
                            @foreach($this->natureJuridiques as $natJur)
                                <option value="{{ $natJur->idNatJur }}">{{ $natJur->NatJur }}</option>
                            @endforeach
                        </select>
                        @error('idNatJur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Source de Financement --}}
                    <div>
                        <label for="idSF" class="block text-sm font-medium text-gray-700 mb-1">
                            Source de Financement <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="idSF"
                            wire:model.defer="idSF"
                            class="select2-search block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('idSF') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez une source de financement</option>
                            @foreach($this->sourceFinancements as $sf)
                                <option value="{{ $sf->idSF }}">{{ $sf->SourceFin }}</option>
                            @endforeach
                        </select>
                        @error('idSF')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Année d'acquisition --}}
                    <div>
                        <label for="DateAcquisition" class="block text-sm font-medium text-gray-700 mb-1">
                            Année d'acquisition
                        </label>
                        <input 
                            type="number"
                            id="DateAcquisition"
                            wire:model.defer="DateAcquisition"
                            min="1900"
                            max="{{ now()->year + 1 }}"
                            placeholder="Ex: {{ now()->year }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('DateAcquisition') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                        @error('DateAcquisition')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Saisissez uniquement l'année (ex: 2024)</p>
                    </div>

                    {{-- Quantité (uniquement en mode création) --}}
                    @if(!$this->isEdit)
                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number"
                            id="quantite"
                            wire:model.defer="quantite"
                            min="1"
                            max="1000"
                            placeholder="1"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('quantite') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                        @error('quantite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Nombre d'immobilisations identiques à créer. Chaque immobilisation aura un NumOrdre unique.
                        </p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Boutons d'action (sticky footer) --}}
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg shadow-lg -mx-6 -mb-6">
            <div class="flex justify-end gap-3">
                <button 
                    type="button"
                    wire:click="cancel"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Annuler
                </button>
                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span wire:loading.remove wire:target="save">
                        {{ $this->isEdit ? 'Modifier' : 'Enregistrer' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </form>

    {{-- Messages flash --}}
    @if(session()->has('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            x-transition
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    {{-- Select2 pour les champs de recherche --}}
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Select2 sur tous les selects avec la classe select2-search
            function initSelect2() {
                $('.select2-search').select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: function() {
                        return $(this).find('option[value=""]').text() || 'Rechercher...';
                    },
                    language: {
                        noResults: function() {
                            return "Aucun résultat trouvé";
                        },
                        searching: function() {
                            return "Recherche en cours...";
                        }
                    },
                    minimumResultsForSearch: 0 // Toujours afficher la recherche
                });

                // Synchroniser Select2 avec Livewire
                $('.select2-search').on('change', function(e) {
                    var select = $(this);
                    var wireModel = select.attr('wire:model') || select.attr('wire:model.defer') || select.attr('wire:model.live');
                    if (wireModel) {
                        var propertyName = wireModel.replace('wire:model.defer=', '').replace('wire:model=', '').replace('wire:model.live=', '');
                        var value = select.val();
                        // Utiliser @this pour mettre à jour la propriété Livewire
                        @this.set(propertyName, value);
                    }
                });
            }

            // Initialiser au chargement
            initSelect2();

            // Réinitialiser après les mises à jour Livewire
            document.addEventListener('livewire:update', function() {
                setTimeout(function() {
                    $('.select2-search').select2('destroy');
                    initSelect2();
                    
                    // Mettre à jour l'état disabled du champ catégorie après mise à jour Livewire
                    var categorieSelect = $('#idCategorie');
                    if (categorieSelect.length) {
                        var isDisabled = categorieSelect.prop('disabled') || categorieSelect.attr('disabled') !== undefined;
                        if (isDisabled) {
                            categorieSelect.addClass('bg-gray-50');
                        } else {
                            categorieSelect.removeClass('bg-gray-50');
                        }
                    }
                }, 100);
            });

            // Réinitialiser après les erreurs de validation
            document.addEventListener('livewire:error', function() {
                setTimeout(function() {
                    $('.select2-search').select2('destroy');
                    initSelect2();
                }, 100);
            });
        });
    </script>
    @endpush
</div>
