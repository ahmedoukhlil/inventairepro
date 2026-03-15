<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Corbeille des immobilisations
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Restaurer, supprimer definitivement ou exporter les elements supprimes
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
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
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Date suppression</th>
                            <th class="px-3 py-2 text-left">NumOrdre</th>
                            <th class="px-3 py-2 text-left">Designation</th>
                            <th class="px-3 py-2 text-left">Emplacement</th>
                            <th class="px-3 py-2 text-left">Quantite</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ optional($row->deleted_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $row->original_num_ordre }}</td>
                                <td class="px-3 py-2">{{ $row->designation_label ?: ('Designation #' . $row->idDesignation) }}</td>
                                <td class="px-3 py-2">{{ $row->idEmplacement }}</td>
                                <td class="px-3 py-2">1</td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
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
                                <td colspan="7" class="px-3 py-8 text-center text-gray-500">
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
</x-app-layout>
