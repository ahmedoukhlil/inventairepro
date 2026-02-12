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
        $etatsConstate = [
            'neuf' => ['label' => 'Neuf', 'color' => 'bg-green-100 text-green-800'],
            'bon' => ['label' => 'Bon état', 'color' => 'bg-blue-100 text-blue-800'],
            'moyen' => ['label' => 'État moyen', 'color' => 'bg-yellow-100 text-yellow-800'],
            'mauvais' => ['label' => 'Défectueuse', 'color' => 'bg-amber-100 text-amber-800'],
        ];
    @endphp

    {{-- Indicateur de mise à jour discret et élégant --}}
    @if(in_array($inventaire->statut, ['en_preparation', 'en_cours']))
        <div 
            x-data="{ 
                updating: false,
                lastUpdate: null,
                init() {
                    // Écouter les mises à jour Livewire
                    Livewire.on('statistiques-updated', () => {
                        this.updating = true;
                        this.lastUpdate = new Date();
                        setTimeout(() => {
                            this.updating = false;
                        }, 1500);
                    });
                    
                    // Afficher un indicateur subtil lors du polling
                    this.$watch('$wire.__instance.loading', (loading) => {
                        if (loading && !this.updating) {
                            this.updating = true;
                            setTimeout(() => {
                                this.updating = false;
                            }, 800);
                        }
                    });
                }
            }"
            class="fixed top-4 right-4 z-50 pointer-events-none"
            x-show="updating"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-1"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div class="bg-indigo-600/95 backdrop-blur-sm text-white px-3 py-1.5 rounded-full shadow-lg flex items-center gap-2 text-xs font-medium">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Actualisation...</span>
            </div>
        </div>
        
        {{-- Polling optimisé (10 secondes) pour les statistiques en temps réel --}}
        <div wire:poll.10s="refreshStatistiques" wire:key="stats-poll-{{ $inventaire->id }}" wire:loading.class="opacity-50" class="transition-opacity duration-300"></div>
    @endif

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
                    Du {{ $inventaire->date_debut->format('d/m/Y') }} 
                    @if($inventaire->date_fin)
                        au {{ $inventaire->date_fin->format('d/m/Y') }}
                    @else
                        - En cours...
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                @if($isAdmin)
                    @if($inventaire->statut === 'en_preparation')
                        <button 
                            wire:click="passerEnCours"
                            wire:confirm="Êtes-vous sûr de vouloir démarrer cet inventaire ?"
                            wire:loading.attr="disabled"
                            wire:target="passerEnCours"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading.remove wire:target="passerEnCours" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg wire:loading wire:target="passerEnCours" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="passerEnCours">Démarrer l'inventaire</span>
                            <span wire:loading wire:target="passerEnCours">Démarrage...</span>
                        </button>
                    @endif

                    @if($inventaire->statut === 'en_cours')
                        <button 
                            wire:click="terminerInventaire"
                            wire:confirm="Êtes-vous sûr de vouloir terminer cet inventaire ? Vérifiez que toutes les localisations sont terminées."
                            wire:loading.attr="disabled"
                            wire:target="terminerInventaire"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading.remove wire:target="terminerInventaire" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg wire:loading wire:target="terminerInventaire" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="terminerInventaire">Terminer l'inventaire</span>
                            <span wire:loading wire:target="terminerInventaire">Finalisation...</span>
                        </button>
                    @endif

                    @if($inventaire->statut === 'termine')
                        <button 
                            wire:click="cloturerInventaire"
                            wire:confirm="Êtes-vous sûr de vouloir clôturer définitivement cet inventaire ? Cette action est irréversible."
                            wire:loading.attr="disabled"
                            wire:target="cloturerInventaire"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading.remove wire:target="cloturerInventaire" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <svg wire:loading wire:target="cloturerInventaire" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="cloturerInventaire">Clôturer définitivement</span>
                            <span wire:loading wire:target="cloturerInventaire">Clôture...</span>
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

        {{-- Card 5 : Activité & Durée --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-md">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Activité</h3>
            <div class="flex items-baseline gap-2 mb-3">
                <p class="text-4xl font-bold text-gray-900">{{ $this->statistiques['scans_aujourdhui'] }}</p>
                <span class="text-sm text-gray-500">scan(s) aujourd'hui</span>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">Vitesse moyenne</span>
                    <span class="font-medium text-gray-700">{{ $this->statistiques['vitesse_moyenne'] }}/jour</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Durée</span>
                    <span class="font-medium text-gray-700">{{ $this->statistiques['duree_jours'] }} jour(s)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Période</span>
                    <span class="text-gray-400">{{ $inventaire->date_debut->format('d/m') }} → {{ $inventaire->date_fin ? $inventaire->date_fin->format('d/m') : 'Auj.' }}</span>
                </div>
            </div>
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
                    <span class="font-medium text-gray-900">{{ $inventaire->date_debut ? $inventaire->date_debut->format('d/m/Y') : 'N/A' }}</span>
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

    {{-- Section alertes améliorée avec catégories collapsibles --}}
    @if($this->totalAlertes > 0)
        @php $alertes = $this->alertes; @endphp
        <div x-data="{ expanded: false }" class="bg-white rounded-lg shadow-sm border border-red-200 mb-6 overflow-hidden">
            {{-- Header cliquable --}}
            <button 
                @click="expanded = !expanded"
                class="w-full flex items-center justify-between p-4 hover:bg-red-50/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-sm font-semibold text-red-800">{{ $this->totalAlertes }} alerte(s) détectée(s)</h3>
                        <p class="text-xs text-red-600 mt-0.5">Cliquez pour voir le détail des alertes</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Badges résumé --}}
                    <div class="hidden sm:flex items-center gap-2">
                        @if(count($alertes['localisations_non_assignees']) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ count($alertes['localisations_non_assignees']) }} non assignée(s)</span>
                        @endif
                        @if(count($alertes['localisations_bloquees']) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ count($alertes['localisations_bloquees']) }} bloquée(s)</span>
                        @endif
                        @if(count($alertes['biens_absents_valeur_haute']) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ count($alertes['biens_absents_valeur_haute']) }} absent(s) critique(s)</span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-red-400 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            {{-- Contenu collapsible --}}
            <div x-show="expanded" x-collapse x-cloak>
                <div class="border-t border-red-100 divide-y divide-red-50">
                    {{-- Localisations non assignées --}}
                    @if(count($alertes['localisations_non_assignees']) > 0)
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                                <h4 class="text-sm font-semibold text-gray-800">Localisations non assignées ({{ count($alertes['localisations_non_assignees']) }})</h4>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($alertes['localisations_non_assignees'] as $alerte)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $alerte['code'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Localisations non démarrées --}}
                    @if(count($alertes['localisations_non_demarrees']) > 0)
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                <h4 class="text-sm font-semibold text-gray-800">Localisations non démarrées ({{ count($alertes['localisations_non_demarrees']) }})</h4>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($alertes['localisations_non_demarrees'], 0, 15) as $alerte)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ $alerte['code'] }}
                                    </span>
                                @endforeach
                                @if(count($alertes['localisations_non_demarrees']) > 15)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-500">
                                        +{{ count($alertes['localisations_non_demarrees']) - 15 }} autre(s)
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Localisations bloquées --}}
                    @if(count($alertes['localisations_bloquees']) > 0)
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" /></svg>
                                <h4 class="text-sm font-semibold text-gray-800">Localisations bloquées ({{ count($alertes['localisations_bloquees']) }})</h4>
                            </div>
                            <div class="space-y-1.5">
                                @foreach($alertes['localisations_bloquees'] as $alerte)
                                    <div class="flex items-center justify-between bg-red-50 rounded-md px-3 py-1.5">
                                        <span class="text-sm font-medium text-red-800">{{ $alerte['code'] }}</span>
                                        <span class="text-xs text-red-600">Inactif depuis {{ $alerte['jours'] }} jour(s)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Biens absents de valeur élevée --}}
                    @if(count($alertes['biens_absents_valeur_haute']) > 0)
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" /><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" /></svg>
                                <h4 class="text-sm font-semibold text-gray-800">Immobilisations absentes de valeur élevée ({{ count($alertes['biens_absents_valeur_haute']) }})</h4>
                            </div>
                            <div class="space-y-1.5">
                                @foreach($alertes['biens_absents_valeur_haute'] as $alerte)
                                    <div class="flex items-center justify-between bg-red-50 rounded-md px-3 py-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono text-red-700">{{ $alerte['code'] }}</span>
                                            <span class="text-sm text-red-800">{{ $alerte['designation'] }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-red-700">{{ number_format($alerte['valeur'], 0, ',', ' ') }} MRU</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Biens défectueux --}}
                    @if(count($alertes['biens_defectueux'] ?? []) > 0)
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <h4 class="text-sm font-semibold text-gray-800">Immobilisations défectueuses ({{ count($alertes['biens_defectueux']) }})</h4>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($alertes['biens_defectueux'], 0, 10) as $alerte)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200" title="{{ $alerte['designation'] }}">
                                        {{ $alerte['code'] }}
                                    </span>
                                @endforeach
                                @if(count($alertes['biens_defectueux']) > 10)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-orange-50 text-orange-500">
                                        +{{ count($alertes['biens_defectueux']) - 10 }} autre(s)
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Section localisations + derniers scans (layout côte à côte) --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        {{-- Tableau localisations (3/4 de la largeur) --}}
        <div class="lg:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-semibold text-gray-900">État par localisation</h2>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $this->inventaireLocalisations->count() }} résultat(s)</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text"
                                wire:model.live.debounce.300ms="searchLoc"
                                placeholder="Rechercher une localisation..."
                                class="block w-full pl-9 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @if($searchLoc)
                                <button wire:click="$set('searchLoc', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            @endif
                        </div>
                        <select 
                            wire:model.live="filterStatutLoc"
                            class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="all">Tous les statuts</option>
                            <option value="en_attente">En attente</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminées</option>
                        </select>
                        <select 
                            wire:model.live="filterAgent"
                            class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="all">Tous les agents</option>
                            @foreach($this->agents as $agent)
                                <option value="{{ $agent->idUser }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto relative">
                {{-- Loading overlay pour le tableau --}}
                <div wire:loading.flex wire:target="searchLoc, filterStatutLoc, filterAgent, sortBy, reassignerLocalisation" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 items-center justify-center">
                    <div class="flex items-center gap-2 text-indigo-600 text-sm font-medium">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Chargement...
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sortBy('code')">
                                <div class="flex items-center gap-1">
                                    Code
                                    @if($sortField === 'code')
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biens</th>
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
                                $progression = $invLoc->nombre_biens_attendus > 0 
                                    ? round(($invLoc->nombre_biens_scannes / $invLoc->nombre_biens_attendus) * 100, 1) 
                                    : 0;
                                $conformite = $invLoc->taux_conformite;
                                $progressionColor = $progression >= 100 ? 'bg-green-500' : ($progression >= 50 ? 'bg-indigo-600' : 'bg-indigo-400');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $invLoc->localisation->CodeLocalisation ?? $invLoc->localisation->Localisation ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 max-w-[200px] truncate" title="{{ $invLoc->localisation->Localisation ?? 'N/A' }}">{{ $invLoc->localisation->Localisation ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(isset($statutsLoc[$invLoc->statut]))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutsLoc[$invLoc->statut]['color'] }}">
                                            {{ $statutsLoc[$invLoc->statut]['label'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="font-medium text-gray-900">{{ $invLoc->nombre_biens_scannes }}</span><span class="text-gray-400">/{{ $invLoc->nombre_biens_attendus }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div 
                                                class="{{ $progressionColor }} h-2 rounded-full transition-all duration-500"
                                                style="width: {{ min($progression, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium {{ $progression >= 100 ? 'text-green-600' : 'text-gray-600' }}">{{ $progression }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php $conformiteColor = $conformite >= 90 ? 'text-green-600' : ($conformite >= 70 ? 'text-yellow-600' : 'text-red-600'); @endphp
                                    <span class="text-sm font-medium {{ $conformiteColor }}">{{ round($conformite, 1) }}%</span>
                                </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($invLoc->agent)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-xs font-medium text-indigo-600">{{ substr($invLoc->agent->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $invLoc->agent->name }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-sm text-amber-600 bg-amber-50 px-2 py-1 rounded">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Non assigné
                                        </span>
                                    @endif
                                    @if($isAdmin)
                                        <div x-data="{ open: false }" class="relative">
                                            <button 
                                                @click="open = !open"
                                                class="p-1 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Réassigner l'agent">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <div 
                                                x-show="open"
                                                @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                x-cloak
                                                class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                                <p class="px-3 py-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigner à</p>
                                                <div class="border-t border-gray-100"></div>
                                                @foreach($this->agents as $agent)
                                                    <button 
                                                        wire:click="reassignerLocalisation({{ $invLoc->id }}, {{ $agent->idUser }})"
                                                        @click="open = false"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-2 transition-colors {{ $invLoc->user_id == $agent->idUser ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                                                        <div class="flex-shrink-0 h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span class="text-xs font-medium text-indigo-600">{{ substr($agent->name, 0, 1) }}</span>
                                                        </div>
                                                        {{ $agent->name }}
                                                        @if($invLoc->user_id == $agent->idUser)
                                                            <svg class="w-4 h-4 ml-auto text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                                        @endif
                                                    </button>
                                                @endforeach
                                                @if($invLoc->agent)
                                                    <div class="border-t border-gray-100 mt-1"></div>
                                                    <button 
                                                        wire:click="reassignerLocalisation({{ $invLoc->id }}, '')"
                                                        @click="open = false"
                                                        class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                        Retirer l'assignation
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                @if($invLoc->date_debut_scan && $invLoc->date_fin_scan)
                                    <span class="font-medium text-gray-700">{{ round($invLoc->date_debut_scan->diffInHours($invLoc->date_fin_scan), 1) }}h</span>
                                @elseif($invLoc->date_debut_scan)
                                    <span class="inline-flex items-center gap-1 text-blue-600">
                                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span></span>
                                        En cours
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a 
                                    href="{{ route('localisations.show', $invLoc->localisation) }}"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 transition-colors"
                                    title="Voir la localisation">
                                    <span>Voir</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-500">Aucune localisation trouvée</p>
                                @if($searchLoc || $filterStatutLoc !== 'all' || $filterAgent !== 'all')
                                    <p class="text-xs text-gray-400 mt-1">Essayez de modifier vos filtres de recherche</p>
                                    <button wire:click="$set('searchLoc', ''); $set('filterStatutLoc', 'all'); $set('filterAgent', 'all')" class="mt-3 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        Réinitialiser les filtres
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>

        {{-- Panneau derniers scans (sidebar droite) --}}
        <div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Activité récente</h3>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                        Live
                    </span>
                </div>
            </div>
            <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                @forelse($this->derniersScans as $scan)
                    <div class="p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-2.5">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="h-7 w-7 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-indigo-600">{{ substr($scan->agent->name ?? 'N', 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $scan->agent->name ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400 whitespace-nowrap">{{ $scan->date_scan->diffForHumans(short: true) }}</p>
                                </div>
                                <p class="text-xs text-gray-600 mt-0.5 truncate" title="{{ $scan->code_inventaire ?? 'N/A' }} - {{ $scan->designation }}">
                                    <span class="font-mono font-medium text-gray-700">{{ $scan->code_inventaire ?? ($scan->gesimmo ? 'GS' . $scan->gesimmo->NumOrdre : 'N/A') }}</span>
                                </p>
                                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                    @if(isset($statutsScan[$scan->statut_scan]))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statutsScan[$scan->statut_scan]['color'] }}">
                                            {{ $statutsScan[$scan->statut_scan]['label'] }}
                                        </span>
                                    @endif
                                    @php $etatKey = $scan->etat_constate ?? 'bon'; $etatStyle = $etatsConstate[$etatKey] ?? $etatsConstate['bon']; @endphp
                                    @if($etatKey === 'mauvais')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $etatStyle['color'] }}">
                                            {{ $scan->etat_constate_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-500">Aucun scan enregistré</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Scripts pour les graphiques Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Animation subtile pour les mises à jour de valeurs */
        @keyframes value-flash {
            0% { background-color: rgba(99, 102, 241, 0); }
            30% { background-color: rgba(99, 102, 241, 0.08); }
            100% { background-color: rgba(99, 102, 241, 0); }
        }
        .value-updated { animation: value-flash 1.2s ease-out; }
    </style>
    <script>
        // Gestionnaire de graphiques avec support du re-rendering Livewire
        (function() {
            let chartScans = null;
            let chartProgLoc = null;
            let chartTemp = null;
            
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
                    ctx.font = 'bold 28px system-ui, sans-serif';
                    ctx.fillStyle = '#1f2937';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(total.toLocaleString('fr-FR'), centerX, centerY - 10);
                    
                    ctx.font = '13px system-ui, sans-serif';
                    ctx.fillStyle = '#6b7280';
                    ctx.fillText('Total scanné', centerX, centerY + 15);
                    ctx.restore();
                }
            };
            
            // Enregistrer le plugin une seule fois
            if (!Chart.registry.plugins.get('centerText')) {
                Chart.register(centerTextPlugin);
            }
            
            function destroyCharts() {
                if (chartScans) { chartScans.destroy(); chartScans = null; }
                if (chartProgLoc) { chartProgLoc.destroy(); chartProgLoc = null; }
                if (chartTemp) { chartTemp.destroy(); chartTemp = null; }
            }
            
            function initCharts() {
                destroyCharts();
                
                // Graphique 1 : Répartition statuts scans (Doughnut)
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
                                data: [stats.biens_presents, stats.biens_deplaces, stats.biens_absents, stats.biens_deteriores, biensDefectueux],
                                backgroundColor: ['#22c55e', '#eab308', '#ef4444', '#f97316', '#d97706'],
                                borderColor: '#ffffff',
                                borderWidth: 3,
                                hoverBorderWidth: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            animation: { animateRotate: true, duration: 800 },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { padding: 15, font: { size: 12, weight: '500' }, usePointStyle: true, pointStyle: 'circle' }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 14,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    callbacks: {
                                        title: (ctx) => ctx[0].label.split(' (')[0],
                                        label: (ctx) => {
                                            const value = ctx.parsed || 0;
                                            const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return ['Quantité: ' + value + ' immobilisation(s)', 'Pourcentage: ' + pct + '%'];
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Graphique 2 : Progression par localisation (Bar Chart)
                const ctxProgLoc = document.getElementById('chart-progression-loc');
                if (ctxProgLoc) {
                    const invLocs = @json($this->inventaireLocalisations->take(10));
                    const progressionData = invLocs.map(loc => {
                        const t = loc.nombre_biens_attendus || 1;
                        return Math.round((loc.nombre_biens_scannes / t) * 100);
                    });
                    
                    chartProgLoc = new Chart(ctxProgLoc, {
                        type: 'bar',
                        data: {
                            labels: invLocs.map(loc => {
                                const code = loc.localisation?.code_localisation || loc.localisation?.localisation || '';
                                return code.length > 15 ? code.substring(0, 15) + '...' : code;
                            }),
                            datasets: [{
                                label: 'Progression (%)',
                                data: progressionData,
                                backgroundColor: invLocs.map(loc => loc.statut === 'termine' ? '#22c55e' : (loc.statut === 'en_cours' ? '#3b82f6' : '#9ca3af')),
                                borderColor: invLocs.map(loc => loc.statut === 'termine' ? '#16a34a' : (loc.statut === 'en_cours' ? '#2563eb' : '#6b7280')),
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 600 },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    callbacks: {
                                        title: (ctx) => {
                                            const loc = invLocs[ctx[0].dataIndex];
                                            return loc.localisation?.localisation || loc.localisation?.code_localisation || '';
                                        },
                                        label: (ctx) => {
                                            const loc = invLocs[ctx.dataIndex];
                                            return [
                                                'Progression: ' + progressionData[ctx.dataIndex] + '%',
                                                'Scanné: ' + (loc.nombre_biens_scannes || 0) + ' / ' + (loc.nombre_biens_attendus || 0),
                                                'Statut: ' + (loc.statut === 'termine' ? 'Terminée' : (loc.statut === 'en_cours' ? 'En cours' : 'En attente'))
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%', font: { size: 11 }, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false } },
                                y: { ticks: { font: { size: 11, weight: '500' }, color: '#374151' }, grid: { display: false, drawBorder: false } }
                            }
                        }
                    });
                }

                // Graphique 3 : Progression temporelle (Line Chart)
                const ctxTemp = document.getElementById('chart-progression-temporelle');
                if (ctxTemp) {
                    const scans = @json($this->scansGraphData);
                    const objectif = {{ $this->statistiques['total_biens_attendus'] }};
                    
                    const scansParDate = {};
                    scans.forEach(scan => {
                        const date = new Date(scan.date_scan).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        scansParDate[date] = (scansParDate[date] || 0) + 1;
                    });
                    
                    const dates = Object.keys(scansParDate).sort((a, b) => new Date(a.split('/').reverse().join('-')) - new Date(b.split('/').reverse().join('-')));
                    const quotidien = dates.map(d => scansParDate[d]);
                    const cumulatif = [];
                    let cumul = 0;
                    quotidien.forEach(qty => { cumul += qty; cumulatif.push(cumul); });

                    chartTemp = new Chart(ctxTemp, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Cumul des scans',
                                data: cumulatif,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 3, tension: 0.4, fill: true,
                                pointRadius: dates.length > 30 ? 0 : 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#fff', pointBorderWidth: 2,
                            }, {
                                label: 'Objectif (' + objectif.toLocaleString('fr-FR') + ')',
                                data: new Array(dates.length).fill(objectif),
                                borderColor: '#9ca3af', borderWidth: 2, borderDash: [8, 4],
                                fill: false, pointRadius: 0, pointHoverRadius: 0,
                            }, {
                                label: 'Scans quotidiens',
                                data: quotidien,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2, tension: 0.3, fill: false,
                                pointRadius: dates.length > 30 ? 0 : 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#fff', pointBorderWidth: 2,
                                yAxisID: 'y1',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 600 },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'top', labels: { padding: 15, font: { size: 12, weight: '500' }, usePointStyle: true } },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 14,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    callbacks: {
                                        label: (ctx) => {
                                            const label = ctx.dataset.label || '';
                                            const value = ctx.parsed.y || 0;
                                            if (ctx.datasetIndex === 0) {
                                                const pct = objectif > 0 ? ((value / objectif) * 100).toFixed(1) : 0;
                                                return label + ': ' + value.toLocaleString('fr-FR') + ' (' + pct + '%)';
                                            } else if (ctx.datasetIndex === 1) {
                                                return label;
                                            }
                                            return label + ': ' + value + ' scan(s)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }, ticks: { font: { size: 11 }, color: '#6b7280', maxRotation: 45, minRotation: 45, autoSkip: true, maxTicksLimit: 15 } },
                                y: {
                                    beginAtZero: true,
                                    max: cumulatif.length > 0 ? Math.max(objectif, Math.max(...cumulatif)) * 1.1 : objectif * 1.1,
                                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                                    ticks: { font: { size: 11 }, color: '#6b7280', callback: v => v.toLocaleString('fr-FR') }
                                },
                                y1: { type: 'linear', display: true, position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, ticks: { font: { size: 11 }, color: '#10b981' } }
                            }
                        }
                    });
                }
            }
            
            // Initialiser les graphiques au chargement
            document.addEventListener('DOMContentLoaded', initCharts);
            
            // Recréer les graphiques après chaque mise à jour Livewire
            document.addEventListener('livewire:navigated', initCharts);
            
            // Écouter le morph de Livewire pour recréer les graphiques après polling
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', ({ el }) => {
                    if (el.id === 'chart-scans' || el.id === 'chart-progression-loc' || el.id === 'chart-progression-temporelle') {
                        setTimeout(initCharts, 100);
                    }
                });
                
                Livewire.on('statistiques-updated', () => {
                    setTimeout(initCharts, 200);
                });
            } else {
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('morph.updated', ({ el }) => {
                        if (el.id === 'chart-scans' || el.id === 'chart-progression-loc' || el.id === 'chart-progression-temporelle') {
                            setTimeout(initCharts, 100);
                        }
                    });
                    
                    Livewire.on('statistiques-updated', () => {
                        setTimeout(initCharts, 200);
                    });
                });
            }
        })();
    </script>

    {{-- Messages flash (stack vertical pour éviter le chevauchement) --}}
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-3 items-end">
        @if(session()->has('success'))
            <div 
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg max-w-md">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="ml-2 text-green-200 hover:text-white flex-shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        @endif

        @if(session()->has('error'))
            <div 
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 6000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="flex items-center gap-3 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg max-w-md">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="ml-2 text-red-200 hover:text-white flex-shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        @endif
    </div>
</div>

