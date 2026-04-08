<div>
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">

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
            label="Valeur totale"
            value="{{ number_format($valeurTotale, 0, ',', ' ') }} MRU"
            sub="Valeur déclarée"
            color="green">
            <x-slot name="icon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
        </x-stat-card>

        {{-- Card Inventaire --}}
        <div class="relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Inventaire actif</p>
                    @if($inventaireEnCours)
                        <p class="mt-2 text-lg font-bold text-slate-900 truncate">{{ $inventaireEnCours->annee }}</p>
                        <div class="mt-1.5">
                            @php
                                $statut = $inventaireEnCours->statut;
                                $statutMap = [
                                    'en_preparation' => ['label' => 'En préparation', 'color' => 'slate'],
                                    'en_cours'       => ['label' => 'En cours',       'color' => 'blue'],
                                    'termine'        => ['label' => 'Terminé',        'color' => 'amber'],
                                    'cloture'        => ['label' => 'Clôturé',        'color' => 'green'],
                                ];
                                $sc = $statutMap[$statut] ?? ['label' => $statut, 'color' => 'slate'];
                            @endphp
                            <x-badge :color="$sc['color']" dot>{{ $sc['label'] }}</x-badge>
                        </div>
                        @php $prog = round($statistiquesInventaire['progression'] ?? 0, 1); @endphp
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Progression</span><span>{{ $prog }}%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-slate-100">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $prog >= 100 ? 'bg-green-500' : ($prog >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                     style="width: {{ $prog }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-lg font-bold text-slate-400">Aucun</p>
                        <p class="mt-1 text-xs text-slate-400">Pas d'inventaire en cours</p>
                    @endif
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
            </div>
            @if($inventaireEnCours)
            <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate class="mt-4 inline-flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 transition-colors">
                Voir l'inventaire
                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
        </div>
    </div>

    {{-- Message bienvenue nouvelle installation --}}
    @if($totalBiens === 0 && $totalLocalisations === 0)
    <x-card class="mb-6">
        <div class="flex items-start gap-4">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Bienvenue dans Gesimmos !</h3>
                <p class="mt-1 text-sm text-slate-500">Pour commencer, créez vos localisations, ajoutez des immobilisations, puis démarrez votre premier inventaire.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <x-btn href="{{ route('localisations.create') }}" variant="secondary" size="sm">Créer une localisation</x-btn>
                    <x-btn href="{{ route('biens.create') }}" variant="secondary" size="sm">Ajouter une immobilisation</x-btn>
                </div>
            </div>
        </div>
    </x-card>
    @endif

    {{-- Section inventaire en cours --}}
    @if($inventaireEnCours)
    <x-card :padding="false" class="mb-6">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($inventaireEnCours->statut === 'en_cours')
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    </span>
                    @endif
                    <h3 class="text-sm font-semibold text-slate-900">
                        Inventaire {{ $inventaireEnCours->annee }}
                    </h3>
                    <x-badge :color="$sc['color']" dot>{{ $sc['label'] }}</x-badge>
                </div>
                <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    Voir les détails →
                </a>
            </div>
        </x-slot>

        {{-- Stats inventaire --}}
        @if(!empty($statistiquesInventaire))
        <div class="grid grid-cols-2 gap-0 divide-x divide-y divide-slate-100 sm:grid-cols-4 sm:divide-y-0">
            @foreach([
                ['label' => 'Localisations', 'value' => ($statistiquesInventaire['localisations_terminees'] ?? 0).' / '.($statistiquesInventaire['total_localisations'] ?? 0), 'color' => 'text-blue-700'],
                ['label' => 'Total scans',   'value' => number_format($statistiquesInventaire['total_scans'] ?? 0, 0, ',', ' '), 'color' => 'text-green-700'],
                ['label' => 'Progression',   'value' => round($statistiquesInventaire['progression'] ?? 0, 1).'%', 'color' => 'text-purple-700'],
                ['label' => 'Conformité',    'value' => round($statistiquesInventaire['taux_conformite'] ?? 0, 1).'%', 'color' => 'text-indigo-700'],
            ] as $stat)
            <div class="px-5 py-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Tableau localisations --}}
        @if(!empty($localisationsInventaire))
        <div class="overflow-x-auto border-t border-slate-100">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Localisation</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Attendus</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Scannés</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Progression</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($localisationsInventaire as $loc)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3 text-sm font-medium text-slate-900">{{ $loc['localisation'] }}</td>
                        <td class="px-5 py-3 text-center text-sm tabular-nums text-slate-700">{{ $loc['biens_attendus'] }}</td>
                        <td class="px-5 py-3 text-center text-sm font-semibold tabular-nums {{ $loc['biens_scannes'] > 0 ? 'text-blue-600' : 'text-slate-400' }}">{{ $loc['biens_scannes'] }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full {{ $loc['progression'] >= 100 ? 'bg-green-500' : ($loc['progression'] >= 50 ? 'bg-blue-500' : ($loc['progression'] > 0 ? 'bg-amber-400' : 'bg-slate-200')) }}"
                                         style="width: {{ min($loc['progression'], 100) }}%"></div>
                                </div>
                                <span class="text-xs tabular-nums text-slate-600">{{ round($loc['progression'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $sColors = ['termine' => 'green', 'en_cours' => 'blue', 'en_attente' => 'amber'];
                                $sLabels = ['termine' => 'Terminé', 'en_cours' => 'En cours', 'en_attente' => 'En attente'];
                            @endphp
                            <x-badge :color="$sColors[$loc['statut']] ?? 'slate'" dot size="xs">
                                {{ $sLabels[$loc['statut']] ?? ucfirst($loc['statut']) }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">
                            @if($loc['agent'] === 'Non assigné')
                                <span class="italic text-slate-400">Non assigné</span>
                            @else
                                {{ $loc['agent'] }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                            Aucune localisation assignée à cet inventaire
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </x-card>
    @endif

    {{-- Emplacements inventoriés --}}
    @if($inventaireEnCours && !empty($emplacementsInventories))
    <x-card :padding="false" class="mb-6">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Emplacements inventoriés <span class="ml-1 text-slate-400 font-normal">({{ count($emplacementsInventories) }})</span></h3>
                <a href="{{ route('emplacements.index') }}" wire:navigate class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Voir tous →</a>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Emplacement</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Localisation</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Scannés / Total</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Progression</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($emplacementsInventories as $emp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-slate-900">{{ $emp['nom'] }}</p>
                            @if(!empty($emp['code']))<p class="text-xs text-slate-400">{{ $emp['code'] }}</p>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $emp['localisation'] }}</td>
                        <td class="px-5 py-3 text-center text-sm font-semibold text-slate-700 tabular-nums">
                            <span class="text-blue-600">{{ $emp['biens_scannes'] }}</span> / {{ $emp['total_biens'] }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-slate-100">
                                    <div class="h-1.5 rounded-full {{ $emp['progression'] >= 100 ? 'bg-green-500' : ($emp['progression'] >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                         style="width: {{ min($emp['progression'], 100) }}%"></div>
                                </div>
                                <span class="text-xs tabular-nums text-slate-600">{{ round($emp['progression'], 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    {{-- Activité récente + Actions rapides --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <x-card>
            <x-slot name="header">
                <h3 class="text-sm font-semibold text-slate-900">Activité récente</h3>
            </x-slot>
            <div class="space-y-3">
                @forelse($dernieresActions as $action)
                <div class="flex items-start gap-3 rounded-lg p-2 hover:bg-slate-50 transition-colors">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700">{{ $action['message'] }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $action['time_ago'] }}</p>
                    </div>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-400">Aucune activité récente</p>
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
                    ['href' => route('biens.create'),          'label' => 'Ajouter une immobilisation', 'color' => 'indigo', 'icon' => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['href' => route('localisations.create'),  'label' => 'Ajouter une localisation',   'color' => 'blue',   'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                    ['href' => route('inventaires.create'),    'label' => 'Démarrer un inventaire',     'color' => 'purple', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z'],
                    ['href' => route('users.index'),           'label' => 'Gérer les utilisateurs',     'color' => 'slate',  'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ] as $qa)
                @php
                    $qColors = ['indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100', 'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100', 'purple' => 'bg-purple-50 text-purple-700 hover:bg-purple-100', 'slate' => 'bg-slate-100 text-slate-700 hover:bg-slate-200'];
                @endphp
                <a href="{{ $qa['href'] }}" wire:navigate
                   class="flex flex-col items-center gap-2 rounded-lg p-4 text-center transition-colors {{ $qColors[$qa['color']] ?? $qColors['slate'] }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $qa['icon'] }}"/>
                    </svg>
                    <span class="text-xs font-medium leading-tight">{{ $qa['label'] }}</span>
                </a>
                @endforeach
            </div>
        </x-card>
        @endif

    </div>

</div>
