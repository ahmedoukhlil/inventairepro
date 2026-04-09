<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Magasins</h1>
            <p class="text-gray-500 mt-1">Gestion des magasins de stockage</p>
        </div>
        <a href="{{ route('stock.magasins.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau magasin
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Recherche --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div wire:loading.delay wire:target="search,confirmDelete,delete"
             class="mb-4 flex items-center gap-2 text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Mise à jour en cours…
        </div>
        <div class="relative max-w-sm">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   wire:loading.attr="disabled"
                   wire:target="search"
                   placeholder="Rechercher un magasin…"
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Magasin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Localisation</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Produits</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Observations</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($magasins as $magasin)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $magasin->magasin }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $magasin->localisation ?: '—' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $magasin->produits_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $magasin->produits_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-gray-500 truncate max-w-[220px]">
                                    {{ $magasin->observations ? Str::limit($magasin->observations, 55) : '—' }}
                                </p>
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('stock.magasins.edit', $magasin->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Modifier
                                    </a>
                                    <button wire:click="confirmDelete({{ $magasin->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete,delete"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors disabled:opacity-40
                                                   {{ $magasin->produits_count > 0
                                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none'
                                                        : 'bg-red-50 text-red-600 hover:bg-red-100' }}"
                                            @if($magasin->produits_count > 0) title="Impossible : contient {{ $magasin->produits_count }} produit(s)" @endif>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Suppr.
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Aucun magasin trouvé</p>
                                    @if($search)
                                        <button wire:click="$set('search', '')" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Effacer la recherche
                                        </button>
                                    @else
                                        <p class="text-xs text-gray-400">Commencez par créer votre premier magasin</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        @if($magasins->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-500">
                    {{ $magasins->firstItem() }}–{{ $magasins->lastItem() }} sur {{ $magasins->total() }} magasin{{ $magasins->total() > 1 ? 's' : '' }}
                </p>
                {{ $magasins->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400">{{ $magasins->total() }} magasin{{ $magasins->total() > 1 ? 's' : '' }}</p>
            </div>
        @endif
    </div>

    {{-- Modal suppression --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900">Confirmer la suppression</h3>
                        <p class="text-sm text-gray-500 mt-1">Êtes-vous sûr de vouloir supprimer ce magasin ? Cette action est irréversible.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="cancelDelete"
                            wire:loading.attr="disabled" wire:target="delete"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            wire:loading.attr="disabled" wire:target="delete"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                        <svg wire:loading wire:target="delete" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
