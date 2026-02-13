<div>
    @php
        $isAdmin = auth()->user()->isAdmin();
        $statutConfig = [
            'en_preparation' => ['label' => 'Préparation', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-400'],
            'en_cours'       => ['label' => 'En cours',     'bg' => 'bg-blue-50',  'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
            'termine'        => ['label' => 'Terminé',      'bg' => 'bg-amber-50', 'text' => 'text-amber-700','dot' => 'bg-amber-500'],
            'cloture'        => ['label' => 'Clôturé',      'bg' => 'bg-green-50', 'text' => 'text-green-700','dot' => 'bg-green-500'],
        ];
        $compteurs = $this->compteurs;
        $inventaireActif = $this->inventaireEnCours;
    @endphp

    {{-- Auto-refresh si inventaire en cours --}}
    @if($inventaireActif && $inventaireActif->statut === 'en_cours')
        <div wire:poll.30s></div>
    @endif

    {{-- ========================================== --}}
    {{-- HEADER                                     --}}
    {{-- ========================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventaires</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $compteurs['total'] }} inventaire(s) au total
                @if($compteurs['en_cours'] > 0)
                    &middot; <span class="text-blue-600 font-medium">{{ $compteurs['en_cours'] }} actif(s)</span>
                @endif
            </p>
        </div>
        @if($isAdmin)
            <a
                href="{{ route('inventaires.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouvel inventaire
            </a>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- INVENTAIRE EN COURS (mise en avant)        --}}
    {{-- ========================================== --}}
    @if($inventaireActif && $inventaireActif->statut === 'en_cours')
        @php $statsActif = $inventaireActif->getStatistiques(); @endphp
        <div class="mb-6 bg-white rounded-xl shadow-sm border-2 border-blue-200 overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900">Inventaire {{ $inventaireActif->annee }}</h2>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">En cours</span>
                    </div>
                    <a
                        href="{{ route('inventaires.show', $inventaireActif) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Tableau de bord
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Barre de progression --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm text-gray-600">Progression</span>
                        <span class="text-sm font-bold text-gray-900">{{ round($statsActif['progression'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div
                            class="h-2.5 rounded-full transition-all duration-500 {{ $statsActif['progression'] >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                            style="width: {{ min($statsActif['progression'], 100) }}%"></div>
                    </div>
                </div>

                {{-- Stats compactes --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Localisations</p>
                            <p class="text-sm font-bold text-gray-900">{{ $statsActif['localisations_terminees'] }}<span class="text-gray-400 font-normal">/{{ $statsActif['total_localisations'] }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Présents</p>
                            <p class="text-sm font-bold text-green-600">{{ $statsActif['scans_presents'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Déplacés</p>
                            <p class="text-sm font-bold text-amber-600">{{ $statsActif['scans_deplaces'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Absents</p>
                            <p class="text-sm font-bold text-red-600">{{ $statsActif['scans_absents'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- FILTRES + TABLEAU                          --}}
    {{-- ========================================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Barre de filtres compacte --}}
        <div class="px-5 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Filtres par statut (tabs) --}}
            <div class="flex items-center gap-1 flex-1">
                @php
                    $filtresStatut = [
                        'all' => 'Tous (' . $compteurs['total'] . ')',
                        'en_cours' => 'En cours',
                        'en_preparation' => 'Préparation',
                        'termine' => 'Terminés',
                        'cloture' => 'Clôturés',
                    ];
                @endphp
                @foreach($filtresStatut as $value => $label)
                    <button
                        wire:click="$set('filterStatut', '{{ $value }}')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                            {{ $filterStatut === $value
                                ? 'bg-indigo-100 text-indigo-700'
                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Filtre année --}}
            @if($this->annees->count() > 1)
                <select
                    wire:model.live="filterAnnee"
                    class="text-sm border-gray-200 rounded-lg py-1.5 pl-3 pr-8 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes les années</option>
                    @foreach($this->annees as $annee)
                        <option value="{{ $annee }}">{{ $annee }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Liste des inventaires --}}
        <div class="divide-y divide-gray-100">
            @forelse($inventaires as $inventaire)
                @php
                    $stats = $inventaire->getStatistiques();
                    $cfg = $statutConfig[$inventaire->statut] ?? $statutConfig['en_preparation'];
                    $isActif = in_array($inventaire->statut, ['en_cours', 'en_preparation']);
                @endphp
                <div class="px-5 py-4 hover:bg-gray-50/50 transition-colors {{ $inventaire->statut === 'en_cours' ? 'bg-blue-50/30' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        {{-- Colonne gauche : année + statut + dates --}}
                        <div class="flex items-center gap-4 sm:w-56 flex-shrink-0">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900 leading-none">{{ $inventaire->annee }}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                    {{ $cfg['label'] }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $inventaire->date_debut ? $inventaire->date_debut->format('d/m/Y') : '—' }}
                                    @if($inventaire->date_fin)
                                        → {{ $inventaire->date_fin->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Colonne centre : progression + stats --}}
                        <div class="flex-1 flex items-center gap-6">
                            {{-- Progression --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-400">
                                        {{ $stats['localisations_terminees'] }}/{{ $stats['total_localisations'] }} loc.
                                    </span>
                                    <span class="text-xs font-semibold text-gray-600">{{ round($stats['progression'], 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    @php
                                        $barColor = match(true) {
                                            $stats['progression'] >= 100 => 'bg-green-500',
                                            $stats['progression'] >= 50  => 'bg-blue-500',
                                            $stats['progression'] > 0    => 'bg-amber-500',
                                            default                      => 'bg-gray-300',
                                        };
                                    @endphp
                                    <div class="{{ $barColor }} h-1.5 rounded-full transition-all" style="width: {{ min($stats['progression'], 100) }}%"></div>
                                </div>
                            </div>

                            {{-- Stats mini --}}
                            <div class="hidden md:flex items-center gap-4 text-xs flex-shrink-0">
                                <div class="text-center" title="Scans effectués">
                                    <p class="font-bold text-gray-700">{{ $stats['total_scans'] }}</p>
                                    <p class="text-gray-400">scans</p>
                                </div>
                                @if($stats['taux_conformite'] > 0)
                                    <div class="text-center" title="Taux de conformité">
                                        @php
                                            $confColor = $stats['taux_conformite'] >= 90 ? 'text-green-600' : ($stats['taux_conformite'] >= 70 ? 'text-amber-600' : 'text-red-600');
                                        @endphp
                                        <p class="font-bold {{ $confColor }}">{{ round($stats['taux_conformite'], 0) }}%</p>
                                        <p class="text-gray-400">conf.</p>
                                    </div>
                                @endif
                                @if($inventaire->duree !== null)
                                    <div class="text-center" title="Durée">
                                        <p class="font-bold text-gray-700">{{ $inventaire->duree }}j</p>
                                        <p class="text-gray-400">durée</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Colonne droite : actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a
                                href="{{ route('inventaires.show', $inventaire) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                    {{ $isActif
                                        ? 'text-blue-700 bg-blue-50 hover:bg-blue-100'
                                        : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </a>

                            @if(in_array($inventaire->statut, ['termine', 'cloture']))
                                <a
                                    href="{{ route('inventaires.rapport', $inventaire) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Rapport
                                </a>
                            @endif

                            {{-- Menu contextuel pour actions admin --}}
                            @if($isAdmin)
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.outside="open = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01"/></svg>
                                    </button>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">

                                        @if($inventaire->statut === 'termine')
                                            <button
                                                wire:click="archiverInventaire({{ $inventaire->id }})"
                                                wire:confirm="Clôturer l'inventaire {{ $inventaire->annee }} ? Cette action est définitive."
                                                @click="open = false"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-green-700 hover:bg-green-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Clôturer
                                            </button>
                                        @endif

                                        @if(in_array($inventaire->statut, ['termine', 'cloture']))
                                            <a
                                                href="{{ route('inventaires.export-pdf', $inventaire) }}"
                                                @click="open = false"
                                                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Exporter PDF
                                            </a>
                                        @endif

                                        @if(in_array($inventaire->statut, ['en_preparation', 'termine', 'cloture']))
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button
                                                wire:click="supprimerInventaire({{ $inventaire->id }})"
                                                wire:confirm="Supprimer l'inventaire {{ $inventaire->annee }} ? Toutes les données seront définitivement perdues."
                                                @click="open = false"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="text-sm font-medium text-gray-900">Aucun inventaire</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">
                        @if($filterStatut !== 'all' || !empty($filterAnnee))
                            Aucun résultat pour ces filtres.
                            <button wire:click="resetFilters" class="text-indigo-600 hover:underline">Réinitialiser</button>
                        @else
                            Créez votre premier inventaire pour commencer.
                        @endif
                    </p>
                    @if($isAdmin && $filterStatut === 'all' && empty($filterAnnee))
                        <a
                            href="{{ route('inventaires.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Créer un inventaire
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($inventaires->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                <select
                    wire:model.live="perPage"
                    class="text-xs border-gray-200 rounded-lg py-1 pl-2 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10">10 / page</option>
                    <option value="20">20 / page</option>
                    <option value="50">50 / page</option>
                </select>
                <div class="text-sm">
                    {{ $inventaires->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- MESSAGES FLASH                             --}}
    {{-- ========================================== --}}
    @if(session()->has('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition
            class="fixed bottom-4 right-4 flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg z-50">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 6000)"
            x-transition
            class="fixed bottom-4 right-4 flex items-center gap-3 bg-red-600 text-white px-5 py-3 rounded-xl shadow-lg z-50">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif
</div>
