<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Stock</h1>
            <p class="text-gray-500 mt-1">Vue d'ensemble de la gestion des consommables</p>
        </div>

        <!-- Cartes statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total produits -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total produits</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalProduits }}</p>
                        <a href="{{ route('stock.produits.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Voir tous les produits →
                        </a>
                    </div>
                    <div class="text-4xl">📦</div>
                </div>
            </div>

            <!-- Produits en alerte -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Alertes stock</p>
                        <p class="text-3xl font-bold {{ $produitsEnAlerte > 0 ? 'text-red-600' : 'text-green-600' }} mt-2">
                            {{ $produitsEnAlerte }}
                        </p>
                        @if($produitsEnAlerte > 0)
                            <p class="text-sm text-red-600 mt-2">⚠️ Réappro. nécessaire</p>
                        @else
                            <p class="text-sm text-green-600 mt-2">✅ Tout est OK</p>
                        @endif
                    </div>
                    <div class="text-4xl">{{ $produitsEnAlerte > 0 ? '🔴' : '🟢' }}</div>
                </div>
            </div>

            <!-- Entrées du mois -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Entrées (ce mois)</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($entreesduMois, 0, ',', ' ') }}</p>
                        <a href="{{ route('stock.entrees.index') }}" class="text-sm text-green-600 hover:text-green-800 mt-2 inline-block">
                            Voir les entrées →
                        </a>
                    </div>
                    <div class="text-4xl">📥</div>
                </div>
            </div>

            <!-- Sorties du mois -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sorties (ce mois)</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ number_format($sortiesDuMois, 0, ',', ' ') }}</p>
                        <a href="{{ route('stock.sorties.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                            Voir les sorties →
                        </a>
                    </div>
                    <div class="text-4xl">📤</div>
                </div>
            </div>
        </div>

        <!-- Produits en alerte -->
        @if(count($produitsAlerteDetails) > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-2">🔴</span>
                        Produits en alerte ({{ $produitsEnAlerte }})
                    </h3>
                    <a href="{{ route('stock.produits.index') }}?filterStatut=alerte" class="text-sm text-blue-600 hover:text-blue-800">
                        Voir tous →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Magasin</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Seuil</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($produitsAlerteDetails as $produit)
                                <tr class="hover:bg-red-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $produit['libelle'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $produit['categorie'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $produit['magasin'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-lg font-bold text-red-600">{{ $produit['stock_actuel'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $produit['seuil_alerte'] }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <a href="{{ route('stock.produits.show', $produit['id']) }}" class="text-blue-600 hover:text-blue-900">Détails</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Stock par magasin et catégorie -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Stock par magasin -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">🏪</span>
                    Stock par magasin
                </h3>
                <div class="space-y-4">
                    @forelse($stockParMagasin as $stock)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $stock['magasin'] }}</p>
                                <p class="text-xs text-gray-500">{{ $stock['localisation'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600">{{ $stock['nombre_produits'] }}</p>
                                <p class="text-xs text-gray-500">produits</p>
                                @if($stock['produits_en_alerte'] > 0)
                                    <p class="text-xs text-red-600 font-semibold mt-1">🔴 {{ $stock['produits_en_alerte'] }} en alerte</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Aucun magasin configuré</p>
                    @endforelse
                </div>
            </div>

            <!-- Stock par catégorie -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">🏷️</span>
                    Stock par catégorie
                </h3>
                <div class="space-y-4">
                    @forelse($stockParCategorie as $stock)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $stock['categorie'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600">{{ $stock['nombre_produits'] }}</p>
                                <p class="text-xs text-gray-500">produits</p>
                                <p class="text-xs text-gray-600 mt-1">Total: {{ number_format($stock['stock_total'], 0, ',', ' ') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Aucune catégorie configurée</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Derniers mouvements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <span class="text-2xl mr-2">📊</span>
                Derniers mouvements
            </h3>
            <div class="space-y-3">
                @forelse($derniersMovements as $mouvement)
                    <div class="flex items-center justify-between p-4 {{ $mouvement['type'] === 'entree' ? 'bg-green-50' : 'bg-indigo-50' }} rounded-lg">
                        <div class="flex items-center flex-1">
                            <span class="text-2xl mr-3">{{ $mouvement['type'] === 'entree' ? '📥' : '📤' }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $mouvement['produit'] }}</p>
                                <p class="text-xs text-gray-600">
                                    {{ $mouvement['type'] === 'entree' ? 'Fourni par' : 'Demandé par' }} : {{ $mouvement['tiers'] }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Par {{ $mouvement['createur'] }} le {{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <span class="px-3 py-1 text-sm font-semibold {{ $mouvement['type'] === 'entree' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' }} rounded-full">
                                {{ $mouvement['type'] === 'entree' ? '+' : '-' }}{{ $mouvement['quantite'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">Aucun mouvement récent</p>
                @endforelse
            </div>
        </div>

        <!-- Actions rapides (Admin) -->
        @if(auth()->check() && auth()->user()->canManageStock())
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Actions rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('stock.produits.create') }}" 
                       class="flex items-center justify-center px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <span class="text-2xl mr-2">📦</span>
                        <span class="font-medium">Ajouter produit</span>
                    </a>
                    <a href="{{ route('stock.entrees.create') }}" 
                       class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                        <span class="text-2xl mr-2">📥</span>
                        <span class="font-medium">Nouvelle entrée</span>
                    </a>
                    <a href="{{ route('stock.sorties.create') }}" 
                       class="flex items-center justify-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                        <span class="text-2xl mr-2">📤</span>
                        <span class="font-medium">Nouvelle sortie</span>
                    </a>
                    <a href="{{ route('stock.magasins.index') }}" 
                       class="flex items-center justify-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                        <span class="text-2xl mr-2">⚙️</span>
                        <span class="font-medium">Paramètres</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
