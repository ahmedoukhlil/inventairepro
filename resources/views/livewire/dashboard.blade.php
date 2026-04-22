<div class="space-y-6">

    {{-- ═══════════════════════════════════════════
         KPI PRINCIPAUX
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        <x-stat-card
            label="Total Immobilisations"
            value="{{ number_format($totalBiens, 0, ',', ' ') }}"
            sub="+{{ $biensCetteAnnee }} cette année"
            href="{{ route('biens.index') }}"
            color="indigo">
            <x-slot name="icon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            label="Localisations"
            value="{{ number_format($totalLocalisations, 0, ',', ' ') }}"
            sub="{{ $nombreBatiments }} bâtiment(s)"
            href="{{ route('localisations.index') }}"
            color="blue">
            <x-slot name="icon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            label="Valeur du parc"
            value="{{ number_format($valeurTotale, 0, ',', ' ') }} MRU"
            sub="Valeur déclarée"
            color="green">
            <x-slot name="icon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
        </x-stat-card>

        {{-- Card Inventaire actif --}}
        @php
            $statut   = $inventaireEnCours?->statut ?? null;
            $statutMap = [
                'en_preparation' => ['label' => 'En préparation', 'color' => 'slate',  'badge' => 'bg-gray-100 text-gray-700'],
                'en_cours'       => ['label' => 'En cours',       'color' => 'blue',   'badge' => 'bg-blue-100 text-blue-700'],
                'termine'        => ['label' => 'Terminé',        'color' => 'amber',  'badge' => 'bg-amber-100 text-amber-700'],
                'cloture'        => ['label' => 'Clôturé',        'color' => 'green',  'badge' => 'bg-green-100 text-green-700'],
            ];
            $sc   = $statutMap[$statut] ?? ['label' => $statut, 'color' => 'slate', 'badge' => 'bg-gray-100 text-gray-700'];
            $prog = round($statistiquesInventaire['progression'] ?? 0, 1);
        @endphp
        <div class="relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md overflow-hidden">
            <div class="h-1 w-full bg-gradient-to-r from-purple-500 to-indigo-500"></div>
            <div class="p-5 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Inventaire actif</p>
                        @if($inventaireEnCours)
                            <p class="mt-1.5 text-xl font-bold text-slate-900">{{ $inventaireEnCours->annee }}</p>
                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc['badge'] }}">
                                @if($statut === 'en_cours')<span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1 animate-pulse inline-block"></span>@endif
                                {{ $sc['label'] }}
                            </span>
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Progression</span><span class="font-semibold">{{ $prog }}%</span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full transition-all duration-500 {{ $prog >= 100 ? 'bg-green-500' : ($prog >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                         style="width:{{ $prog }}%"></div>
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-lg font-bold text-slate-400">Aucun</p>
                            <p class="text-xs text-slate-400 mt-0.5">Pas d'inventaire actif</p>
                        @endif
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                    </div>
                </div>
            </div>
            @if($inventaireEnCours)
            <div class="px-5 pb-4">
                <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate
                   class="inline-flex items-center text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                    Voir l'inventaire
                    <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Message bienvenue --}}
    @if($totalBiens === 0 && $totalLocalisations === 0)
    <x-card class="border-indigo-100 bg-indigo-50">
        <div class="flex items-start gap-4">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-indigo-900">Bienvenue dans Gesimmos !</h3>
                <p class="mt-1 text-sm text-indigo-700">Créez vos localisations, ajoutez des immobilisations, puis démarrez votre premier inventaire.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <x-btn href="{{ route('localisations.create') }}" variant="secondary" size="sm">Créer une localisation</x-btn>
                    <x-btn href="{{ route('biens.create') }}" variant="secondary" size="sm">Ajouter une immobilisation</x-btn>
                </div>
            </div>
        </div>
    </x-card>
    @endif

    {{-- ═══════════════════════════════════════════
         INVENTAIRE EN COURS
    ═══════════════════════════════════════════ --}}
    @if($inventaireEnCours)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Bande + header --}}
        <div class="h-1 w-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-400"></div>
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($statut === 'en_cours')
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                </span>
                @endif
                <h3 class="text-sm font-semibold text-slate-900">Inventaire {{ $inventaireEnCours->annee }}</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc['badge'] }}">{{ $sc['label'] }}</span>
            </div>
            <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate
               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                Voir les détails →
            </a>
        </div>

        {{-- KPI inventaire --}}
        @if(!empty($statistiquesInventaire))
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-slate-100">
            @php
                $tauxConf = $statistiquesInventaire['taux_conformite'] ?? 0;
                $confColor = $tauxConf >= 85 ? 'text-green-700' : ($tauxConf >= 70 ? 'text-amber-600' : 'text-red-600');
                $tauxAbs  = $statistiquesInventaire['taux_absence'] ?? 0;
            @endphp
            <div class="px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Progression</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-indigo-700">{{ $prog }}%</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $statistiquesInventaire['localisations_terminees'] ?? 0 }}/{{ $statistiquesInventaire['total_localisations'] ?? 0 }} loc.</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Conformité</p>
                <p class="mt-1 text-2xl font-bold tabular-nums {{ $confColor }}">{{ $tauxConf }}%</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $statistiquesInventaire['biens_presents'] ?? 0 }} présents</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Scannés</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-blue-700">{{ number_format($statistiquesInventaire['total_scans'] ?? 0, 0, ',', ' ') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">sur {{ number_format($statistiquesInventaire['total_attendus'] ?? 0, 0, ',', ' ') }} attendus</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Absents</p>
                <p class="mt-1 text-2xl font-bold tabular-nums {{ $tauxAbs > 10 ? 'text-red-600' : 'text-slate-700' }}">{{ $statistiquesInventaire['biens_absents'] ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tauxAbs }}% du total</p>
            </div>
        </div>

        {{-- Répartition statuts scans --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex flex-wrap gap-3">
            @foreach([
                ['label'=>'Présents',   'val'=>$statistiquesInventaire['biens_presents']??0,   'cls'=>'bg-green-100 text-green-700'],
                ['label'=>'Déplacés',   'val'=>$statistiquesInventaire['biens_deplaces']??0,   'cls'=>'bg-yellow-100 text-yellow-700'],
                ['label'=>'Absents',    'val'=>$statistiquesInventaire['biens_absents']??0,    'cls'=>'bg-red-100 text-red-700'],
                ['label'=>'Détériorés', 'val'=>$statistiquesInventaire['biens_deteriores']??0, 'cls'=>'bg-orange-100 text-orange-700'],
                ['label'=>'Défectueux', 'val'=>$statistiquesInventaire['biens_defectueux']??0, 'cls'=>'bg-amber-100 text-amber-700'],
            ] as $kpi)
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $kpi['cls'] }}">
                <span class="text-xs font-semibold">{{ $kpi['label'] }}</span>
                <span class="text-sm font-bold">{{ $kpi['val'] }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Tableau localisations --}}
        @if(!empty($localisationsInventaire))
        <div class="overflow-x-auto border-t border-slate-100">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Localisation</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Attendus</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Scannés</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Progression</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($localisationsInventaire as $loc)
                    @php
                        $sColors = ['termine' => 'bg-green-100 text-green-700', 'en_cours' => 'bg-blue-100 text-blue-700', 'en_attente' => 'bg-gray-100 text-gray-600'];
                        $sLabels = ['termine' => 'Terminé', 'en_cours' => 'En cours', 'en_attente' => 'En attente'];
                        $sCls = $sColors[$loc['statut']] ?? 'bg-gray-100 text-gray-600';
                        $sLbl = $sLabels[$loc['statut']] ?? ucfirst($loc['statut']);
                        $barCls = $loc['progression'] >= 100 ? 'bg-green-500' : ($loc['progression'] >= 50 ? 'bg-blue-500' : ($loc['progression'] > 0 ? 'bg-amber-400' : 'bg-slate-200'));
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $loc['localisation'] }}</td>
                        <td class="px-5 py-3 text-center tabular-nums text-slate-600">{{ $loc['biens_attendus'] }}</td>
                        <td class="px-5 py-3 text-center font-semibold tabular-nums {{ $loc['biens_scannes'] > 0 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $loc['biens_scannes'] }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full {{ $barCls }}" style="width:{{ min($loc['progression'], 100) }}%"></div>
                                </div>
                                <span class="text-xs tabular-nums text-slate-500">{{ round($loc['progression'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $sCls }}">{{ $sLbl }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            @if($loc['agent'] === 'Non assigné')
                                <span class="italic text-slate-400 text-xs">Non assigné</span>
                            @else
                                {{ $loc['agent'] }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-400">Aucune localisation assignée à cet inventaire</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </div>
    @endif

    {{-- ═══════════════════════════════════════════
         ACTIVITÉ RÉCENTE + ACTIONS RAPIDES
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <x-card>
            <x-slot name="header">
                <h3 class="text-sm font-semibold text-slate-900">Activité récente</h3>
            </x-slot>
            <div class="space-y-1">
                @forelse($dernieresActions as $action)
                <div class="flex items-start gap-3 rounded-lg px-2 py-2.5 hover:bg-slate-50 transition-colors">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        @if($action['type'] === 'scan')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        @elseif($action['type'] === 'inventaire_started')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($action['type'] === 'inventaire_closed')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700">{{ $action['message'] }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $action['time_ago'] }}</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                    <svg class="h-8 w-8 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm">Aucune activité récente</p>
                </div>
                @endforelse
            </div>
        </x-card>

        @if(auth()->user()->isAdmin())
        <x-card>
            <x-slot name="header">
                <h3 class="text-sm font-semibold text-slate-900">Actions rapides</h3>
            </x-slot>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['href' => route('biens.create'),         'label' => 'Ajouter une immobilisation', 'bg' => 'bg-indigo-50 hover:bg-indigo-100', 'text' => 'text-indigo-700', 'icon' => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['href' => route('localisations.create'), 'label' => 'Ajouter une localisation',   'bg' => 'bg-blue-50   hover:bg-blue-100',   'text' => 'text-blue-700',   'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                    ['href' => route('inventaires.create'),   'label' => 'Nouvel inventaire',          'bg' => 'bg-purple-50 hover:bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z'],
                    ['href' => route('users.index'),          'label' => 'Gérer les utilisateurs',     'bg' => 'bg-slate-100  hover:bg-slate-200',  'text' => 'text-slate-700',  'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ] as $qa)
                <a href="{{ $qa['href'] }}" wire:navigate
                   class="flex flex-col items-center gap-2.5 rounded-xl p-4 text-center transition-colors {{ $qa['bg'] }}">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $qa['bg'] }} {{ $qa['text'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $qa['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold leading-tight {{ $qa['text'] }}">{{ $qa['label'] }}</span>
                </a>
                @endforeach
            </div>
        </x-card>
        @endif

    </div>

</div>
