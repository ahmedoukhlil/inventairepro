<div>
    {{-- Header avec breadcrumb --}}
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
                        <a href="{{ route('inventaires.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Inventaires</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Démarrer un inventaire</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900">Démarrer un nouvel inventaire</h1>
        <p class="mt-1 text-sm text-gray-500">
            L'inventaire sera créé en mode 'préparation'. Vous pourrez le passer en 'en cours' une fois prêt.
        </p>
    </div>

    {{-- Wizard avec étapes --}}
    <div x-data="{ etape: @entangle('etapeActuelle') }" class="space-y-6">
        {{-- Indicateur d'étapes --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $etapeActuelle >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <span class="text-sm font-medium">1</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium {{ $etapeActuelle >= 1 ? 'text-indigo-600' : 'text-gray-500' }}">Informations</p>
                        </div>
                    </div>
                    <div class="flex-1 h-0.5 {{ $etapeActuelle >= 2 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $etapeActuelle >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <span class="text-sm font-medium">2</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium {{ $etapeActuelle >= 2 ? 'text-indigo-600' : 'text-gray-500' }}">Localisations</p>
                        </div>
                    </div>
                    <div class="flex-1 h-0.5 {{ $etapeActuelle >= 3 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $etapeActuelle >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <span class="text-sm font-medium">3</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium {{ $etapeActuelle >= 3 ? 'text-indigo-600' : 'text-gray-500' }}">Assignation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="demarrer" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- ÉTAPE 1 : Informations générales --}}
                <div x-show="etape === 1" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations générales</h2>
                    
                    <div class="space-y-6">
                        {{-- Année --}}
                        <div>
                            <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">
                                Année de l'inventaire <span class="text-red-500">*</span>
                            </label>
                            <livewire:components.searchable-select
                                wire:model="annee"
                                :options="$this->anneeOptions"
                                placeholder="Sélectionner une année"
                                search-placeholder="Rechercher une année..."
                                no-results-text="Aucune année disponible"
                                :allow-clear="true"
                                name="annee"
                            />
                            @error('annee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Chaque année ne peut avoir qu'un seul inventaire</p>
                        </div>

                        {{-- Date début --}}
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">
                                Date de début <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date"
                                id="date_debut"
                                wire:model.defer="date_debut"
                                min="{{ now()->format('Y-m-d') }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('date_debut') border-red-300 @enderror">
                            @error('date_debut')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observation --}}
                        <div>
                            <label for="observation" class="block text-sm font-medium text-gray-700 mb-1">
                                Observation
                            </label>
                            <textarea 
                                id="observation"
                                wire:model.defer="observation"
                                rows="3"
                                maxlength="1000"
                                placeholder="Notes sur cet inventaire..."
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('observation') border-red-300 @enderror"></textarea>
                            @error('observation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ strlen($observation) }}/1000 caractères</p>
                        </div>
                    </div>
                </div>

                {{-- ÉTAPE 2 : Sélection des localisations --}}
                <div x-show="etape === 2" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Localisations à inventorier</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $this->totalLocalisations }} localisation(s) sélectionnée(s) | {{ $this->totalBiensAttendus }} immobilisation(s) attendue(s)
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                type="button"
                                wire:click="selectToutesLocalisations"
                                class="px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                Tout sélectionner
                            </button>
                            <button 
                                type="button"
                                wire:click="deselectToutesLocalisations"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Tout désélectionner
                            </button>
                        </div>
                    </div>

                    @error('localisationsSelectionnees')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    {{-- Grille des localisations --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($this->localisations as $localisation)
                            @php
                                $estSelectionnee = in_array($localisation->idLocalisation, $localisationsSelectionnees);
                            @endphp
                            <div 
                                wire:click="toggleLocalisation({{ $localisation->idLocalisation }})"
                                class="relative p-4 border-2 rounded-lg cursor-pointer transition-all {{ $estSelectionnee ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <div class="flex items-start">
                                    <input 
                                        type="checkbox"
                                        wire:model.live="localisationsSelectionnees"
                                        value="{{ $localisation->idLocalisation }}"
                                        class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900">
                                                {{ $localisation->CodeLocalisation ? $localisation->CodeLocalisation . ' - ' : '' }}{{ $localisation->Localisation }}
                                            </h3>
                                        </div>
                                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                            @php
                                                $affectationsCount = $localisation->emplacements()->distinct('idAffectation')->count('idAffectation');
                                                $emplacementsCount = $localisation->emplacements()->count();
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $affectationsCount }} affectation(s)
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                {{ $emplacementsCount }} emplacement(s)
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $localisation->biens_count ?? 0 }} immobilisation(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($this->localisations->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-500">Aucune localisation active disponible</p>
                        </div>
                    @endif
                </div>

                {{-- ÉTAPE 3 : Assignation agents --}}
                <div x-show="etape === 3" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Assignation des agents</h2>
                            <p class="text-sm text-gray-500 mt-1">Assignez des agents aux localisations (optionnel)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox"
                                wire:model.live="assignerLocalisations"
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    @if($assignerLocalisations)
                        {{-- Assignation rapide --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigner un agent</label>
                                    <livewire:components.searchable-select
                                        wire:model="agentGlobalSelect"
                                        :options="$this->agentOptions"
                                        placeholder="Sélectionner un agent"
                                        search-placeholder="Rechercher un agent..."
                                        no-results-text="Aucun agent trouvé"
                                        :allow-clear="true"
                                    />
                                </div>
                                <button 
                                    type="button"
                                    wire:click="assignerAgentGlobal({{ $agentGlobalSelect }})"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                    Assigner aux non assignées
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Cet agent sera assigné à toutes les localisations qui n'ont pas encore d'agent assigné</p>
                        </div>

                        {{-- Tableau des assignations --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immobilisations attendues</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigné à</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($this->localisations->whereIn('idLocalisation', $localisationsSelectionnees) as $localisation)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $localisation->CodeLocalisation ? $localisation->CodeLocalisation . ' - ' : '' }}{{ $localisation->Localisation }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @php
                                                        $affectationsCount = $localisation->emplacements()->distinct('idAffectation')->count('idAffectation');
                                                        $emplacementsCount = $localisation->emplacements()->count();
                                                    @endphp
                                                    {{ $affectationsCount }} affectation(s) • {{ $emplacementsCount }} emplacement(s)
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $localisation->biens_count ?? 0 }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @php
                                                    $agentOptionsWithEmpty = array_merge([['value' => '', 'text' => 'Non assigné']], array_filter($this->agentOptions, fn($opt) => $opt['value'] !== ''));
                                                    $currentAgentId = isset($assignations[$localisation->idLocalisation]) ? (string)$assignations[$localisation->idLocalisation] : '';
                                                @endphp
                                                <livewire:components.searchable-select
                                                    wire:key="agent-select-{{ $localisation->idLocalisation }}"
                                                    :value="$currentAgentId"
                                                    :options="$agentOptionsWithEmpty"
                                                    placeholder="Non assigné"
                                                    search-placeholder="Rechercher un agent..."
                                                    no-results-text="Aucun agent trouvé"
                                                    :allow-clear="true"
                                                    x-on:option-selected.window="$wire.assignerAgent({{ $localisation->idLocalisation }}, $event.detail.value)"
                                                    x-on:option-cleared.window="$wire.assignerAgent({{ $localisation->idLocalisation }}, '')"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Statistiques --}}
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-500">Localisations assignées</p>
                                <p class="text-lg font-bold text-blue-600">{{ count(array_filter($assignations)) }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Non assignées</p>
                                <p class="text-lg font-bold text-gray-600">{{ $this->totalLocalisations - count(array_filter($assignations)) }}</p>
                            </div>
                            <div class="p-3 bg-indigo-50 rounded-lg">
                                <p class="text-xs text-gray-500">Agents impliqués</p>
                                <p class="text-lg font-bold text-indigo-600">{{ $this->agentsImpliques }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar résumé (1/3) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Année</p>
                            <p class="text-lg font-bold text-gray-900">{{ $annee ?: 'Non définie' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $date_debut ? \Carbon\Carbon::parse($date_debut)->format('d/m/Y') : 'Non définie' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Localisations</p>
                            <p class="text-lg font-bold text-indigo-600">{{ $this->totalLocalisations }} sélectionnée(s)</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Immobilisations attendues</p>
                            <p class="text-lg font-bold text-gray-900">{{ $this->totalBiensAttendus }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Agents impliqués</p>
                            <p class="text-lg font-bold text-gray-900">{{ $this->agentsImpliques }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Valeur totale</p>
                            <p class="text-lg font-bold text-indigo-600">
                                {{ number_format($this->valeurTotale, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Boutons de navigation --}}
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg shadow-lg -mx-6 -mb-6">
            <div class="flex justify-between">
                <div>
                    @if($etapeActuelle > 1)
                        <button 
                            type="button"
                            wire:click="etapePrecedente"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            ← Précédent
                        </button>
                    @else
                        <button 
                            type="button"
                            wire:click="cancel"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                    @endif
                </div>
                <div class="flex gap-3">
                    @if($etapeActuelle < 3)
                        <button 
                            type="button"
                            wire:click="etapeSuivante"
                            class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            Suivant →
                        </button>
                    @else
                        <button 
                            type="submit"
                            wire:click="demarrer"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span wire:loading.remove wire:target="demarrer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Créer l'inventaire
                            </span>
                            <span wire:loading wire:target="demarrer" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Création...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

