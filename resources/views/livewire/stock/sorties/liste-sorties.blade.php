<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sorties de stock</h1>
                <p class="text-gray-500 mt-1">Historique des distributions</p>
            </div>
            <a href="{{ route('stock.sorties.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle sortie
            </a>
        </div>
    </div>

    <!-- Statistique du total -->
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg shadow p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-100">Total sorties (période sélectionnée)</p>
                <p class="text-4xl font-bold mt-2">{{ number_format($totalQuantite, 0, ',', ' ') }}</p>
                <p class="text-sm text-indigo-100 mt-1">Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
            </div>
            <div class="text-6xl">📤</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div wire:loading.delay wire:target="search,filterProduit,filterDemandeur,dateDebut,dateFin" class="mb-3 text-xs text-indigo-700 bg-indigo-50 border border-indigo-200 rounded px-3 py-2">
            Mise à jour en cours...
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       wire:loading.attr="disabled"
                       wire:target="search"
                       placeholder="Rechercher..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                <select wire:model.live="filterProduit" wire:loading.attr="disabled" wire:target="filterProduit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les produits</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}">{{ $produit->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Demandeur</label>
                <select wire:model.live="filterDemandeur" wire:loading.attr="disabled" wire:target="filterDemandeur" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les demandeurs</option>
                    @foreach($demandeurs as $demandeur)
                        <option value="{{ $demandeur->id }}">{{ $demandeur->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" wire:model.live="dateDebut" wire:loading.attr="disabled" wire:target="dateDebut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" wire:model.live="dateFin" wire:loading.attr="disabled" wire:target="dateFin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50 border-b border-indigo-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Demandeur</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Par</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Observations</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider">Bon</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sorties as $sortie)
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sortie->date_sortie->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $sortie->produit->libelle ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $sortie->produit->categorie->libelle ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $sortie->demandeur->nom ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $sortie->demandeur->poste_service ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-sm font-semibold bg-indigo-100 text-indigo-800 rounded-full border border-indigo-200">
                                -{{ $sortie->quantite }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sortie->nom_createur }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sortie->observations ? Str::limit($sortie->observations, 40) : '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('stock.sorties.bon', $sortie->id) }}" target="_blank"
                               title="Imprimer le bon de sortie"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Bon
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <span class="text-6xl mb-3">📤</span>
                            <p class="text-sm font-medium text-gray-500">Aucune sortie trouvée</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">{{ $sorties->links() }}</div>
</div>
