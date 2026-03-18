<div>
    @php
        $isAdmin = auth()->user()->isAdmin();
    @endphp

    <div class="space-y-6">
        {{-- Header avec titre et actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Désignations</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $totalDesignations }} désignation(s)
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('designations.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter une désignation
                </a>
            </div>
        </div>

        @if($isAdmin)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h2 class="text-base font-semibold text-gray-900">Suppression en lot par idDesignation</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Collez les idDesignation (separes par virgules, espaces ou retours a la ligne).
                    Les immobilisations rattachees seront deplacees vers la corbeille.
                </p>

                @if($bulkFeedbackMessage)
                    <div class="mt-3 rounded-lg border px-3 py-2 text-sm
                        {{ $bulkFeedbackType === 'success'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                            : 'border-red-200 bg-red-50 text-red-800' }}">
                        {{ $bulkFeedbackMessage }}
                    </div>
                @endif

                <div class="mt-3">
                    <textarea
                        wire:model="bulkDesignationIds"
                        rows="4"
                        placeholder="Exemple: 6121, 6170, 6183&#10;6200&#10;6201"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        wire:loading.attr="disabled"
                        wire:target="moveImmosToTrashByDesignationIds"></textarea>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <div wire:loading wire:target="moveImmosToTrashByDesignationIds"
                         class="flex items-center gap-2 text-sm text-indigo-700">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Traitement par lots en cours, veuillez patienter...
                    </div>
                    <div wire:loading.remove wire:target="moveImmosToTrashByDesignationIds">&nbsp;</div>

                    <button
                        wire:click="moveImmosToTrashByDesignationIds"
                        wire:confirm="Confirmer le deplacement en corbeille des immobilisations rattachees aux idDesignation saisis ?"
                        wire:loading.attr="disabled"
                        wire:target="moveImmosToTrashByDesignationIds"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="moveImmosToTrashByDesignationIds">Envoyer les immobilisations en corbeille</span>
                        <span wire:loading wire:target="moveImmosToTrashByDesignationIds">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            En cours...
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Barre de filtres (collapsible) --}}
        <div 
            x-data="{ open: false }"
            class="bg-white rounded-lg shadow-sm border border-gray-200">
            <button 
                @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors">
                <span class="font-medium text-gray-900">Filtres de recherche</span>
                <svg 
                    class="w-5 h-5 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': open }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div 
                x-show="open"
                x-collapse
                class="border-t border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Recherche globale --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Recherche globale
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Nom, code, catégorie..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    {{-- Filtre par catégorie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Catégorie
                        </label>
                        <select 
                            wire:model.live="filterCategorie"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($this->categories as $categorie)
                                <option value="{{ $categorie->idCategorie }}">{{ $categorie->Categorie }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button 
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Réinitialiser filtres
                    </button>

                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $designations->total() }}</span> désignation(s) trouvée(s)
                    </div>
                </div>
            </div>
        </div>

        {{-- Vue tableau --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('designation')">
                                <div class="flex items-center">
                                    Désignation
                                    @if($sortField === 'designation')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('categorie')">
                                <div class="flex items-center">
                                    Catégorie
                                    @if($sortField === 'categorie')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Immobilisations
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($designations as $designation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $designation->designation }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $designation->CodeDesignation ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($designation->categorie)
                                            {{ $designation->categorie->Categorie }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $designation->immobilisations_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($isAdmin)
                                            <a 
                                                href="{{ route('designations.edit', $designation) }}"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                aria-label="Modifier la désignation {{ $designation->designation }}"
                                                title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <button 
                                                wire:click="deleteDesignation({{ $designation->id }})"
                                                wire:confirm="Supprimer la désignation '{{ $designation->designation }}' ? {{ $designation->immobilisations_count }} immobilisation(s) liee(s) seront placees dans la corbeille."
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                aria-label="Supprimer la désignation {{ $designation->designation }}"
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
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune désignation trouvée</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if($search || $filterCategorie)
                                            Essayez de modifier vos critères de recherche.
                                        @else
                                            Commencez par créer une nouvelle désignation.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($designations->hasPages())
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
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            {{ $designations->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
