<div>
    @php
        $isAdmin = auth()->user()->isAdmin();
        $statuts = [
            'en_preparation' => ['label' => 'En préparation', 'color' => 'bg-gray-100 text-gray-800', 'icon' => 'clock'],
            'en_cours' => ['label' => 'En cours', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'play-circle'],
            'termine' => ['label' => 'Terminé', 'color' => 'bg-orange-100 text-orange-800', 'icon' => 'check-circle'],
            'cloture' => ['label' => 'Clôturé', 'color' => 'bg-green-100 text-green-800', 'icon' => 'lock-closed'],
        ];
    @endphp

    {{-- Auto-refresh si inventaire en cours --}}
    @if($this->inventaireEnCours)
        <div wire:poll.30s></div>
    @endif

    <div class="space-y-6">
        {{-- Header avec titre et statistiques --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Inventaires</h1>
            <p class="mt-1 text-sm text-gray-500">Historique des inventaires annuels</p>
        </div>

        {{-- Statistiques rapides (cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Total inventaires</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistiquesGlobales['total_inventaires'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Inventaire en cours</p>
                @if($this->statistiquesGlobales['inventaire_en_cours'])
                    <p class="text-2xl font-bold text-blue-600 mt-1">
                        {{ $this->statistiquesGlobales['inventaire_en_cours']->annee }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ round($this->statistiquesGlobales['inventaire_en_cours']->progression, 1) }}% complété
                    </p>
                @else
                    <p class="text-lg font-medium text-gray-400 mt-1">Aucun</p>
                @endif
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Taux moyen conformité</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">
                    {{ $this->statistiquesGlobales['taux_moyen_conformite'] }}%
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-500">Dernier clôturé</p>
                @if($this->statistiquesGlobales['dernier_cloture'])
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $this->statistiquesGlobales['dernier_cloture']->annee }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $this->statistiquesGlobales['dernier_cloture']->date_fin->format('d/m/Y') }}
                    </p>
                @else
                    <p class="text-lg font-medium text-gray-400 mt-1">Aucun</p>
                @endif
            </div>
        </div>

        {{-- Cartes statistiques des résultats d'inventaire --}}
        @if($this->statistiquesResultats['nombre_inventaires_termines'] > 0)
        @php $resultats = $this->statistiquesResultats; @endphp
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Résultats des inventaires</h2>
            <p class="text-sm text-gray-500 mb-4">Statistiques agrégées des {{ $resultats['nombre_inventaires_termines'] }} inventaire(s) terminé(s) et clôturé(s)</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 font-medium uppercase">Total scannés</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($resultats['total_scans']) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <p class="text-xs text-green-700 font-medium uppercase">Présents</p>
                    <p class="text-xl font-bold text-green-700 mt-1">{{ number_format($resultats['biens_presents']) }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                    <p class="text-xs text-yellow-700 font-medium uppercase">Déplacés</p>
                    <p class="text-xl font-bold text-yellow-700 mt-1">{{ number_format($resultats['biens_deplaces']) }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <p class="text-xs text-red-700 font-medium uppercase">Absents</p>
                    <p class="text-xl font-bold text-red-700 mt-1">{{ number_format($resultats['biens_absents']) }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                    <p class="text-xs text-amber-700 font-medium uppercase">Défectueux</p>
                    <p class="text-xl font-bold text-amber-700 mt-1">{{ number_format($resultats['biens_defectueux']) }}</p>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                    <p class="text-xs text-indigo-700 font-medium uppercase">Conformité</p>
                    <p class="text-xl font-bold text-indigo-700 mt-1">{{ $resultats['taux_conformite'] }}%</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Bouton principal pour démarrer un inventaire --}}
        @if($isAdmin)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if(!$this->inventaireEnCours)
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Prêt à démarrer un nouvel inventaire ?</h2>
                        <p class="text-sm text-gray-500 mb-4">Aucun inventaire en cours. Vous pouvez créer un nouvel inventaire annuel.</p>
                        <a 
                            href="{{ route('inventaires.create') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Démarrer un nouvel inventaire
                        </a>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Un inventaire est déjà en cours</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">
                                Inventaire {{ $this->inventaireEnCours->annee }} - {{ $statuts[$this->inventaireEnCours->statut]['label'] }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Terminez-le ou clôturez-le avant de pouvoir en démarrer un autre.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a 
                                href="{{ route('inventaires.show', $this->inventaireEnCours) }}"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                Voir l'inventaire en cours
                            </a>
                            <a 
                                href="{{ route('inventaires.create') }}"
                                class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Démarrer un nouvel inventaire
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Carte mise en avant : Inventaire en cours --}}
        @if($this->inventaireEnCours && $this->inventaireEnCours->statut === 'en_cours')
            @php
                $stats = $this->inventaireEnCours->getStatistiques();
                $dernierScan = $this->inventaireEnCours->inventaireScans()
                    ->orderBy('date_scan', 'desc')
                    ->first();
            @endphp
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-lg border-2 border-blue-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-500 text-white animate-pulse">
                            🔴 EN COURS
                        </span>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Inventaire {{ $this->inventaireEnCours->annee }}
                        </h2>
                    </div>
                    <a 
                        href="{{ route('inventaires.show', $this->inventaireEnCours) }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        Accéder au tableau de bord
                    </a>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progression globale</span>
                        <span class="text-sm font-bold text-indigo-600">{{ round($stats['progression'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div 
                            class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                            style="width: {{ $stats['progression'] }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Localisations</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $stats['localisations_terminees'] }}/{{ $stats['total_localisations'] }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Immobilisations scannées</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $stats['total_scans'] }}/{{ $stats['total_localisations'] > 0 ? $stats['total_localisations'] * 10 : 0 }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Présents</p>
                        <p class="text-lg font-bold text-green-600">{{ $stats['scans_presents'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Déplacés</p>
                        <p class="text-lg font-bold text-yellow-600">{{ $stats['scans_deplaces'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Absents</p>
                        <p class="text-lg font-bold text-red-600">{{ $stats['scans_absents'] }}</p>
                    </div>
                </div>

                @if($dernierScan)
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-xs text-gray-500">
                            Dernière activité : Il y a {{ $dernierScan->date_scan->diffForHumans() }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Filtres inline --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select 
                        wire:model.live="filterStatut"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="all">Tous</option>
                        <option value="en_preparation">En préparation</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="cloture">Clôturé</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select 
                        wire:model.live="filterAnnee"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Toutes</option>
                        @foreach($this->annees as $annee)
                            <option value="{{ $annee }}">{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button 
                        wire:click="resetFilters"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        {{-- Tableau des inventaires --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('annee')">
                                <div class="flex items-center">
                                    Année
                                    @if($sortField === 'annee')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date début
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date fin
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durée
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Créé par
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Localisations
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Immobilisations scannées
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Progression
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Conformité
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inventaires as $inventaire)
                            @php
                                $stats = $inventaire->getStatistiques();
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $inventaire->statut === 'en_cours' ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900">{{ $inventaire->annee }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(isset($statuts[$inventaire->statut]))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statuts[$inventaire->statut]['color'] }}">
                                            {{ $statuts[$inventaire->statut]['label'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $inventaire->date_debut ? $inventaire->date_debut->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($inventaire->date_fin)
                                            {{ $inventaire->date_fin->format('d M Y') }}
                                        @else
                                            <span class="text-gray-400 italic">En cours...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($inventaire->duree !== null)
                                            {{ $inventaire->duree }} jour{{ $inventaire->duree > 1 ? 's' : '' }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $inventaire->creator->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $stats['localisations_terminees'] }}/{{ $stats['total_localisations'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $stats['total_scans'] }}/{{ $stats['total_localisations'] > 0 ? $stats['total_localisations'] * 10 : 0 }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div 
                                                class="bg-indigo-600 h-2 rounded-full"
                                                style="width: {{ min($stats['progression'], 100) }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ round($stats['progression'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @php
                                            $conformiteColor = $stats['taux_conformite'] >= 90 ? 'bg-green-100 text-green-800' : ($stats['taux_conformite'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conformiteColor }}">
                                            {{ round($stats['taux_conformite'], 1) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href="{{ route('inventaires.show', $inventaire) }}"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                            title="Voir détails">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if(in_array($inventaire->statut, ['termine', 'cloture']))
                                            <a 
                                                href="{{ route('inventaires.rapport', $inventaire) }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Voir rapport">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </a>
                                        @endif

                                        @if($isAdmin && $inventaire->statut === 'termine')
                                            <button 
                                                wire:click="archiverInventaire({{ $inventaire->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir clôturer cet inventaire ? Cette action est définitive."
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Clôturer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        @endif

                                        @if(in_array($inventaire->statut, ['termine', 'cloture']))
                                            <a 
                                                href="{{ route('inventaires.export-pdf', $inventaire) }}"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Export PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        @endif

                                        @if($isAdmin && in_array($inventaire->statut, ['en_preparation', 'termine', 'cloture']))
                                            <button 
                                                wire:click="supprimerInventaire({{ $inventaire->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer cet inventaire ({{ $inventaire->annee }}) ? Tous les scans et données associés seront définitivement supprimés."
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun inventaire créé</h3>
                                    <p class="mt-1 text-sm text-gray-500 mb-4">
                                        Démarrez votre premier inventaire annuel pour commencer la gestion de votre patrimoine.
                                    </p>
                                    @if($isAdmin)
                                        <a 
                                            href="{{ route('inventaires.create') }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Créer un inventaire
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($inventaires->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 sm:px-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-700">Par page :</label>
                            <select 
                                wire:model.live="perPage"
                                class="block px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div>
                            {{ $inventaires->links() }}
                        </div>
                    </div>
                </div>
            @endif
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

