<div>
    @php
        $isAdmin = auth()->user()->isAdmin();
        $statutsInventaire = [
            'en_preparation' => ['label' => 'En préparation', 'color' => 'bg-gray-100 text-gray-800'],
            'en_cours' => ['label' => 'En cours', 'color' => 'bg-blue-100 text-blue-800'],
            'termine' => ['label' => 'Terminé', 'color' => 'bg-orange-100 text-orange-800'],
            'cloture' => ['label' => 'Clôturé', 'color' => 'bg-green-100 text-green-800'],
        ];
        $statutsLoc = [
            'en_attente' => ['label' => 'En attente', 'color' => 'bg-gray-100 text-gray-800', 'icon' => 'clock'],
            'en_cours' => ['label' => 'En cours', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'play'],
            'termine' => ['label' => 'Terminée', 'color' => 'bg-green-100 text-green-800', 'icon' => 'check'],
        ];
        $statutsScan = [
            'present' => ['label' => 'Présent', 'color' => 'bg-green-100 text-green-800'],
            'deplace' => ['label' => 'Déplacé', 'color' => 'bg-yellow-100 text-yellow-800'],
            'absent' => ['label' => 'Absent', 'color' => 'bg-red-100 text-red-800'],
            'deteriore' => ['label' => 'Détérioré', 'color' => 'bg-orange-100 text-orange-800'],
        ];
    @endphp

    {{-- Actualisation automatique désactivée --}}
    {{-- Le polling automatique a été complètement supprimé pour éviter les actualisations non désirées --}}

    {{-- Header avec breadcrumb et actions --}}
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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Inventaire {{ $inventaire->annee }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    Inventaire {{ $inventaire->annee }}
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold {{ $statutsInventaire[$inventaire->statut]['color'] }}">
                        {{ $statutsInventaire[$inventaire->statut]['label'] }}
                    </span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    @if($inventaire->date_debut)
                        Du {{ $inventaire->date_debut->format('d/m/Y') }} 
                        @if($inventaire->date_fin)
                            au {{ $inventaire->date_fin->format('d/m/Y') }}
                        @else
                            - En cours...
                        @endif
                    @else
                        Date de début non définie
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                @if($isAdmin)
                    @if($inventaire->statut === 'en_preparation')
                        <button 
                            wire:click="passerEnCours"
                            wire:confirm="Êtes-vous sûr de vouloir démarrer cet inventaire ?"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Démarrer l'inventaire
                        </button>
                    @endif

                    @if($inventaire->statut === 'en_cours')
                        <button 
                            wire:click="terminerInventaire"
                            wire:confirm="Êtes-vous sûr de vouloir terminer cet inventaire ? Vérifiez que toutes les localisations sont terminées."
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Terminer l'inventaire
                        </button>
                    @endif

                    @if($inventaire->statut === 'termine')
                        <button 
                            wire:click="cloturerInventaire"
                            wire:confirm="Êtes-vous sûr de vouloir clôturer définitivement cet inventaire ? Cette action est irréversible."
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Clôturer définitivement
                        </button>
                    @endif
                @endif

                <a 
                    href="{{ route('inventaires.rapport', $inventaire) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Voir rapport
                </a>
            </div>
        </div>
    </div>

    {{-- Section statistiques globales (5 cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Card 1 : Progression --}}
        <div 
            wire:key="card-progression-{{ $this->statistiques['progression_globale'] }}"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-md"
            x-data="{ updated: false }"
            x-effect="updated = true; setTimeout(() => updated = false, 1000)"
            :class="{ 'ring-2 ring-indigo-200': updated }">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Progression globale</h3>
            <p 
                wire:key="progression-{{ $this->statistiques['progression_globale'] }}"
                class="text-4xl font-bold text-indigo-600 mb-3 transition-all duration-500 ease-out"
                x-data="{ value: {{ round($this->statistiques['progression_globale'], 1) }} }"
                x-effect="value = {{ round($this->statistiques['progression_globale'], 1) }}"
                x-text="value.toFixed(1) + '%'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                {{ round($this->statistiques['progression_globale'], 1) }}%
            </p>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                <div 
                    wire:key="progress-bar-{{ $this->statistiques['progression_globale'] }}"
                    class="bg-indigo-600 h-3 rounded-full transition-all duration-700 ease-out"
                    style="width: {{ $this->statistiques['progression_globale'] }}%"></div>
            </div>
            <p 
                wire:key="progress-text-{{ $this->statistiques['total_biens_scannes'] }}"
                class="text-xs text-gray-500 transition-colors duration-300">
                {{ $this->statistiques['total_biens_scannes'] }}/{{ $this->statistiques['total_biens_attendus'] }} immobilisations scannées
            </p>
        </div>

        {{-- Card 2 : Localisations --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-md">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Localisations</h3>
            <p 
                wire:key="localisations-{{ $this->statistiques['localisations_terminees'] }}-{{ $this->statistiques['total_localisations'] }}"
                class="text-4xl font-bold text-gray-900 mb-3 transition-all duration-500 ease-out"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                {{ $this->statistiques['localisations_terminees'] }}/{{ $this->statistiques['total_localisations'] }}
            </p>
            <div class="space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">En attente</span>
                    <span class="font-medium text-gray-700">{{ $this->statistiques['localisations_en_attente'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">En cours</span>
                    <span class="font-medium text-blue-600">{{ $this->statistiques['localisations_en_cours'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Terminées</span>
                    <span class="font-medium text-green-600">{{ $this->statistiques['localisations_terminees'] }}</span>
                </div>
            </div>
        </div>

        {{-- Card 3 : Conformité --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-md">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Taux de conformité</h3>
            @php
                $conformiteColor = $this->statistiques['taux_conformite'] >= 90 ? 'text-green-600' : ($this->statistiques['taux_conformite'] >= 70 ? 'text-yellow-600' : 'text-red-600');
                $conformiteBg = $this->statistiques['taux_conformite'] >= 90 ? 'bg-green-100 text-green-800' : ($this->statistiques['taux_conformite'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
            @endphp
            <p 
                wire:key="conformite-{{ round($this->statistiques['taux_conformite'], 1) }}"
                class="text-4xl font-bold {{ $conformiteColor }} mb-3 transition-all duration-500 ease-out"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                {{ round($this->statistiques['taux_conformite'], 1) }}%
            </p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conformiteBg }}">
                {{ $this->statistiques['biens_presents'] }} immobilisations conformes
            </span>
        </div>

        {{-- Card 4 : Anomalies --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-md">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Alertes</h3>
            <p 
                wire:key="alertes-{{ $this->totalAlertes }}"
                class="text-4xl font-bold text-red-600 mb-3 transition-all duration-500 ease-out"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                {{ $this->totalAlertes }}
            </p>
            <div class="space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">Déplacés</span>
                    <span class="font-medium text-yellow-600">{{ $this->statistiques['biens_deplaces'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Absents</span>
                    <span class="font-medium text-red-600">{{ $this->statistiques['biens_absents'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Détériorés</span>
                    <span class="font-medium text-orange-600">{{ $this->statistiques['biens_deteriores'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Défectueux</span>
                    <span class="font-medium text-amber-600">{{ $this->statistiques['biens_defectueux'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Card 5 : Durée --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Durée</h3>
            <p class="text-4xl font-bold text-gray-900 mb-3">{{ $this->statistiques['duree_jours'] }}</p>
            <p class="text-xs text-gray-500">jour(s)</p>
            <p class="text-xs text-gray-400 mt-2">
                @if($inventaire->date_debut)
                    {{ $inventaire->date_debut->format('d/m/Y') }} → 
                    {{ $inventaire->date_fin ? $inventaire->date_fin->format('d/m/Y') : 'Aujourd\'hui' }}
                @else
                    Date non définie
                @endif
            </p>
        </div>
    </div>

    {{-- Section graphiques (2 colonnes) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Graphique 1 : Répartition statuts scans (Pie Chart) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Répartition des scans</h3>
                <p class="text-sm text-gray-500">État des immobilisations scannées</p>
            </div>
            <div class="relative" style="height: 320px;" wire:key="chart-scans-{{ $this->statistiques['total_biens_scannes'] }}">
                <canvas id="chart-scans"></canvas>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total scanné:</span>
                        <span class="font-bold text-gray-900">{{ $this->statistiques['total_biens_scannes'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Conformité:</span>
                        <span class="font-bold text-green-600">{{ round($this->statistiques['taux_conformite'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Graphique 2 : Progression par localisation (Bar Chart) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Progression par localisation</h3>
                <p class="text-sm text-gray-500">Top 10 des localisations par taux de progression</p>
            </div>
            <div class="relative" style="height: 320px;" wire:key="chart-prog-loc-{{ $this->inventaireLocalisations->count() }}">
                <canvas id="chart-progression-loc"></canvas>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-center gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-green-500"></div>
                        <span class="text-gray-600">Terminée</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-blue-500"></div>
                        <span class="text-gray-600">En cours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-gray-400"></div>
                        <span class="text-gray-600">En attente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique 3 : Progression temporelle (Line Chart) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Progression temporelle</h3>
            <p class="text-sm text-gray-500">Évolution du nombre d'immobilisations scannées au fil du temps</p>
        </div>
        <div class="relative" style="height: 350px;" wire:key="chart-temp-{{ $this->statistiques['total_biens_scannes'] }}">
            <canvas id="chart-progression-temporelle"></canvas>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Début:</span>
                    <span class="font-medium text-gray-900">{{ $inventaire->date_debut ? $inventaire->date_debut->format('d/m/Y') : 'Non défini' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Scans aujourd'hui:</span>
                    <span class="font-bold text-blue-600">{{ $this->statistiques['scans_aujourdhui'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Vitesse moyenne:</span>
                    <span class="font-medium text-gray-900">{{ $this->statistiques['vitesse_moyenne'] ?? 0 }}/jour</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Section alertes --}}
    @if($this->totalAlertes > 0)
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">Alertes détectées</h3>
                    <div class="mt-2 text-sm text-red-700 space-y-1">
                        @if(count($this->alertes['localisations_non_demarrees']) > 0)
                            <p>⚠️ {{ count($this->alertes['localisations_non_demarrees']) }} localisation(s) non démarrée(s)</p>
                        @endif
                        @foreach(array_slice($this->alertes['localisations_bloquees'], 0, 3) as $alerte)
                            <p>⚠️ {{ $alerte['code'] }} bloquée (pas de scan depuis {{ $alerte['jours'] }} jour(s))</p>
                        @endforeach
                        @if(count($this->alertes['biens_absents_valeur_haute']) > 0)
                            <p>⚠️ {{ count($this->alertes['biens_absents_valeur_haute']) }} immobilisation(s) absente(s) de valeur élevée (>100k MRU)</p>
                        @endif
                        @if(count($this->alertes['biens_defectueux'] ?? []) > 0)
                            <p>⚠️ {{ count($this->alertes['biens_defectueux']) }} immobilisation(s) signalée(s) défectueuse(s) lors de l'inventaire</p>
                        @endif
                        @if(count($this->alertes['localisations_non_assignees']) > 0)
                            <p>⚠️ {{ count($this->alertes['localisations_non_assignees']) }} localisation(s) non assignée(s)</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Section localisations (tableau principal) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-semibold text-gray-900">État par localisation</h2>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="searchLoc"
                            placeholder="Rechercher..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <select 
                        wire:model.live="filterStatutLoc"
                        class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="all">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminées</option>
                    </select>
                    <select 
                        wire:model.live="filterAgent"
                        class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="all">Tous les agents</option>
                        @foreach($this->agents as $agent)
                            <option value="{{ $agent->idUser }}">{{ $agent->users ?? $agent->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('code')">
                            <div class="flex items-center">
                                Code
                                @if($sortField === 'code')
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immobilisations</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durée</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->inventaireLocalisations as $invLoc)
                        @php
                            $progression = $invLoc->progression ?? ($invLoc->nombre_biens_attendus > 0 
                                ? round(($invLoc->nombre_biens_scannes / $invLoc->nombre_biens_attendus) * 100, 1) 
                                : 0);
                            $conformite = $invLoc->taux_conformite ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($invLoc->localisation)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $invLoc->localisation->CodeLocalisation ?? 'N/A' }}
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-red-600">Localisation introuvable</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($invLoc->localisation)
                                    <div class="text-sm text-gray-900">{{ $invLoc->localisation->Localisation ?? 'N/A' }}</div>
                                @else
                                    <div class="text-sm text-red-600">N/A</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if(isset($statutsLoc[$invLoc->statut]))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutsLoc[$invLoc->statut]['color'] }}">
                                        @if(isset($statutsLoc[$invLoc->statut]['icon']))
                                            @if($statutsLoc[$invLoc->statut]['icon'] === 'clock')
                                                ⏳
                                            @elseif($statutsLoc[$invLoc->statut]['icon'] === 'play')
                                                🔄
                                            @elseif($statutsLoc[$invLoc->statut]['icon'] === 'check')
                                                ✅
                                            @endif
                                        @endif
                                        {{ $statutsLoc[$invLoc->statut]['label'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $invLoc->statut ?? 'N/A')) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-semibold {{ $invLoc->nombre_biens_scannes > 0 ? 'text-blue-600' : 'text-gray-500' }}">
                                    {{ number_format($invLoc->nombre_biens_scannes ?? 0, 0, ',', ' ') }}
                                </span>
                                <span class="text-gray-400">/</span>
                                <span class="text-gray-700">
                                    {{ number_format($invLoc->nombre_biens_attendus ?? 0, 0, ',', ' ') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div 
                                            class="bg-indigo-600 h-2 rounded-full"
                                            style="width: {{ min($progression, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $progression }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $conformiteColor = $conformite >= 90 ? 'text-green-600' : ($conformite >= 70 ? 'text-yellow-600' : ($conformite > 0 ? 'text-orange-600' : 'text-gray-400'));
                                @endphp
                                <span class="text-sm font-medium {{ $conformiteColor }}">{{ number_format($conformite, 1) }}%</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="space-y-2">
                                    @if($invLoc->agent)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-xs font-medium text-indigo-600">{{ strtoupper(substr($invLoc->agent->users ?? 'N', 0, 1)) }}</span>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $invLoc->agent->users ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Non assigné</span>
                                    @endif
                                    @if($isAdmin)
                                        <select 
                                            wire:change="reassignerLocalisation({{ $invLoc->id }}, $event.target.value)"
                                            class="mt-1 block w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Réassigner...</option>
                                            @foreach($this->agents as $agent)
                                                <option value="{{ $agent->idUser }}" {{ $invLoc->user_id == $agent->idUser ? 'selected' : '' }}>
                                                    {{ $agent->users ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                @if($invLoc->date_debut_scan && $invLoc->date_fin_scan)
                                    @php
                                        $heures = $invLoc->date_debut_scan->diffInHours($invLoc->date_fin_scan);
                                        $jours = floor($heures / 24);
                                        $heuresRestantes = $heures % 24;
                                    @endphp
                                    @if($jours > 0)
                                        {{ $jours }}j {{ $heuresRestantes }}h
                                    @else
                                        {{ $heures }}h
                                    @endif
                                @elseif($invLoc->date_debut_scan)
                                    <span class="text-blue-600 font-medium">En cours...</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a 
                                    href="{{ route('localisations.show', $invLoc->localisation) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                                Aucune localisation trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section activité récente (sidebar) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Derniers scans</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($this->derniersScans as $scan)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-xs font-medium text-indigo-600">{{ substr($scan->agent->users ?? 'N', 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $scan->agent->users ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $scan->date_scan->diffForHumans() }}</p>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            a scanné <span class="font-medium">{{ $scan->code_inventaire ?? ($scan->gesimmo ? 'GS' . $scan->gesimmo->NumOrdre : 'N/A') }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">{{ $scan->localisationReelle->CodeLocalisation ?? ($scan->localisationReelle->Localisation ?? 'N/A') }}</span>
                            @if(isset($statutsScan[$scan->statut_scan]))
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statutsScan[$scan->statut_scan]['color'] }}">
                                    {{ $statutsScan[$scan->statut_scan]['label'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-8">Aucun scan enregistré</p>
            @endforelse
        </div>
    </div>

    {{-- Scripts pour les graphiques Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Animations élégantes pour les mises à jour */
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .stat-card-updating {
            animation: pulse-subtle 1.5s ease-in-out;
        }
        
        .value-update {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .value-update.updated {
            animation: highlight-update 0.6s ease-out;
        }
        
        @keyframes highlight-update {
            0% { 
                transform: scale(1);
                background-color: transparent;
            }
            50% { 
                transform: scale(1.05);
            }
            100% { 
                transform: scale(1);
                background-color: transparent;
            }
        }
    </style>
    <script>
        // Variables globales pour les graphiques
        let chartScans = null;
        let chartProgLoc = null;
        let chartTemp = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Plugin personnalisé pour afficher le texte au centre du doughnut
            const centerTextPlugin = {
                id: 'centerText',
                afterDraw: function(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    
                    const ctx = chart.ctx;
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    
                    const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    
                    ctx.save();
                    ctx.font = 'bold 32px Arial';
                    ctx.fillStyle = '#1f2937';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(total, centerX, centerY - 10);
                    
                    ctx.font = '14px Arial';
                    ctx.fillStyle = '#6b7280';
                    ctx.fillText('Total scanné', centerX, centerY + 20);
                    ctx.restore();
                }
            };
            
            // Enregistrer le plugin
            Chart.register(centerTextPlugin);
            
            // Graphique 1 : Répartition statuts scans (Doughnut Chart amélioré)
            const ctxScans = document.getElementById('chart-scans');
            if (ctxScans) {
                const stats = @json($this->statistiques);
                const biensDefectueux = stats.biens_defectueux ?? 0;
                const total = stats.biens_presents + stats.biens_deplaces + stats.biens_absents + stats.biens_deteriores + biensDefectueux;
                
                chartScans = new Chart(ctxScans, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Présents (' + stats.biens_presents + ')',
                            'Déplacés (' + stats.biens_deplaces + ')',
                            'Absents (' + stats.biens_absents + ')',
                            'Détériorés (' + stats.biens_deteriores + ')',
                            'Défectueux (' + biensDefectueux + ')'
                        ],
                        datasets: [{
                            data: [
                                stats.biens_presents,
                                stats.biens_deplaces,
                                stats.biens_absents,
                                stats.biens_deteriores,
                                biensDefectueux
                            ],
                            backgroundColor: [
                                '#22c55e',   // Vert vif - Présents
                                '#eab308',   // Jaune vif - Déplacés
                                '#ef4444',   // Rouge vif - Absents
                                '#f97316',   // Orange vif - Détériorés
                                '#d97706',   // Amber - Défectueux
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverBorderWidth: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 14,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label.split(' (')[0];
                                    },
                                    label: function(context) {
                                        const label = context.label.split(' (')[0];
                                        const value = context.parsed || 0;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return [
                                            'Quantité: ' + value + ' immobilisation(s)',
                                            'Pourcentage: ' + percentage + '%'
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Graphique 2 : Progression par localisation (Bar Chart amélioré)
            const ctxProgLoc = document.getElementById('chart-progression-loc');
            if (ctxProgLoc) {
                const invLocs = @json($this->localisationsGraphData);
                const progressionData = invLocs.map(loc => {
                    const total = loc.nombre_biens_attendus || 1;
                    return Math.round((loc.nombre_biens_scannes / total) * 100);
                });
                
                chartProgLoc = new Chart(ctxProgLoc, {
                    type: 'bar',
                    data: {
                        labels: invLocs.map(loc => loc.localisation?.CodeLocalisation ?? loc.localisation?.Localisation ?? 'N/A'),
                        datasets: [{
                            label: 'Progression (%)',
                            data: progressionData,
                            backgroundColor: invLocs.map(loc => {
                                if (loc.statut === 'termine') return '#22c55e';      // Vert vif
                                if (loc.statut === 'en_cours') return '#3b82f6';    // Bleu vif
                                return '#9ca3af';                                   // Gris
                            }),
                            borderColor: invLocs.map(loc => {
                                if (loc.statut === 'termine') return '#16a34a';
                                if (loc.statut === 'en_cours') return '#2563eb';
                                return '#6b7280';
                            }),
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        const loc = invLocs[index];
                                        return loc.localisation?.Localisation ?? loc.localisation?.CodeLocalisation ?? 'N/A';
                                    },
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const loc = invLocs[index];
                                        const progression = progressionData[index];
                                        const scannes = loc.nombre_biens_scannes || 0;
                                        const attendus = loc.nombre_biens_attendus || 0;
                                        return [
                                            'Progression: ' + progression + '%',
                                            'Scanné: ' + scannes + ' / ' + attendus,
                                            'Statut: ' + (loc.statut === 'termine' ? 'Terminée' : (loc.statut === 'en_cours' ? 'En cours' : 'En attente'))
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: '#374151'
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            }

            // Graphique 3 : Progression temporelle (Line Chart amélioré)
            const ctxTemp = document.getElementById('chart-progression-temporelle');
            if (ctxTemp) {
                const scans = @json($this->scansGraphData);
                const objectif = {{ $this->statistiques['total_biens_attendus'] ?? 0 }};
                
                // Grouper les scans par date et calculer le cumul correct
                const scansParDate = {};
                scans.forEach(scan => {
                    const date = new Date(scan.date_scan).toLocaleDateString('fr-FR', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        year: 'numeric' 
                    });
                    if (!scansParDate[date]) {
                        scansParDate[date] = 0;
                    }
                    scansParDate[date] += 1;
                });
                
                // Trier les dates et calculer le cumul
                const dates = Object.keys(scansParDate).sort((a, b) => {
                    return new Date(a.split('/').reverse().join('-')) - new Date(b.split('/').reverse().join('-'));
                });
                
                const quotidien = dates.map(date => scansParDate[date]);
                const cumulatif = [];
                let cumul = 0;
                quotidien.forEach(qty => {
                    cumul += qty;
                    cumulatif.push(cumul);
                });

                chartTemp = new Chart(ctxTemp, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Cumul des scans',
                            data: cumulatif,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }, {
                            label: 'Objectif (' + objectif + ')',
                            data: new Array(dates.length).fill(objectif),
                            borderColor: '#9ca3af',
                            borderWidth: 2,
                            borderDash: [8, 4],
                            fill: false,
                            pointRadius: 0,
                            pointHoverRadius: 0,
                        }, {
                            label: 'Scans quotidiens',
                            data: quotidien,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            yAxisID: 'y1',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 14,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.y || 0;
                                        if (context.datasetIndex === 0) {
                                            const pourcentage = objectif > 0 ? ((value / objectif) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + pourcentage + '% de l\'objectif)';
                                        } else if (context.datasetIndex === 1) {
                                            return label;
                                        } else {
                                            return label + ': ' + value + ' scan(s)';
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: Math.max(objectif, Math.max(...cumulatif)) * 1.1,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#6b7280',
                                    callback: function(value) {
                                        return value.toLocaleString('fr-FR');
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#10b981'
                                }
                            }
                        }
                    }
                });
            }
            
            // Fonction pour mettre à jour les graphiques de manière élégante
            function updateCharts() {
                // Les graphiques seront mis à jour manuellement si nécessaire
                // L'actualisation automatique est désactivée
            }
            
            // Écouter les événements Livewire pour mettre à jour les graphiques (désactivé)
            // document.addEventListener('livewire:init', () => {
            //     Livewire.on('statistiques-updated', () => {
            //         // Les graphiques seront recréés automatiquement lors du re-render
            //         // grâce aux wire:key sur les éléments
            //     });
            // });
        });
    </script>

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

