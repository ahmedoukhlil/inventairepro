<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Collecte initiale
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Consultation des lignes de collecte et export Excel
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('collecte-initiale.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="emplacement">Emplacement</label>
                    <input
                        id="emplacement"
                        name="emplacement"
                        type="text"
                        value="{{ $filters['emplacement'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Bureau DG"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="lot_uid">Lot UID</label>
                    <input
                        id="lot_uid"
                        name="lot_uid"
                        type="text"
                        value="{{ $filters['lot_uid'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="UUID lot"
                    >
                </div>

                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Afficher
                    </button>
                    <a href="{{ route('collecte-initiale.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Reinitialiser
                    </a>
                    <a
                        href="{{ route('collecte-initiale.export-excel', request()->query()) }}"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                    >
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
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Lot UID</th>
                            <th class="px-3 py-2 text-left">Emplacement</th>
                            <th class="px-3 py-2 text-left">Designation</th>
                            <th class="px-3 py-2 text-left">Quantite</th>
                            <th class="px-3 py-2 text-left">Etat</th>
                            <th class="px-3 py-2 text-left">Observations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ optional($row->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2 font-mono text-xs">{{ $row->lot_uid }}</td>
                                <td class="px-3 py-2">{{ $row->emplacement_label }}</td>
                                <td class="px-3 py-2">{{ $row->designation }}</td>
                                <td class="px-3 py-2">{{ $row->quantite }}</td>
                                <td class="px-3 py-2">{{ $row->etat ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $row->observations ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                    Aucune ligne de collecte trouvee.
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
