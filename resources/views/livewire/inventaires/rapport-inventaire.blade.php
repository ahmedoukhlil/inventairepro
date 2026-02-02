<div x-data="{ activeTab: @entangle('activeTab'), sousOnglet: 'presents' }" class="space-y-6">
    @php
        $statutsInventaire = [
            'termine' => ['label' => 'Terminé', 'color' => 'bg-orange-100 text-orange-800'],
            'cloture' => ['label' => 'Clôturé', 'color' => 'bg-green-100 text-green-800'],
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
            'moyen' => ['label' => 'Bon état', 'color' => 'bg-blue-100 text-blue-800'],
            'mauvais' => ['label' => 'Défectueuse', 'color' => 'bg-amber-100 text-amber-800'],
        ];
        $conformiteInterpretation = function($taux) {
            if ($taux >= 95) return ['label' => 'Excellent', 'color' => 'text-green-600', 'bg' => 'bg-green-50'];
            if ($taux >= 85) return ['label' => 'Bon', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'];
            if ($taux >= 70) return ['label' => 'Moyen', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50'];
            return ['label' => 'Insuffisant', 'color' => 'text-red-600', 'bg' => 'bg-red-50'];
        };
        $interpretation = $conformiteInterpretation($this->statistiques['taux_conformite']);
    @endphp

    {{-- Header simplifié --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Rapport Inventaire {{ $inventaire->annee }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statutsInventaire[$inventaire->statut]['color'] }}">
                        {{ $statutsInventaire[$inventaire->statut]['label'] }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    Du {{ $inventaire->date_debut->format('d/m/Y') }}
                    @if($inventaire->date_fin)
                        au {{ $inventaire->date_fin->format('d/m/Y') }}
                    @endif
                    • {{ $this->statistiques['duree_jours'] }} jour(s)
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <button 
                    wire:click="exportPDF"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDF
                </button>
                <button 
                    wire:click="exportExcel"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </button>
                <button 
                    onclick="window.print()"
                    class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>
    </div>

    {{-- Section statistiques pertinentes --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        {{-- Taux de conformité --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 {{ $interpretation['bg'] }}">
            <h3 class="text-xs font-medium text-gray-600 mb-1">Taux de conformité</h3>
            <p class="text-2xl font-bold {{ $interpretation['color'] }}">{{ round($this->statistiques['taux_conformite'], 1) }}%</p>
            <p class="text-xs text-gray-500 mt-1">{{ $interpretation['label'] }}</p>
        </div>

        {{-- Taux de couverture --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h3 class="text-xs font-medium text-gray-600 mb-1">Taux de couverture</h3>
            <p class="text-2xl font-bold text-indigo-600">{{ $this->statistiques['taux_couverture'] ?? 0 }}%</p>
            <p class="text-xs text-gray-500 mt-1">{{ $this->statistiques['total_biens_scannes'] }}/{{ $this->statistiques['total_biens_attendus'] }} scannés</p>
        </div>

        {{-- Biens non scannés --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 {{ ($this->statistiques['biens_non_scannes'] ?? 0) > 0 ? 'border-amber-200 bg-amber-50' : '' }}">
            <h3 class="text-xs font-medium text-gray-600 mb-1">Non scannés</h3>
            <p class="text-2xl font-bold {{ ($this->statistiques['biens_non_scannes'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-600' }}">
                {{ $this->statistiques['biens_non_scannes'] ?? 0 }}
            </p>
            <p class="text-xs text-gray-500 mt-1">manquants</p>
        </div>

        {{-- Agents --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h3 class="text-xs font-medium text-gray-600 mb-1">Agents</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $this->statistiques['nombre_agents'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">ayant participé</p>
        </div>
    </div>

    {{-- Répartition par état physique (Neuf, Bon état, Défectueuse) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Répartition par état physique</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-xs text-green-700 font-medium mb-1">Neuf</p>
                <p class="text-2xl font-bold text-green-700">{{ $this->statistiques['biens_neufs'] ?? 0 }}</p>
                <p class="text-xs text-green-600 mt-1">{{ $this->statistiques['total_biens_scannes'] > 0 ? round((($this->statistiques['biens_neufs'] ?? 0) / $this->statistiques['total_biens_scannes']) * 100, 1) : 0 }}%</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-xs text-blue-700 font-medium mb-1">Bon état</p>
                <p class="text-2xl font-bold text-blue-700">{{ $this->statistiques['biens_bon_etat'] ?? 0 }}</p>
                <p class="text-xs text-blue-600 mt-1">{{ $this->statistiques['total_biens_scannes'] > 0 ? round((($this->statistiques['biens_bon_etat'] ?? 0) / $this->statistiques['total_biens_scannes']) * 100, 1) : 0 }}%</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                <p class="text-xs text-amber-700 font-medium mb-1">Défectueuse</p>
                <p class="text-2xl font-bold text-amber-700">{{ $this->statistiques['biens_defectueux'] ?? 0 }}</p>
                <p class="text-xs text-amber-600 mt-1">{{ $this->statistiques['total_biens_scannes'] > 0 ? round((($this->statistiques['biens_defectueux'] ?? 0) / $this->statistiques['total_biens_scannes']) * 100, 1) : 0 }}%</p>
            </div>
        </div>
    </div>

    {{-- Navigation onglets simplifiée --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 px-6 overflow-x-auto" aria-label="Tabs">
                <button 
                    @click="activeTab = 'resume'; $wire.setActiveTab('resume')"
                    :class="activeTab === 'resume' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Résumé
                </button>
                <button 
                    @click="activeTab = 'localisations'; $wire.setActiveTab('localisations')"
                    :class="activeTab === 'localisations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Par localisation
                </button>
                <button 
                    @click="activeTab = 'biens'; $wire.setActiveTab('biens')"
                    :class="activeTab === 'biens' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Immobilisations
                </button>
                <button 
                    @click="activeTab = 'anomalies'; $wire.setActiveTab('anomalies')"
                    :class="activeTab === 'anomalies' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Anomalies
                </button>
            </nav>
        </div>

        {{-- Contenu des onglets --}}
        <div class="p-6">
            {{-- ONGLET Résumé --}}
            <div x-show="activeTab === 'resume'" x-transition class="space-y-6">
                {{-- Indicateurs clés --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-1">Taux d'absence</p>
                        <p class="text-xl font-bold {{ ($this->statistiques['taux_absence'] ?? 0) > 10 ? 'text-red-600' : 'text-gray-900' }}">{{ $this->statistiques['taux_absence'] ?? 0 }}%</p>
                        <p class="text-xs text-gray-500">{{ $this->statistiques['biens_absents'] }} biens absents</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-1">Taux d'anomalies</p>
                        <p class="text-xl font-bold {{ ($this->statistiques['taux_anomalies'] ?? 0) > 15 ? 'text-orange-600' : 'text-gray-900' }}">{{ $this->statistiques['taux_anomalies'] ?? 0 }}%</p>
                        <p class="text-xs text-gray-500">déplacés + absents + défectueux</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-1">Progression</p>
                        <p class="text-xl font-bold text-indigo-600">{{ $this->statistiques['progression_globale'] ?? 0 }}%</p>
                        <p class="text-xs text-gray-500">{{ $this->statistiques['localisations_terminees'] }}/{{ $this->statistiques['total_localisations'] }} loc. terminées</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-1">Durée</p>
                        <p class="text-xl font-bold text-gray-900">{{ $this->statistiques['duree_jours'] ?? 0 }} jour(s)</p>
                        <p class="text-xs text-gray-500">inventaire</p>
                    </div>
                </div>

                {{-- Répartition par statut de scan --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-xs text-green-700 font-medium mb-1">Présents</p>
                        <p class="text-2xl font-bold text-green-700">{{ $this->statistiques['biens_presents'] }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <p class="text-xs text-yellow-700 font-medium mb-1">Déplacés</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $this->statistiques['biens_deplaces'] }}</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <p class="text-xs text-red-700 font-medium mb-1">Absents</p>
                        <p class="text-2xl font-bold text-red-700">{{ $this->statistiques['biens_absents'] }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <p class="text-xs text-orange-700 font-medium mb-1">Détériorés</p>
                        <p class="text-2xl font-bold text-orange-700">{{ $this->statistiques['biens_deteriores'] }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                        <p class="text-xs text-amber-700 font-medium mb-1">Défectueux</p>
                        <p class="text-2xl font-bold text-amber-700">{{ $this->statistiques['biens_defectueux'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- Contribution par agent --}}
                @if(count($this->statistiques['par_agent'] ?? []) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contribution par agent</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisations</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biens scannés</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">% du total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->statistiques['par_agent'] as $agent)
                                    @php $pct = $this->statistiques['total_biens_scannes'] > 0 ? round(($agent['biens_scannes'] / $this->statistiques['total_biens_scannes']) * 100, 1) : 0; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $agent['agent_name'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $agent['localisations'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $agent['biens_scannes'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $pct }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Performance par localisation (tableau simplifié) --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance par localisation</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scannés</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Présents</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(collect($this->statistiques['par_localisation'])->sortByDesc('taux_conformite')->take(10) as $loc)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loc['code'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $loc['biens_scannes'] }}/{{ $loc['biens_attendus'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 font-medium">{{ $loc['biens_presents'] ?? 0 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-sm font-medium {{ $loc['taux_conformite'] >= 90 ? 'text-green-600' : ($loc['taux_conformite'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ round($loc['taux_conformite'], 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ONGLET Par localisation --}}
            <div x-show="activeTab === 'localisations'" x-transition style="display: none;" class="space-y-6">
                <div class="mb-4">
                    <select 
                        wire:model.live="filterLocalisation"
                        class="block w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="all">Toutes les localisations</option>
                        @foreach($inventaire->inventaireLocalisations as $invLoc)
                            <option value="{{ $invLoc->localisation_id }}">{{ $invLoc->localisation->code }}</option>
                        @endforeach
                    </select>
                </div>

                @foreach($inventaire->inventaireLocalisations as $invLoc)
                    @if($filterLocalisation === 'all' || $filterLocalisation == $invLoc->localisation_id)
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $invLoc->localisation->code }}</h3>
                                    <p class="text-sm text-gray-600">{{ $invLoc->localisation->designation }}</p>
                                </div>
                                <span class="text-sm font-medium {{ $invLoc->taux_conformite >= 90 ? 'text-green-600' : ($invLoc->taux_conformite >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ round($invLoc->taux_conformite, 1) }}%
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $invLoc->nombre_biens_scannes }}/{{ $invLoc->nombre_biens_attendus }} immobilisations scannées
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- ONGLET Immobilisations (regroupé) --}}
            <div x-show="activeTab === 'biens'" x-transition style="display: none;" class="space-y-6">
                {{-- Sous-onglets pour les biens --}}
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-4">
                        <button 
                            @click="sousOnglet = 'presents'"
                            :class="sousOnglet === 'presents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-2 px-1 border-b-2 font-medium text-sm">
                            Présents ({{ count($this->biensPresents) }})
                        </button>
                        <button 
                            @click="sousOnglet = 'deplaces'"
                            :class="sousOnglet === 'deplaces' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-2 px-1 border-b-2 font-medium text-sm">
                            Déplacés ({{ count($this->biensDeplaces) }})
                        </button>
                        <button 
                            @click="sousOnglet = 'absents'"
                            :class="sousOnglet === 'absents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-2 px-1 border-b-2 font-medium text-sm">
                            Absents ({{ count($this->biensAbsents) }})
                        </button>
                        <button 
                            @click="sousOnglet = 'defectueux'"
                            :class="sousOnglet === 'defectueux' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-2 px-1 border-b-2 font-medium text-sm">
                            Défectueux ({{ count($this->biensDefectueux) }})
                        </button>
                    </nav>
                </div>

                {{-- Présents --}}
                <div x-show="sousOnglet === 'presents'" x-transition style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->biensPresents->take(50) as $scan)
                                    @php $etatKey = $scan->etat_constate ?? 'bon'; $etatStyle = $etatsConstate[$etatKey] ?? $etatsConstate['bon']; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->designation, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $etatStyle['color'] }}">{{ $scan->etat_constate_label }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->localisation_code ?? ($scan->bien?->localisation?->code ?? 'N/A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($this->biensPresents) > 50)
                        <p class="text-sm text-gray-500 mt-4">Affichage des 50 premiers résultats sur {{ count($this->biensPresents) }}</p>
                    @endif
                </div>

                {{-- Déplacés --}}
                <div x-show="sousOnglet === 'deplaces'" x-transition style="display: none;">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-sm text-yellow-700">
                            <strong>Action suggérée :</strong> Mettre à jour les localisations permanentes des immobilisations déplacées.
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation prévue</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation réelle</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->biensDeplaces->take(50) as $scan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->designation, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-medium">{{ $scan->localisation_code ?? ($scan->bien?->localisation?->code ?? 'N/A') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-orange-600 font-medium">{{ $scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Absents --}}
                <div x-show="sousOnglet === 'absents'" x-transition style="display: none;">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <p class="text-sm font-medium text-red-800">
                            {{ count($this->biensAbsents) }} immobilisation(s) absente(s)
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->biensAbsents->take(50) as $scan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->designation, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->localisation_code ?? ($scan->bien?->localisation?->code ?? 'N/A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Défectueux (signalés via PWA) --}}
                <div x-show="sousOnglet === 'defectueux'" x-transition style="display: none;">
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4">
                        <p class="text-sm text-amber-700">
                            <strong>Immobilisations signalées défectueuses</strong> lors de l'inventaire (via PWA). 3 états : Neuf, Bon état, Défectueuse.
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->biensDefectueux->take(50) as $scan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->designation, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Défectueuse</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? ($scan->localisation_code ?? 'N/A') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            @if($scan->photo_path)
                                                <div x-data="{ open: false }" class="inline">
                                                    <button @click="open = true" type="button" class="flex items-center gap-2 group">
                                                        <img src="{{ asset('storage/' . $scan->photo_path) }}" alt="Photo {{ $scan->code_inventaire }}" class="w-12 h-12 object-cover rounded border border-gray-200 group-hover:border-indigo-400 transition cursor-pointer">
                                                        <span class="text-indigo-600 hover:underline text-sm">Voir</span>
                                                    </button>
                                                    <div x-show="open" x-cloak @click.self="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" x-transition>
                                                        <div class="relative max-w-4xl max-h-[90vh]">
                                                            <img src="{{ asset('storage/' . $scan->photo_path) }}" alt="Photo {{ $scan->code_inventaire }}" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-xl">
                                                            <button @click="open = false" class="absolute -top-10 right-0 text-white hover:text-gray-300 p-2">
                                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                            <p class="text-white text-sm mt-2 text-center">{{ $scan->code_inventaire }} - {{ Str::limit($scan->designation, 40) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($this->biensDefectueux) > 50)
                        <p class="text-sm text-gray-500 mt-4">Affichage des 50 premiers résultats sur {{ count($this->biensDefectueux) }}</p>
                    @endif
                </div>
            </div>

            {{-- ONGLET Anomalies --}}
            <div x-show="activeTab === 'anomalies'" x-transition style="display: none;" class="space-y-6">
                @php $anomalies = $this->anomalies; @endphp

                @if(count($anomalies['localisations_non_demarrees']) > 0 || count($anomalies['taux_absence_eleve']) > 0 || count($anomalies['biens_defectueux'] ?? []) > 0)
                    <div class="space-y-4">
                        @if(count($anomalies['localisations_non_demarrees']) > 0)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <h4 class="font-medium text-yellow-800 mb-2">Localisations non démarrées ({{ count($anomalies['localisations_non_demarrees']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                                    @foreach($anomalies['localisations_non_demarrees'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ $anomalie['designation'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($anomalies['taux_absence_eleve']) > 0)
                            <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
                                <h4 class="font-medium text-orange-800 mb-2">Taux d'absence élevé ({{ count($anomalies['taux_absence_eleve']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-orange-700 space-y-1">
                                    @foreach($anomalies['taux_absence_eleve'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ $anomalie['taux_absence'] }}% absents ({{ $anomalie['biens_absents'] }} immobilisations)</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($anomalies['biens_defectueux'] ?? []) > 0)
                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
                                <h4 class="font-medium text-amber-800 mb-2">Immobilisations signalées défectueuses ({{ count($anomalies['biens_defectueux']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-amber-700 space-y-1">
                                    @foreach($anomalies['biens_defectueux'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ $anomalie['designation'] }} ({{ $anomalie['localisation'] }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium text-gray-900">Aucune anomalie détectée</p>
                        <p class="text-sm text-gray-500 mt-2">Tout semble être en ordre !</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer simplifié --}}
    <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-500 text-center">
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name }}</p>
    </div>

    {{-- Messages flash --}}
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
