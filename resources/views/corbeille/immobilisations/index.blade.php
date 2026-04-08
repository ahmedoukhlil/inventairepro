<x-layouts.app>
    <div
        class="space-y-4"
        x-data="{
            deleteModalOpen: false,
            deleteAction: '',
            deleteLabel: '',
        }"
    >
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Corbeille des immobilisations</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Restaurer, supprimer définitivement ou exporter les éléments supprimés
                </p>
            </div>
        </div>

        @php
            $hasActiveFilters = !empty($filterDesignation) || !empty($filterEmplacement) || !empty($filterCategorie) || !empty($filterEtat) || !empty($filterNatJur) || !empty($filterSF) || !empty($filterDateAcquisition) || !empty($search);
        @endphp

        <div x-data="{ open: {{ $hasActiveFilters ? 'true' : 'false' }} }" class="bg-white rounded-lg shadow-sm border border-gray-200">
            <button
                @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <span class="font-medium text-gray-900">Filtres de recherche</span>
                    @if($hasActiveFilters)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Actifs</span>
                    @endif
                </div>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="border-t border-gray-200 p-4"
                style="overflow: visible !important;"
                @option-selected.window="$nextTick(() => $refs.filterForm.submit())"
                @option-cleared.window="$nextTick(() => $refs.filterForm.submit())"
            >
                <form x-ref="filterForm" method="GET" action="{{ route('corbeille.immobilisations.index') }}" class="space-y-4">

                    {{-- Ligne 1 : Recherche + Désignation + Catégorie --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="search">Recherche globale</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input
                                    id="search"
                                    name="search"
                                    type="text"
                                    value="{{ $search }}"
                                    placeholder="NumOrdre, désignation, emplacement, catégorie..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    x-on:input.debounce.600ms="$refs.filterForm.submit()"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Désignation</label>
                            <livewire:components.searchable-select
                                name="filter_designation"
                                :value="(string) ($filterDesignation ?? '')"
                                :options="collect($designationOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'] . ' (' . $option['id'] . ')',
                                ])->prepend(['value' => '', 'text' => 'Toutes les désignations'])->toArray()"
                                placeholder="Toutes les désignations"
                                search-placeholder="Rechercher une désignation..."
                                no-results-text="Aucune désignation trouvée"
                                :allow-clear="true"
                                :key="'corbeille-filter-designation-' . ($filterDesignation ?: 'all')"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <livewire:components.searchable-select
                                name="filter_categorie"
                                :value="(string) ($filterCategorie ?? '')"
                                :options="collect($categorieOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les catégories'])->toArray()"
                                placeholder="Toutes les catégories"
                                search-placeholder="Rechercher une catégorie..."
                                no-results-text="Aucune catégorie trouvée"
                                :allow-clear="true"
                                :key="'corbeille-filter-categorie-' . ($filterCategorie ?: 'all')"
                            />
                        </div>
                    </div>

                    {{-- Ligne 2 : Emplacement encadré --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Filtrage par emplacement
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                            <livewire:components.searchable-select
                                name="filter_emplacement"
                                :value="(string) ($filterEmplacement ?? '')"
                                :options="collect($emplacementOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'] . ' (' . $option['id'] . ')',
                                ])->prepend(['value' => '', 'text' => 'Tous les emplacements'])->toArray()"
                                placeholder="Tous les emplacements"
                                search-placeholder="Rechercher un emplacement..."
                                no-results-text="Aucun emplacement trouvé"
                                :allow-clear="true"
                                :key="'corbeille-filter-emplacement-' . ($filterEmplacement ?: 'all')"
                            />
                        </div>
                    </div>

                    {{-- Ligne 3 : État, Nature Juridique, Source Financement, Année --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                            <livewire:components.searchable-select
                                name="filter_etat"
                                :value="(string) ($filterEtat ?? '')"
                                :options="collect($etatOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Tous les états'])->toArray()"
                                placeholder="Tous les états"
                                search-placeholder="Rechercher un état..."
                                no-results-text="Aucun état trouvé"
                                :allow-clear="true"
                                :key="'corbeille-filter-etat-' . ($filterEtat ?: 'all')"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nature Juridique</label>
                            <livewire:components.searchable-select
                                name="filter_natjur"
                                :value="(string) ($filterNatJur ?? '')"
                                :options="collect($natJurOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les natures juridiques'])->toArray()"
                                placeholder="Toutes les natures juridiques"
                                search-placeholder="Rechercher une nature juridique..."
                                no-results-text="Aucune nature juridique trouvée"
                                :allow-clear="true"
                                :key="'corbeille-filter-natjur-' . ($filterNatJur ?: 'all')"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source de Financement</label>
                            <livewire:components.searchable-select
                                name="filter_sf"
                                :value="(string) ($filterSF ?? '')"
                                :options="collect($sourceFinOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les sources'])->toArray()"
                                placeholder="Toutes les sources"
                                search-placeholder="Rechercher une source de financement..."
                                no-results-text="Aucune source trouvée"
                                :allow-clear="true"
                                :key="'corbeille-filter-sf-' . ($filterSF ?: 'all')"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Année d'acquisition</label>
                            <input
                                id="filter_date_acquisition"
                                name="filter_date_acquisition"
                                type="number"
                                min="1900"
                                max="{{ now()->year + 1 }}"
                                value="{{ $filterDateAcquisition }}"
                                placeholder="Ex: {{ now()->year }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                x-on:change="$refs.filterForm.submit()"
                            >
                        </div>
                    </div>

                    {{-- Pied de filtres --}}
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Rechercher
                            </button>
                            <a href="{{ route('corbeille.immobilisations.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Réinitialiser
                            </a>
                            <a href="{{ route('corbeille.immobilisations.export-excel', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </a>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">{{ $rows->total() }}</span> élément(s) trouvé(s)
                        </div>
                    </div>
                </form>

                @if(!empty($filterDesignation) || !empty($filterEmplacement))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-semibold text-gray-700">Restaurer en lot :</span>

                            @if(!empty($filterDesignation))
                                <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-designation-selection') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="designation_id" value="{{ $filterDesignation }}">
                                    @php
                                        $selectedDesignation = collect($designationOptions)->firstWhere('id', (int) $filterDesignation);
                                    @endphp
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors text-sm"
                                        onclick="return confirm('Restaurer toutes les immobilisations de la désignation : {{ addslashes($selectedDesignation['label'] ?? '') }} ?')"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer désignation : {{ Str::limit($selectedDesignation['label'] ?? '?', 30) }}
                                    </button>
                                </form>
                            @endif

                            @if(!empty($filterEmplacement))
                                <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-emplacement-selection') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="emplacement_id" value="{{ $filterEmplacement }}">
                                    @php
                                        $selectedEmplacement = collect($emplacementOptions)->firstWhere('id', (int) $filterEmplacement);
                                    @endphp
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors text-sm"
                                        onclick="return confirm('Restaurer toutes les immobilisations de l\'emplacement : {{ addslashes($selectedEmplacement['label'] ?? '') }} ?')"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer emplacement : {{ Str::limit($selectedEmplacement['label'] ?? '?', 30) }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="w-full overflow-x-auto overflow-y-hidden">
                <table class="min-w-[1300px] divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NumOrdre</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Désignation</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année d'acquisition</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date suppression</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $row->original_num_ordre }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-600">{{ $row->code_display }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $row->designation_display }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $row->categorie_display }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $row->etat_display }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $row->emplacement_display }}
                                        @if($row->affectation_display)
                                            <br><span class="text-xs text-gray-500">{{ $row->affectation_display }}</span>
                                        @endif
                                        @if($row->localisation_display)
                                            <br><span class="text-xs text-gray-500">{{ $row->localisation_display }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ ($row->DateAcquisition && (int) $row->DateAcquisition > 1970) ? (int) $row->DateAcquisition : 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ optional($row->deleted_at)->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('corbeille.immobilisations.restore', $row->id) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                title="Restaurer"
                                                onclick="return confirm('Restaurer cette immobilisation ?')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Restaurer
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            title="Supprimer définitivement"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-colors"
                                            x-on:click="
                                                deleteAction = @js(route('corbeille.immobilisations.force-delete', $row->id));
                                                deleteLabel = @js(($row->designation_display ?? 'Immobilisation') . ' (#' . $row->original_num_ordre . ')');
                                                deleteModalOpen = true;
                                            "
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                    Corbeille vide.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $rows->links() }}
        </div>

        <div
            x-show="deleteModalOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-modal-title"
        >
            <div class="absolute inset-0 bg-black/50" x-on:click="deleteModalOpen = false"></div>

            <div class="relative w-full max-w-lg rounded-xl bg-white shadow-xl border border-gray-200">
                <div class="p-6">
                    <h2 id="delete-modal-title" class="text-lg font-semibold text-gray-900">
                        Confirmer la suppression définitive
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Cette action est irréversible. L'élément
                        <span class="font-medium text-gray-900" x-text="deleteLabel"></span>
                        sera supprimé définitivement.
                    </p>
                </div>

                <div class="px-6 pb-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200"
                        x-on:click="deleteModalOpen = false"
                    >
                        Annuler
                    </button>
                    <form method="POST" :action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700"
                        >
                            Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
