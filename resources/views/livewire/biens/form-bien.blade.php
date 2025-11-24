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
                    <div class="md:col-span-2">
                        <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">
                            Désignation <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text"
                            id="designation"
                            wire:model.defer="designation"
                            placeholder="Ex: Bureau direction en bois massif"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('designation') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                        @error('designation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nature --}}
                    <div>
                        <label for="nature" class="block text-sm font-medium text-gray-700 mb-1">
                            Nature du bien <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="nature"
                            wire:model.defer="nature"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('nature') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez une nature</option>
                            @foreach($this->natures as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('nature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date d'acquisition --}}
                    <div>
                        <label for="date_acquisition" class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'acquisition <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date"
                            id="date_acquisition"
                            wire:model.defer="date_acquisition"
                            max="{{ now()->format('Y-m-d') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('date_acquisition') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                        @error('date_acquisition')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2 : Localisation --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                    Localisation
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Localisation --}}
                    <div>
                        <label for="localisation_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Localisation <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="localisation_id"
                            wire:model.defer="localisation_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('localisation_id') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez une localisation</option>
                            @foreach($this->localisations as $localisation)
                                <option value="{{ $localisation->id }}">
                                    {{ $localisation->code }} - {{ $localisation->designation }}
                                </option>
                            @endforeach
                        </select>
                        @error('localisation_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Service usager --}}
                    <div>
                        <label for="service_usager" class="block text-sm font-medium text-gray-700 mb-1">
                            Service usager <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text"
                            id="service_usager"
                            wire:model.defer="service_usager"
                            list="services-list"
                            placeholder="Ex: Direction, Comptabilité"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('service_usager') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                        <datalist id="services-list">
                            @foreach($this->services as $service)
                                <option value="{{ $service }}">
                            @endforeach
                        </datalist>
                        @error('service_usager')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3 : Caractéristiques --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                    Caractéristiques
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Valeur d'acquisition --}}
                    <div>
                        <label for="valeur_acquisition" class="block text-sm font-medium text-gray-700 mb-1">
                            Valeur d'acquisition (MRU) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number"
                                id="valeur_acquisition"
                                wire:model.defer="valeur_acquisition"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('valeur_acquisition') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">MRU</span>
                            </div>
                        </div>
                        @error('valeur_acquisition')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- État --}}
                    <div>
                        <label for="etat" class="block text-sm font-medium text-gray-700 mb-1">
                            État du bien <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="etat"
                            wire:model.defer="etat"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('etat') border-red-300 @enderror"
                            wire:loading.attr="disabled">
                            <option value="">Sélectionnez un état</option>
                            @foreach($this->etats as $key => $label)
                                <option value="{{ $key }}">
                                    @php
                                        $badgeColors = [
                                            'neuf' => '🟢',
                                            'bon' => '🟢',
                                            'moyen' => '🟡',
                                            'mauvais' => '🔴',
                                            'reforme' => '⚫',
                                        ];
                                    @endphp
                                    {{ $badgeColors[$key] ?? '' }} {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('etat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observation --}}
                    <div class="md:col-span-2">
                        <label for="observation" class="block text-sm font-medium text-gray-700 mb-1">
                            Observation
                        </label>
                        <div class="relative">
                            <textarea 
                                id="observation"
                                wire:model.live="observation"
                                rows="3"
                                maxlength="1000"
                                placeholder="Remarques particulières..."
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('observation') border-red-300 @enderror"
                                wire:loading.attr="disabled"></textarea>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-500 bg-white px-1 rounded">
                                {{ strlen($observation ?? '') }}/1000
                            </div>
                        </div>
                        @error('observation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 4 : Options --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                    Options
                </h2>
                
                <div class="space-y-4">
                    {{-- Générer QR Code --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                id="genererQRCode"
                                type="checkbox"
                                wire:model.defer="genererQRCode"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                wire:loading.attr="disabled">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="genererQRCode" class="font-medium text-gray-700">
                                Générer automatiquement le QR code
                            </label>
                            <p class="text-gray-500">
                                Un QR code unique sera généré pour ce bien
                            </p>
                        </div>
                        <div 
                            x-data="{ show: false }"
                            class="ml-2">
                            <button 
                                type="button"
                                @mouseenter="show = true"
                                @mouseleave="show = false"
                                class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div 
                                x-show="show"
                                x-transition
                                class="absolute z-10 w-64 p-2 mt-1 text-xs text-white bg-gray-900 rounded-lg shadow-lg"
                                style="display: none;">
                                Le QR code permettra d'identifier rapidement le bien lors des inventaires
                            </div>
                        </div>
                    </div>

                    {{-- Afficher QR code existant si édition --}}
                    @if($this->isEdit && $bien && $bien->qr_code_path)
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        QR code déjà généré
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Ce bien possède déjà un QR code. Cochez la case ci-dessus pour en générer un nouveau.</p>
                                    </div>
                                    @if($bien->qr_code_path && Storage::disk('public')->exists($bien->qr_code_path))
                                        <div class="mt-3">
                                            @if(str_ends_with($bien->qr_code_path, '.svg'))
                                                <div class="h-24 w-24 border border-blue-300 rounded flex items-center justify-center overflow-hidden">
                                                    @php
                                                        $svgContent = file_get_contents(storage_path('app/public/' . $bien->qr_code_path));
                                                        $svgContent = preg_replace('/width="[^"]*"/', 'width="100%"', $svgContent);
                                                        $svgContent = preg_replace('/height="[^"]*"/', 'height="100%"', $svgContent);
                                                        $svgContent = str_replace('<svg', '<svg style="width: 100%; height: 100%; max-width: 100%; max-height: 100%;"', $svgContent);
                                                    @endphp
                                                    {!! $svgContent !!}
                                                </div>
                                            @else
                                                <img 
                                                    src="{{ asset('storage/' . $bien->qr_code_path) }}"
                                                    alt="QR Code"
                                                    class="h-24 w-24 border border-blue-300 rounded object-contain">
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
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
</div>

