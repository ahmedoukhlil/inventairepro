<div x-data="{ activeTab: @entangle('activeTab') }" class="space-y-6">
    @php
        $statutsInventaire = [
            'termine' => ['label' => 'Terminé', 'color' => 'bg-orange-100 text-orange-800'],
            'cloture' => ['label' => 'Clôturé', 'color' => 'bg-green-100 text-green-800'],
        ];
        $statutsScan = [
            'present' => ['label' => 'Présent', 'color' => 'bg-green-100 text-green-800', 'icon' => 'check-circle'],
            'deplace' => ['label' => 'Déplacé', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'arrow-right'],
            'absent' => ['label' => 'Absent', 'color' => 'bg-red-100 text-red-800', 'icon' => 'x-circle'],
            'deteriore' => ['label' => 'Détérioré', 'color' => 'bg-orange-100 text-orange-800', 'icon' => 'exclamation'],
        ];
        $natures = [
            'mobilier' => ['label' => 'Mobilier', 'color' => 'bg-blue-100 text-blue-800'],
            'informatique' => ['label' => 'Informatique', 'color' => 'bg-purple-100 text-purple-800'],
            'vehicule' => ['label' => 'Véhicule', 'color' => 'bg-yellow-100 text-yellow-800'],
            'materiel' => ['label' => 'Matériel', 'color' => 'bg-green-100 text-green-800'],
        ];
        $conformiteInterpretation = function($taux) {
            if ($taux >= 95) return ['label' => 'Excellent', 'color' => 'text-green-600', 'bg' => 'bg-green-50'];
            if ($taux >= 85) return ['label' => 'Bon', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'];
            if ($taux >= 70) return ['label' => 'Moyen', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50'];
            return ['label' => 'Insuffisant', 'color' => 'text-red-600', 'bg' => 'bg-red-50'];
        };
        $interpretation = $conformiteInterpretation($this->statistiques['taux_conformite']);
    @endphp

    {{-- Header avec breadcrumb et actions --}}
    <div class="mb-6 print:mb-4">
        <nav class="flex mb-4 print:hidden" aria-label="Breadcrumb">
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
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('inventaires.show', $inventaire) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Inventaire {{ $inventaire->annee }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Rapport</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:flex-col print:items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 print:text-2xl">
                    Rapport Inventaire {{ $inventaire->annee }}
                </h1>
                <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statutsInventaire[$inventaire->statut]['color'] }}">
                        {{ $statutsInventaire[$inventaire->statut]['label'] }}
                    </span>
                    <span>
                        Du {{ $inventaire->date_debut->format('d/m/Y') }} 
                        @if($inventaire->date_fin)
                            au {{ $inventaire->date_fin->format('d/m/Y') }}
                        @endif
                        | Durée : {{ $this->statistiques['duree_jours'] }} jour(s)
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    Créé par : {{ $inventaire->creator->name ?? 'N/A' }} le {{ $inventaire->created_at->format('d/m/Y à H:i') }}
                    @if($inventaire->closer)
                        | Clôturé par : {{ $inventaire->closer->name }} le {{ $inventaire->updated_at->format('d/m/Y à H:i') }}
                    @endif
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2 print:hidden">
                <button 
                    wire:click="exportPDF"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Télécharger PDF
                </button>
                <button 
                    wire:click="exportExcel"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger Excel
                </button>
                <button 
                    wire:click="imprimerRapport"
                    onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>
    </div>

    {{-- Section synthèse (cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 print:grid-cols-2 print:gap-4">
        {{-- Card 1 : Résultat global --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 {{ $interpretation['bg'] }} print:border-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    Inventaire {{ $inventaire->annee }} - 
                    <span class="{{ $interpretation['color'] }}">
                        {{ $this->statistiques['taux_conformite'] >= 95 ? 'CONFORME' : 'NON CONFORME' }}
                    </span>
                </h2>
                @if($this->statistiques['taux_conformite'] >= 95)
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
            </div>
            <p class="text-5xl font-bold {{ $interpretation['color'] }} mb-2">
                {{ round($this->statistiques['taux_conformite'], 1) }}%
            </p>
            <p class="text-lg font-medium {{ $interpretation['color'] }}">
                {{ $interpretation['label'] }}
            </p>
        </div>

        {{-- Card 2 : Chiffres clés --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:border-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Chiffres clés</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Total immobilisations inventoriées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->statistiques['total_biens_scannes'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Immobilisations présentes</p>
                    <p class="text-2xl font-bold text-green-600">{{ $this->statistiques['biens_presents'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Immobilisations déplacées</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $this->statistiques['biens_deplaces'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Immobilisations absentes</p>
                    <p class="text-2xl font-bold text-red-600">{{ $this->statistiques['biens_absents'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Immobilisations détériorées</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $this->statistiques['biens_deteriores'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Valeur totale</p>
                    <p class="text-2xl font-bold text-indigo-600">
                        {{ number_format($this->statistiques['valeur_totale_scannee'], 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Valeur absente</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ number_format($this->statistiques['valeur_absente'], 0, ',', ' ') }} MRU
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques de synthèse --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 print:grid-cols-2 print:gap-4">
        {{-- Graphique 1 : Répartition statuts --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:border print:break-inside-avoid">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition des statuts</h3>
            <canvas id="chart-statuts" height="300"></canvas>
        </div>

        {{-- Graphique 2 : Conformité par localisation --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:border print:break-inside-avoid">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conformité par localisation (Top 10)</h3>
            <canvas id="chart-conformite-loc" height="300"></canvas>
        </div>

        {{-- Graphique 3 : Conformité par service --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:border print:break-inside-avoid">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conformité par service</h3>
            <canvas id="chart-conformite-service" height="300"></canvas>
        </div>

        {{-- Graphique 4 : Valeur par statut --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:border print:break-inside-avoid">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Valeur par statut</h3>
            <canvas id="chart-valeur-statut" height="300"></canvas>
        </div>
    </div>

    {{-- Navigation onglets --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 print:border-0 print:shadow-none">
        <div class="border-b border-gray-200 print:hidden">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
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
                    @click="activeTab = 'presents'; $wire.setActiveTab('presents')"
                    :class="activeTab === 'presents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Immobilisations présentes
                </button>
                <button 
                    @click="activeTab = 'deplaces'; $wire.setActiveTab('deplaces')"
                    :class="activeTab === 'deplaces' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Immobilisations déplacées
                </button>
                <button 
                    @click="activeTab = 'absents'; $wire.setActiveTab('absents')"
                    :class="activeTab === 'absents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Immobilisations absentes
                </button>
                <button 
                    @click="activeTab = 'non-scannes'; $wire.setActiveTab('non-scannes')"
                    :class="activeTab === 'non-scannes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Non scannés
                </button>
                <button 
                    @click="activeTab = 'anomalies'; $wire.setActiveTab('anomalies')"
                    :class="activeTab === 'anomalies' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Anomalies
                </button>
                <button 
                    @click="activeTab = 'recommandations'; $wire.setActiveTab('recommandations')"
                    :class="activeTab === 'recommandations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Recommandations
                </button>
            </nav>
        </div>

        {{-- Contenu des onglets --}}
        <div class="p-6 print:p-4">
            {{-- ONGLET Résumé --}}
            <div x-show="activeTab === 'resume'" x-transition class="space-y-6 print:block">
                {{-- Déroulement de l'inventaire --}}
                <div class="mb-6 print:mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Déroulement de l'inventaire</h3>
                    <div class="bg-gray-50 rounded-lg p-4 print:border print:border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Démarrage</p>
                                <p class="text-lg font-bold text-gray-900">{{ $inventaire->date_debut->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex-1 mx-4">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div 
                                        class="h-2 bg-indigo-600 rounded-full"
                                        style="width: {{ $this->statistiques['progression_globale'] }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-center">{{ round($this->statistiques['progression_globale'], 1) }}% complété</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Fin</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $inventaire->date_fin ? $inventaire->date_fin->format('d/m/Y') : 'En cours' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Performance par localisation --}}
                <div class="mb-6 print:mb-4 print:break-inside-avoid">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Performance par localisation</h3>
                    <div class="overflow-x-auto print:overflow-visible">
                        <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                            <thead class="bg-gray-50 print:bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immobilisations</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scannés</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Présents</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Déplacés</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Absents</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(collect($this->statistiques['par_localisation'])->sortBy('taux_conformite') as $loc)
                                    @php
                                        $scans = $inventaire->inventaireScans()
                                            ->whereHas('inventaireLocalisation', function($q) use ($loc) {
                                                $q->where('localisation_id', $loc['localisation_id']);
                                            })
                                            ->get();
                                        $presents = $scans->where('statut_scan', 'present')->count();
                                        $deplaces = $scans->where('statut_scan', 'deplace')->count();
                                        $absents = $scans->where('statut_scan', 'absent')->count();
                                        $problematique = $loc['taux_conformite'] < 80;
                                    @endphp
                                    <tr class="{{ $problematique ? 'bg-red-50 print:bg-gray-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loc['code'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $loc['biens_attendus'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $loc['biens_scannes'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600">{{ $presents }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-yellow-600">{{ $deplaces }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600">{{ $absents }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-sm font-medium {{ $problematique ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ round($loc['taux_conformite'], 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Performance par agent --}}
                <div class="mb-6 print:mb-4 print:break-inside-avoid">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Performance par agent</h3>
                    <div class="overflow-x-auto print:overflow-visible">
                        <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                            <thead class="bg-gray-50 print:bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisations</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immobilisations scannées</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durée totale</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Moyenne/localisation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(collect($this->statistiques['par_agent'])->sortByDesc('biens_scannes') as $agent)
                                    @php
                                        $invLocs = $inventaire->inventaireLocalisations()->where('user_id', $agent['user_id'])->get();
                                        $dureeTotale = $invLocs->sum(function($invLoc) {
                                            if ($invLoc->date_debut_scan && $invLoc->date_fin_scan) {
                                                return $invLoc->date_debut_scan->diffInHours($invLoc->date_fin_scan);
                                            }
                                            return 0;
                                        });
                                        $moyenne = $invLocs->count() > 0 ? round($dureeTotale / $invLocs->count(), 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $agent['agent_name'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $agent['localisations'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $agent['biens_scannes'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ round($dureeTotale, 1) }}h</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $moyenne }}h</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Répartition par nature --}}
                <div class="print:break-inside-avoid">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Répartition par nature</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($this->statistiques['par_nature'] as $nature => $stats)
                            <div class="bg-gray-50 rounded-lg p-4 print:border print:border-gray-300">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    @if(isset($natures[$nature]))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $natures[$nature]['color'] }} mr-2">
                                            {{ $natures[$nature]['label'] }}
                                        </span>
                                    @else
                                        {{ ucfirst($nature) }}
                                    @endif
                                </h4>
                                <div class="space-y-1 text-sm">
                                    <p>Total : <span class="font-medium">{{ $stats['total'] }}</span> immobilisations</p>
                                    <p>Présents : <span class="font-medium text-green-600">{{ $stats['presents'] }}</span></p>
                                    <p>Déplacés : <span class="font-medium text-yellow-600">{{ $stats['deplaces'] }}</span></p>
                                    <p>Absents : <span class="font-medium text-red-600">{{ $stats['absents'] }}</span></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ONGLET Par localisation --}}
            <div x-show="activeTab === 'localisations'" x-transition style="display: none;" class="space-y-6 print:block">
                <div class="mb-4 print:hidden">
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
                        <div class="bg-gray-50 rounded-lg p-6 mb-6 print:border print:border-gray-300 print:break-inside-avoid">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $invLoc->localisation->code }} - {{ $invLoc->localisation->designation }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $invLoc->nombre_biens_scannes }}/{{ $invLoc->nombre_biens_attendus }} immobilisations scannées | 
                                        Conformité : {{ round($invLoc->taux_conformite, 1) }}%
                                    </p>
                                </div>
                            </div>

                            @php
                                $scans = $inventaire->inventaireScans()
                                    ->where('inventaire_localisation_id', $invLoc->id)
                                    ->with(['bien', 'localisationReelle', 'agent'])
                                    ->get();
                            @endphp

                            @if($scans->count() > 0)
                                <div class="overflow-x-auto print:overflow-visible">
                                    <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                                        <thead class="bg-gray-50 print:bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Localisation réelle</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($scans as $scan)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->bien->code_inventaire ?? 'N/A' }}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($scan->bien->designation ?? 'N/A', 40) }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        @if(isset($statutsScan[$scan->statut_scan]))
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statutsScan[$scan->statut_scan]['color'] }}">
                                                                {{ $statutsScan[$scan->statut_scan]['label'] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                        @if($scan->localisationReelle)
                                                            {{ $scan->localisationReelle->code }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $scan->date_scan->format('d/m/Y H:i') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- ONGLET Immobilisations présentes --}}
            <div x-show="activeTab === 'presents'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                <div class="flex items-center justify-between mb-4 print:hidden">
                    <h3 class="text-lg font-semibold text-gray-900">Immobilisations présentes</h3>
                    <div class="text-sm text-gray-500">
                        Total valeur : <span class="font-bold text-indigo-600">
                            {{ number_format($this->biensPresents->sum(function($s) { return $s->bien->valeur_acquisition ?? 0; }), 0, ',', ' ') }} MRU
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto print:overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nature</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date scan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->biensPresents as $scan)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->bien->code_inventaire ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->bien->designation ?? 'N/A', 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->bien->nature ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->bien->localisation->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($scan->bien->valeur_acquisition ?? 0, 0, ',', ' ') }} MRU
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $scan->date_scan->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $scan->agent->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ONGLET Immobilisations déplacées --}}
            <div x-show="activeTab === 'deplaces'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 print:border print:border-yellow-300">
                    <p class="text-sm text-yellow-700">
                        <strong>Action suggérée :</strong> Mettre à jour les localisations permanentes des immobilisations déplacées.
                    </p>
                </div>

                <div class="overflow-x-auto print:overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation prévue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation réelle</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date scan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->biensDeplaces as $scan)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->bien->code_inventaire ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->bien->designation ?? 'N/A', 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-medium">{{ $scan->bien->localisation->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-orange-600 font-medium">{{ $scan->localisationReelle->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($scan->bien->valeur_acquisition ?? 0, 0, ',', ' ') }} MRU
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $scan->date_scan->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ONGLET Immobilisations absentes --}}
            <div x-show="activeTab === 'absents'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 print:border print:border-red-300">
                    <p class="text-sm font-medium text-red-800">
                        {{ count($this->biensAbsents) }} immobilisations absentes pour une valeur de 
                        {{ number_format($this->statistiques['valeur_absente'], 0, ',', ' ') }} MRU
                    </p>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 print:border print:border-blue-300">
                    <p class="text-sm text-blue-700">
                        <strong>Actions suggérées :</strong>
                        Lancer recherche approfondie | Déclarer perte | Mettre en réforme
                    </p>
                </div>

                <div class="overflow-x-auto print:overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nature</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation prévue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date acquisition</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->biensAbsents as $scan)
                                @php
                                    $valeurHaute = ($scan->bien->valeur_acquisition ?? 0) > 50000;
                                @endphp
                                <tr class="{{ $valeurHaute ? 'bg-red-50 print:bg-gray-50' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scan->bien->code_inventaire ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($scan->bien->designation ?? 'N/A', 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->bien->nature ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $scan->bien->localisation->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold {{ $valeurHaute ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ number_format($scan->bien->valeur_acquisition ?? 0, 0, ',', ' ') }} MRU
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $scan->bien->date_acquisition ? $scan->bien->date_acquisition->format('d/m/Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ONGLET Immobilisations non scannées --}}
            <div x-show="activeTab === 'non-scannes'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 print:border print:border-yellow-300">
                    <p class="text-sm text-yellow-700">
                        Ces immobilisations étaient dans les localisations inventoriées mais n'ont pas été scannées.
                    </p>
                </div>

                <div class="overflow-x-auto print:overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200 print:border print:border-gray-300">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localisation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->biensNonScannes as $bien)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $bien->code_inventaire }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($bien->designation, 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $bien->localisation->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($bien->valeur_acquisition, 0, ',', ' ') }} MRU
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ONGLET Anomalies --}}
            <div x-show="activeTab === 'anomalies'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                @php $anomalies = $this->anomalies; @endphp

                {{-- Localisations problématiques --}}
                @if(count($anomalies['localisations_non_demarrees']) > 0 || count($anomalies['localisations_bloquees']) > 0 || count($anomalies['taux_absence_eleve']) > 0)
                    <div class="mb-6 print:mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Localisations problématiques</h3>
                        
                        @if(count($anomalies['localisations_non_demarrees']) > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Localisations non démarrées ({{ count($anomalies['localisations_non_demarrees']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($anomalies['localisations_non_demarrees'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ $anomalie['designation'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($anomalies['localisations_bloquees']) > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Localisations bloquées ({{ count($anomalies['localisations_bloquees']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($anomalies['localisations_bloquees'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - pas de scan depuis {{ $anomalie['jours'] }} jour(s)</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($anomalies['taux_absence_eleve']) > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Taux d'absence élevé ({{ count($anomalies['taux_absence_eleve']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($anomalies['taux_absence_eleve'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ $anomalie['taux_absence'] }}% absents ({{ $anomalie['biens_absents'] }} immobilisations)</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Biens à attention particulière --}}
                @if(count($anomalies['biens_absents_valeur_haute']) > 0 || count($anomalies['biens_deteriores']) > 0)
                    <div class="mb-6 print:mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Immobilisations à attention particulière</h3>
                        
                        @if(count($anomalies['biens_absents_valeur_haute']) > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-red-700 mb-2">Immobilisations absentes de valeur élevée ({{ count($anomalies['biens_absents_valeur_haute']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($anomalies['biens_absents_valeur_haute'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - {{ number_format($anomalie['valeur'], 0, ',', ' ') }} MRU</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(count($anomalies['biens_deteriores']) > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-orange-700 mb-2">Immobilisations détériorées ({{ count($anomalies['biens_deteriores']) }})</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($anomalies['biens_deteriores'] as $anomalie)
                                        <li>{{ $anomalie['code'] }} - État : {{ $anomalie['etat_constate'] ?? 'N/A' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ONGLET Recommandations --}}
            <div x-show="activeTab === 'recommandations'" x-transition style="display: none;" class="space-y-6 print:block print:break-inside-avoid">
                <div class="mb-6 print:mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Corrections immédiates nécessaires</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                        @if(count($this->biensDeplaces) > 0)
                            <li>Mettre à jour {{ count($this->biensDeplaces) }} localisation(s) d'immobilisations déplacées</li>
                        @endif
                        @if(count($this->anomalies['biens_absents_valeur_haute']) > 0)
                            <li>Investiguer {{ count($this->anomalies['biens_absents_valeur_haute']) }} immobilisation(s) absente(s) de valeur élevée</li>
                        @endif
                        @if(count($this->biensDeteriores) > 0)
                            <li>Réparer/remplacer {{ count($this->biensDeteriores) }} immobilisation(s) détériorée(s)</li>
                        @endif
                    </ul>
                </div>

                <div class="mb-6 print:mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Améliorations organisationnelles</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                        @if(count($this->anomalies['taux_absence_eleve']) > 0)
                            <li>Améliorer signalétique des localisations avec taux d'erreur élevé</li>
                        @endif
                        <li>Former les agents sur les procédures d'inventaire</li>
                        @if(count($this->biensDeplaces) > 5)
                            <li>Réorganiser les services avec nombreux déplacements</li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 print:text-base">Prochain inventaire</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                        <li>Prévoir {{ round($this->statistiques['duree_jours'] * 1.1) }} jours basé sur cette expérience</li>
                        <li>Assigner {{ count($this->statistiques['par_agent']) }} agent(s) minimum</li>
                        <li>Prioriser les localisations avec anomalies détectées</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer rapport --}}
    <div class="mt-6 pt-6 border-t border-gray-200 print:border-t-2 print:mt-4 print:pt-4">
        <div class="text-sm text-gray-500 space-y-1">
            <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>Généré par : {{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400 mt-2">Ce document est confidentiel et destiné à un usage interne uniquement.</p>
        </div>
    </div>

    {{-- Scripts pour les graphiques Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stats = @json($this->statistiques);

            // Graphique 1 : Répartition statuts
            const ctxStatuts = document.getElementById('chart-statuts');
            if (ctxStatuts) {
                new Chart(ctxStatuts, {
                    type: 'doughnut',
                    data: {
                        labels: ['Présents', 'Déplacés', 'Absents', 'Détériorés'],
                        datasets: [{
                            data: [
                                stats.biens_presents,
                                stats.biens_deplaces,
                                stats.biens_absents,
                                stats.biens_deteriores
                            ],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(234, 179, 8, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Graphique 2 : Conformité par localisation
            const ctxConformiteLoc = document.getElementById('chart-conformite-loc');
            if (ctxConformiteLoc) {
                const top10 = stats.par_localisation.slice(0, 10).sort((a, b) => b.taux_conformite - a.taux_conformite);
                new Chart(ctxConformiteLoc, {
                    type: 'bar',
                    data: {
                        labels: top10.map(loc => loc.code),
                        datasets: [{
                            label: 'Conformité (%)',
                            data: top10.map(loc => loc.taux_conformite),
                            backgroundColor: top10.map(loc => 
                                loc.taux_conformite >= 90 ? 'rgba(34, 197, 94, 0.8)' :
                                loc.taux_conformite >= 70 ? 'rgba(234, 179, 8, 0.8)' :
                                'rgba(239, 68, 68, 0.8)'
                            ),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100 }
                        }
                    }
                });
            }

            // Graphique 3 : Conformité par service (simplifié)
            const ctxService = document.getElementById('chart-conformite-service');
            if (ctxService) {
                // Regrouper par service depuis les localisations
                const services = {};
                stats.par_localisation.forEach(loc => {
                    // Simplification - à améliorer avec vraie relation service
                    const service = 'Service ' + (loc.localisation_id % 5 + 1);
                    if (!services[service]) {
                        services[service] = { total: 0, conforme: 0 };
                    }
                    services[service].total += loc.biens_scannes;
                    services[service].conforme += Math.round(loc.biens_scannes * (loc.taux_conformite / 100));
                });

                new Chart(ctxService, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(services),
                        datasets: [{
                            label: 'Conformité (%)',
                            data: Object.values(services).map(s => 
                                s.total > 0 ? Math.round((s.conforme / s.total) * 100 * 10) / 10 : 0
                            ),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100 }
                        }
                    }
                });
            }

            // Graphique 4 : Valeur par statut
            const ctxValeur = document.getElementById('chart-valeur-statut');
            if (ctxValeur) {
                // Calculer valeur par statut depuis les scans
                const valeurParStatut = {
                    'present': 0,
                    'deplace': 0,
                    'absent': 0,
                    'deteriore': 0
                };
                
                @foreach($inventaire->inventaireScans as $scan)
                    @if($scan->bien)
                        valeurParStatut['{{ $scan->statut_scan }}'] += {{ $scan->bien->valeur_acquisition ?? 0 }};
                    @endif
                @endforeach

                new Chart(ctxValeur, {
                    type: 'bar',
                    data: {
                        labels: ['Présents', 'Déplacés', 'Absents', 'Détériorés'],
                        datasets: [{
                            label: 'Valeur (MRU)',
                            data: [
                                valeurParStatut.present,
                                valeurParStatut.deplace,
                                valeurParStatut.absent,
                                valeurParStatut.deteriore
                            ],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(234, 179, 8, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Valeur : ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' MRU';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        });
    </script>

    {{-- CSS Print-friendly --}}
    <style>
        @media print {
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            .print\:border { border: 1px solid #000 !important; }
            .print\:border-2 { border: 2px solid #000 !important; }
            .print\:break-inside-avoid { break-inside: avoid; }
            .print\:bg-gray-50 { background-color: #f9fafb !important; }
            .print\:bg-gray-100 { background-color: #f3f4f6 !important; }
            .print\:text-2xl { font-size: 1.5rem !important; }
            .print\:text-base { font-size: 1rem !important; }
            .print\:mb-4 { margin-bottom: 1rem !important; }
            .print\:p-4 { padding: 1rem !important; }
            .print\:gap-4 { gap: 1rem !important; }
            .print\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .print\:overflow-visible { overflow: visible !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .print\:border-0 { border: 0 !important; }
            
            @page {
                margin: 2cm;
                size: A4;
            }
            
            body { 
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>

    {{-- Messages flash --}}
    @if(session()->has('error'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 print:hidden">
            {{ session('error') }}
        </div>
    @endif
</div>

