<x-layouts.app>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Corbeille des immobilisations</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Restaurer, supprimer definitivement ou exporter les elements supprimes
                </p>
            </div>
        </div>

        <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm border border-gray-200">
            <button
                @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
            >
                <span class="font-medium text-gray-900">Filtres de recherche</span>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="border-t border-gray-200 p-4">
                <form method="GET" action="{{ route('corbeille.immobilisations.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="search">Recherche globale</label>
                            <input
                                id="search"
                                name="search"
                                type="text"
                                value="{{ $search }}"
                                placeholder="NumOrdre, designation, emplacement, categorie..."
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_designation">Désignation</label>
                            <select id="filter_designation" name="filter_designation" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Toutes les désignations</option>
                                @foreach($designationOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterDesignation === (string) $option['id'])>
                                        {{ $option['label'] }} ({{ $option['id'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_categorie">Catégorie</label>
                            <select id="filter_categorie" name="filter_categorie" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Toutes les catégories</option>
                                @foreach($categorieOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterCategorie === (string) $option['id'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_emplacement">Emplacement</label>
                            <select id="filter_emplacement" name="filter_emplacement" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tous les emplacements</option>
                                @foreach($emplacementOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterEmplacement === (string) $option['id'])>
                                        {{ $option['label'] }} ({{ $option['id'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_etat">État</label>
                            <select id="filter_etat" name="filter_etat" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tous les états</option>
                                @foreach($etatOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterEtat === (string) $option['id'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_natjur">Nature Juridique</label>
                            <select id="filter_natjur" name="filter_natjur" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Toutes les natures juridiques</option>
                                @foreach($natJurOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterNatJur === (string) $option['id'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_sf">Source de Financement</label>
                            <select id="filter_sf" name="filter_sf" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Toutes les sources</option>
                                @foreach($sourceFinOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected((string) $filterSF === (string) $option['id'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="filter_date_acquisition">Année d'acquisition</label>
                            <input
                                id="filter_date_acquisition"
                                name="filter_date_acquisition"
                                type="number"
                                min="1900"
                                max="{{ now()->year + 1 }}"
                                value="{{ $filterDateAcquisition }}"
                                placeholder="Ex: {{ now()->year }}"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Afficher
                        </button>
                        <a href="{{ route('corbeille.immobilisations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Reinitialiser
                        </a>
                        <a href="{{ route('corbeille.immobilisations.export-excel', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Récupération groupée</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-designation-selection') }}" class="flex items-end gap-2">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="designation_id">Par désignation</label>
                        <select
                            id="designation_id"
                            name="designation_id"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Sélectionner une désignation</option>
                            @foreach($designationOptions as $option)
                                <option value="{{ $option['id'] }}">
                                    {{ $option['label'] }} ({{ $option['id'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors"
                        onclick="return confirm('Restaurer toutes les immobilisations de la désignation sélectionnée ?')"
                    >
                        Restaurer désignation
                    </button>
                </form>

                <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-emplacement-selection') }}" class="flex items-end gap-2">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="emplacement_id">Par emplacement</label>
                        <select
                            id="emplacement_id"
                            name="emplacement_id"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Sélectionner un emplacement</option>
                            @foreach($emplacementOptions as $option)
                                <option value="{{ $option['id'] }}">
                                    {{ $option['label'] }} ({{ $option['id'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors"
                        onclick="return confirm('Restaurer toutes les immobilisations de l\'emplacement sélectionné ?')"
                    >
                        Restaurer emplacement
                    </button>
                </form>
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
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <form method="POST" action="{{ route('corbeille.immobilisations.restore', $row->id) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                                onclick="return confirm('Restaurer cette immobilisation ?')"
                                            >
                                                Restaurer
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-designation', $row->idDesignation) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 text-xs font-medium bg-sky-600 text-white rounded hover:bg-sky-700"
                                                onclick="return confirm('Restaurer toutes les immobilisations de cette designation ?')"
                                            >
                                                Restaurer designation
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('corbeille.immobilisations.restore-by-emplacement', $row->idEmplacement) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 text-xs font-medium bg-violet-600 text-white rounded hover:bg-violet-700"
                                                onclick="return confirm('Restaurer toutes les immobilisations de cet emplacement ?')"
                                            >
                                                Restaurer emplacement
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('corbeille.immobilisations.force-delete', $row->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700"
                                                onclick="return confirm('Suppression definitive. Confirmer ?')"
                                            >
                                                Supprimer definitivement
                                            </button>
                                        </form>
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
    </div>
</x-layouts.app>
