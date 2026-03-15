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

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('corbeille.immobilisations.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="search">Recherche</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ $search }}"
                        placeholder="NumOrdre, designation, idDesignation..."
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
                <div class="flex items-end gap-2">
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

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
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
