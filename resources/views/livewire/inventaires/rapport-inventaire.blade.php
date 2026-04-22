<div x-data="{ activeTab: @entangle('activeTab'), sousOnglet: 'presents' }" class="space-y-6">
    @php
        // $etatsConstate est injecté par le composant Livewire (depuis la table etat)
        $conformiteClass = function($taux) {
            if ($taux >= 95) return ['text' => 'text-green-700',  'bg' => 'bg-green-50',  'border' => 'border-green-200',  'bar' => 'bg-green-500',  'label' => 'Excellent'];
            if ($taux >= 85) return ['text' => 'text-indigo-700', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'bar' => 'bg-indigo-500', 'label' => 'Satisfaisant'];
            if ($taux >= 70) return ['text' => 'text-amber-700',  'bg' => 'bg-amber-50',  'border' => 'border-amber-200',  'bar' => 'bg-amber-500',  'label' => 'Moyen'];
            return             ['text' => 'text-red-700',    'bg' => 'bg-red-50',    'border' => 'border-red-200',    'bar' => 'bg-red-500',    'label' => 'Insuffisant'];
        };
        $taux        = $this->statistiques['taux_conformite'];
        $couverture  = $this->statistiques['taux_couverture'] ?? 0;
        $interp      = $conformiteClass($taux);

        $statutsInventaire = [
            'termine' => ['label' => 'Terminé',  'color' => 'bg-amber-100 text-amber-800 border border-amber-200'],
            'cloture' => ['label' => 'Clôturé',  'color' => 'bg-green-100 text-green-800 border border-green-200'],
        ];
        $statutsScan = [
            'present'   => ['label' => 'Présent',   'color' => 'bg-green-100  text-green-800'],
            'deplace'   => ['label' => 'Déplacé',   'color' => 'bg-yellow-100 text-yellow-800'],
            'absent'    => ['label' => 'Absent',    'color' => 'bg-red-100    text-red-800'],
            'deteriore' => ['label' => 'Détérioré', 'color' => 'bg-orange-100 text-orange-800'],
        ];
    @endphp

    {{-- ═══════════════════════════════════════════════════
         EN-TÊTE
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Bande couleur --}}
        <div class="h-1 w-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-400"></div>

        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap mb-1">
                    <h1 class="text-xl font-bold text-gray-900">
                        Rapport — Inventaire {{ $inventaire->annee }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statutsInventaire[$inventaire->statut]['color'] }}">
                        {{ $statutsInventaire[$inventaire->statut]['label'] }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    Du {{ $inventaire->date_debut?->format('d/m/Y') ?? '—' }}
                    @if($inventaire->date_fin) au {{ $inventaire->date_fin->format('d/m/Y') }} @endif
                    &bull; {{ $this->statistiques['duree_jours'] }} jour(s)
                    &bull; {{ $this->statistiques['nombre_agents'] }} agent(s)
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <button wire:click="exportPDF"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </button>
                <a href="{{ route('inventaires.imprimer', $inventaire) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         KPI PRINCIPAUX
    ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Conformité --}}
        <div class="bg-white rounded-xl border {{ $interp['border'] }} shadow-sm p-5 {{ $interp['bg'] }}">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $interp['text'] }} opacity-75 mb-1">Taux de conformité</p>
            <p class="text-3xl font-bold {{ $interp['text'] }}">{{ $taux }}%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="{{ $interp['bar'] }} h-full rounded-full" style="width:{{ min(100,$taux) }}%"></div>
            </div>
            <p class="text-xs {{ $interp['text'] }} mt-1.5 font-medium">{{ $interp['label'] }}</p>
        </div>

        {{-- Couverture --}}
        <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-5 bg-indigo-50">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 opacity-75 mb-1">Taux de couverture</p>
            <p class="text-3xl font-bold text-indigo-700">{{ $couverture }}%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-indigo-500 h-full rounded-full" style="width:{{ min(100,$couverture) }}%"></div>
            </div>
            <p class="text-xs text-indigo-600 mt-1.5">{{ $this->statistiques['total_biens_scannes'] }} / {{ $this->statistiques['total_biens_attendus'] }} vérifiés</p>
        </div>

        {{-- Absence --}}
        @php $tauxAbsence = $this->statistiques['taux_absence'] ?? 0; @endphp
        <div class="bg-white rounded-xl border {{ $tauxAbsence > 10 ? 'border-red-200 bg-red-50' : 'border-gray-200' }} shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $tauxAbsence > 10 ? 'text-red-600' : 'text-gray-500' }} opacity-75 mb-1">Taux d'absence</p>
            <p class="text-3xl font-bold {{ $tauxAbsence > 10 ? 'text-red-700' : 'text-gray-900' }}">{{ $tauxAbsence }}%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="{{ $tauxAbsence > 10 ? 'bg-red-500' : 'bg-gray-400' }} h-full rounded-full" style="width:{{ min(100,$tauxAbsence) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5">{{ $this->statistiques['biens_absents'] }} absent(s)</p>
        </div>

        {{-- Progression --}}
        @php $prog = $this->statistiques['progression_globale'] ?? 0; @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 opacity-75 mb-1">Progression</p>
            <p class="text-3xl font-bold text-gray-900">{{ $prog }}%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-gray-600 h-full rounded-full" style="width:{{ min(100,$prog) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5">{{ $this->statistiques['localisations_terminees'] }}/{{ $this->statistiques['total_localisations'] }} loc.</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         RÉSULTATS PAR STATUT DE SCAN
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <div class="w-1 h-4 rounded-full bg-indigo-600"></div>
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Résultats de vérification</h2>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach([
                ['label'=>'Présents',   'val'=>$this->statistiques['biens_presents'],               'cls'=>'text-green-700  bg-green-50  border-green-200'],
                ['label'=>'Déplacés',   'val'=>$this->statistiques['biens_deplaces'],               'cls'=>'text-yellow-700 bg-yellow-50 border-yellow-200'],
                ['label'=>'Absents',    'val'=>$this->statistiques['biens_absents'],                'cls'=>'text-red-700    bg-red-50    border-red-200'],
                ['label'=>'Détériorés', 'val'=>$this->statistiques['biens_deteriores'],             'cls'=>'text-orange-700 bg-orange-50 border-orange-200'],
                ['label'=>'Défectueux', 'val'=>$this->statistiques['biens_defectueux'] ?? 0,       'cls'=>'text-amber-700  bg-amber-50  border-amber-200'],
            ] as $kpi)
            <div class="rounded-lg border p-4 text-center {{ $kpi['cls'] }}">
                <p class="text-xs font-semibold uppercase tracking-wide opacity-70 mb-1">{{ $kpi['label'] }}</p>
                <p class="text-2xl font-bold">{{ $kpi['val'] }}</p>
                <p class="text-xs opacity-60 mt-0.5">
                    {{ $this->statistiques['total_biens_scannes'] > 0 ? round($kpi['val'] / $this->statistiques['total_biens_scannes'] * 100, 1) : 0 }}%
                </p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ÉTAT PHYSIQUE CONSTATÉ (depuis DB)
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <div class="w-1 h-4 rounded-full bg-indigo-600"></div>
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">État physique constaté</h2>
        </div>
        <div class="p-5 grid grid-cols-3 gap-3">
            @php
                $etatsPhysiques = [
                    ['key'=>'neuf',    'stat'=>'biens_neufs',      'cls'=>'text-green-700 bg-green-50 border-green-200'],
                    ['key'=>'bon',     'stat'=>'biens_bon_etat',   'cls'=>'text-blue-700  bg-blue-50  border-blue-200'],
                    ['key'=>'mauvais', 'stat'=>'biens_defectueux', 'cls'=>'text-amber-700 bg-amber-50 border-amber-200'],
                ];
            @endphp
            @foreach($etatsPhysiques as $ep)
            @php
                $label = $etatsConstate[$ep['key']]['label'] ?? ucfirst($ep['key']);
                $val   = $this->statistiques[$ep['stat']] ?? 0;
                $pct   = $this->statistiques['total_biens_scannes'] > 0 ? round($val / $this->statistiques['total_biens_scannes'] * 100, 1) : 0;
            @endphp
            <div class="rounded-lg border p-4 text-center {{ $ep['cls'] }}">
                <p class="text-xs font-semibold uppercase tracking-wide opacity-70 mb-1">{{ $label }}</p>
                <p class="text-2xl font-bold">{{ $val }}</p>
                <p class="text-xs opacity-60 mt-0.5">{{ $pct }}%</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ONGLETS
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Nav onglets --}}
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex overflow-x-auto px-4" aria-label="Onglets">
                @foreach([
                    ['id'=>'resume',      'label'=>'Résumé'],
                    ['id'=>'emplacements','label'=>'Par localisation'],
                    ['id'=>'biens',       'label'=>'Immobilisations'],
                    ['id'=>'anomalies',   'label'=>'Anomalies'],
                ] as $tab)
                <button
                    @click="activeTab = '{{ $tab['id'] }}'; $wire.setActiveTab('{{ $tab['id'] }}')"
                    :class="activeTab === '{{ $tab['id'] }}'
                        ? 'border-indigo-600 text-indigo-600 bg-white'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-3.5 px-4 border-b-2 font-medium text-sm transition-colors -mb-px">
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">

            {{-- ─── ONGLET : RÉSUMÉ ─────────────────────────── --}}
            <div x-show="activeTab === 'resume'" x-transition class="space-y-6">

                {{-- Contribution par agent --}}
                @if(count($this->statistiques['par_agent'] ?? []) > 0)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        Contribution par agent
                    </h3>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Localisations</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Biens scannés</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">% du total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($this->statistiques['par_agent'] as $agent)
                                @php $pct = $this->statistiques['total_biens_scannes'] > 0 ? round($agent['biens_scannes']/$this->statistiques['total_biens_scannes']*100,1) : 0; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $agent['agent_name'] }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $agent['localisations'] }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ number_format($agent['biens_scannes'],0,',',' ') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 max-w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-indigo-500 rounded-full" style="width:{{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-indigo-700">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Indicateurs secondaires --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        Indicateurs secondaires
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach([
                            ['label'=>"Taux d'anomalies", 'val'=>($this->statistiques['taux_anomalies']??0).'%', 'sub'=>'déplacés + absents', 'warn'=>($this->statistiques['taux_anomalies']??0)>15],
                            ['label'=>'Non scannés',       'val'=>$this->statistiques['biens_non_scannes']??0,    'sub'=>'manquants',          'warn'=>($this->statistiques['biens_non_scannes']??0)>0],
                            ['label'=>'Durée',             'val'=>($this->statistiques['duree_jours']??0).'j',    'sub'=>'inventaire',         'warn'=>false],
                            ['label'=>'Agents',            'val'=>$this->statistiques['nombre_agents']??0,        'sub'=>'ayant participé',    'warn'=>false],
                        ] as $ind)
                        <div class="rounded-lg border {{ $ind['warn'] ? 'border-amber-200 bg-amber-50' : 'border-gray-200 bg-gray-50' }} p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $ind['label'] }}</p>
                            <p class="text-xl font-bold {{ $ind['warn'] ? 'text-amber-700' : 'text-gray-900' }}">{{ $ind['val'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $ind['sub'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─── ONGLET : PAR LOCALISATION ───────────────── --}}
            <div x-show="activeTab === 'emplacements'" x-transition style="display:none;" class="space-y-4">
                @if(count($this->detailParEmplacement) > 0)
                <div>
                    <select wire:model.live="filterEmplacement"
                        class="block w-full md:w-72 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Toutes les localisations</option>
                        @foreach($this->detailParEmplacement as $emp)
                        <option value="{{ $emp['emplacement_id'] }}">{{ $emp['designation'] ?? $emp['code'] }}</option>
                        @endforeach
                    </select>
                </div>

                @foreach($this->detailParEmplacement as $emp)
                    @if($filterEmplacement === 'all' || $filterEmplacement == $emp['emplacement_id'])
                    @php $tc = $emp['taux_conformite'] ?? 0; @endphp
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $emp['designation'] ?? $emp['code'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $emp['localisation'] ?? '' }} — {{ $emp['total_trouves'] }}/{{ $emp['total_attendus'] }} trouvés</p>
                            </div>
                            <span class="text-sm font-bold {{ $tc >= 90 ? 'text-green-700' : ($tc >= 70 ? 'text-amber-700' : 'text-red-700') }}">
                                {{ round($tc,1) }}%
                            </span>
                        </div>
                        @if(count($emp['lignes'] ?? []) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Attendu</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Trouvé</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">État</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($emp['lignes'] as $ligne)
                                    @php
                                        $s = $ligne['statut_scan'] ?? '';
                                        $statutCls = $statutsScan[$s]['color'] ?? 'bg-gray-100 text-gray-700';
                                        $statutLbl = $statutsScan[$s]['label'] ?? $s;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 font-medium text-gray-900">{{ $ligne['code'] ?? '—' }}</td>
                                        <td class="px-4 py-2.5 text-gray-700">{{ Str::limit($ligne['designation'] ?? '—', 45) }}</td>
                                        <td class="px-4 py-2.5 text-center text-gray-600">{{ $ligne['attendu'] ?? 1 }}</td>
                                        <td class="px-4 py-2.5 text-center text-gray-600">{{ $ligne['trouve'] ?? 0 }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statutCls }}">{{ $statutLbl }}</span>
                                        </td>
                                        <td class="px-4 py-2.5 text-center text-gray-600 text-xs">{{ $ligne['etat'] ?? '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="px-4 py-5 text-sm text-gray-400 italic">Aucune immobilisation.</p>
                        @endif
                    </div>
                    @endif
                @endforeach

                @else
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-4 text-sm text-amber-800">
                    Aucune donnée par localisation disponible.
                </div>
                @endif
            </div>

            {{-- ─── ONGLET : IMMOBILISATIONS ────────────────── --}}
            <div x-show="activeTab === 'biens'" x-transition style="display:none;" class="space-y-4">

                {{-- Sous-onglets --}}
                <div class="flex gap-1 p-1 bg-gray-100 rounded-lg w-fit">
                    @foreach([
                        ['id'=>'presents',   'label'=>'Présents',   'count'=>count($this->biensPresents)],
                        ['id'=>'deplaces',   'label'=>'Déplacés',   'count'=>count($this->biensDeplaces)],
                        ['id'=>'absents',    'label'=>'Absents',    'count'=>count($this->biensAbsents)],
                        ['id'=>'defectueux', 'label'=>'Défectueux', 'count'=>count($this->biensDefectueux)],
                    ] as $st)
                    <button
                        @click="sousOnglet = '{{ $st['id'] }}'"
                        :class="sousOnglet === '{{ $st['id'] }}' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap">
                        {{ $st['label'] }}
                        <span class="ml-1 text-xs opacity-60">({{ $st['count'] }})</span>
                    </button>
                    @endforeach
                </div>

                {{-- Présents --}}
                <div x-show="sousOnglet === 'presents'" x-transition style="display:none;">
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">État physique</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($this->biensPresents->take(50) as $scan)
                                @php
                                    $eKey  = $scan->etat_constate ?? 'bon';
                                    $eCls  = $etatsConstate[$eKey]['color'] ?? 'bg-gray-100 text-gray-700';
                                    $eLbl  = $etatsConstate[$eKey]['label'] ?? $scan->etat_constate_label;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($scan->designation, 50) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $eCls }}">{{ $eLbl }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $scan->localisation_code ?? ($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? '—') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($this->biensPresents) > 50)
                    <p class="text-xs text-gray-400 mt-2">50 premiers résultats sur {{ count($this->biensPresents) }}</p>
                    @endif
                </div>

                {{-- Déplacés --}}
                <div x-show="sousOnglet === 'deplaces'" x-transition style="display:none;">
                    @if(count($this->biensDeplaces) > 0)
                    <div class="mb-3 rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800 font-medium">
                        Mettre à jour la localisation permanente de ces {{ count($this->biensDeplaces) }} immobilisation(s).
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation prévue</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation réelle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($this->biensDeplaces->take(50) as $scan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($scan->designation, 45) }}</td>
                                    <td class="px-4 py-3 text-xs text-red-600 font-medium">{{ $scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-amber-700 font-semibold">{{ $scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien déplacé.</p>
                    @endif
                </div>

                {{-- Absents --}}
                <div x-show="sousOnglet === 'absents'" x-transition style="display:none;">
                    @if(count($this->biensAbsents) > 0)
                    <div class="mb-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 font-medium">
                        {{ count($this->biensAbsents) }} immobilisation(s) absente(s) — une enquête est nécessaire.
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Catégorie</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Agent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($this->biensAbsents->take(50) as $scan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($scan->designation, 45) }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $scan->bien?->categorie?->Categorie ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $scan->agent?->users ?? $scan->agent?->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien absent.</p>
                    @endif
                </div>

                {{-- Défectueux --}}
                <div x-show="sousOnglet === 'defectueux'" x-transition style="display:none;">
                    @if(count($this->biensDefectueux) > 0)
                    <div class="mb-3 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 font-medium">
                        {{ count($this->biensDefectueux) }} immobilisation(s) signalée(s) en mauvais état — décision de réparation ou mise au rebut requise.
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">État</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Commentaire</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Photo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($this->biensDefectueux->take(50) as $scan)
                                @php
                                    $eKey = $scan->etat_constate ?? 'mauvais';
                                    $eCls = $etatsConstate[$eKey]['color'] ?? 'bg-amber-100 text-amber-800';
                                    $eLbl = $etatsConstate[$eKey]['label'] ?? $scan->etat_constate_label;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $scan->code_inventaire }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($scan->designation, 40) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $eCls }}">{{ $eLbl }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $scan->localisationReelle?->CodeLocalisation ?? $scan->localisation_code ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500 italic">{{ Str::limit($scan->commentaire ?? '', 35) ?: '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($scan->photo_path && $scan->photo_url)
                                        <div x-data="{ open: false }" @keydown.escape.window="open = false" class="inline">
                                            <button @click="open = true" type="button">
                                                <img src="{{ $scan->photo_url }}" alt="" class="w-10 h-10 object-cover rounded border border-gray-200 hover:border-indigo-400 transition cursor-pointer"
                                                    onerror="this.style.display='none'">
                                            </button>
                                            <div x-show="open" x-cloak @click.self="open = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100">
                                                <div class="relative">
                                                    <img src="{{ $scan->photo_url }}" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-xl">
                                                    <button @click="open = false" class="absolute -top-10 right-0 text-white p-2 hover:bg-white/10 rounded-full transition">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($this->biensDefectueux) > 50)
                    <p class="text-xs text-gray-400 mt-2">50 premiers résultats sur {{ count($this->biensDefectueux) }}</p>
                    @endif
                    @else
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien défectueux signalé.</p>
                    @endif
                </div>
            </div>

            {{-- ─── ONGLET : ANOMALIES ──────────────────────── --}}
            <div x-show="activeTab === 'anomalies'" x-transition style="display:none;" class="space-y-4">
                @php $anomalies = $this->anomalies; @endphp

                @if(count($anomalies['localisations_non_demarrees'] ?? []) > 0 || count($anomalies['taux_absence_eleve'] ?? []) > 0 || count($anomalies['biens_defectueux'] ?? []) > 0)

                    @if(count($anomalies['localisations_non_demarrees'] ?? []) > 0)
                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                        <h4 class="font-semibold text-yellow-800 text-sm mb-2">
                            Localisations non démarrées ({{ count($anomalies['localisations_non_demarrees']) }})
                        </h4>
                        <ul class="space-y-1 text-sm text-yellow-700 list-disc list-inside">
                            @foreach($anomalies['localisations_non_demarrees'] as $a)
                            <li><strong>{{ $a['code'] }}</strong> — {{ $a['designation'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(count($anomalies['taux_absence_eleve'] ?? []) > 0)
                    <div class="rounded-lg bg-orange-50 border border-orange-200 p-4">
                        <h4 class="font-semibold text-orange-800 text-sm mb-2">
                            Taux d'absence élevé ({{ count($anomalies['taux_absence_eleve']) }})
                        </h4>
                        <ul class="space-y-1 text-sm text-orange-700 list-disc list-inside">
                            @foreach($anomalies['taux_absence_eleve'] as $a)
                            <li><strong>{{ $a['code'] }}</strong> — {{ $a['taux_absence'] }}% absents ({{ $a['biens_absents'] }} immobilisations)</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(count($anomalies['biens_defectueux'] ?? []) > 0)
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                        <h4 class="font-semibold text-amber-800 text-sm mb-2">
                            Immobilisations défectueuses ({{ count($anomalies['biens_defectueux']) }})
                        </h4>
                        <ul class="space-y-1 text-sm text-amber-700 list-disc list-inside">
                            @foreach($anomalies['biens_defectueux'] as $a)
                            <li><strong>{{ $a['code'] }}</strong> — {{ $a['designation'] }} ({{ $a['localisation'] }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900">Aucune anomalie détectée</p>
                    <p class="text-sm text-gray-500 mt-1">L'inventaire s'est déroulé sans anomalie majeure.</p>
                </div>
                @endif
            </div>

        </div>{{-- /p-6 --}}
    </div>{{-- /card onglets --}}

    {{-- Pied de page --}}
    <div class="text-center text-xs text-gray-400 py-2">
        Rapport généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->users ?? auth()->user()->name ?? '—' }}
    </div>

</div>
