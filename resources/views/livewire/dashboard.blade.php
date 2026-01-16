<div>
    <!-- Titre du dashboard -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-gray-500 mt-1">Vue d'ensemble de votre gestion d'inventaire</p>
    </div>

    @if($totalBiens === 0 && $totalLocalisations === 0)
        <!-- Message d'accueil pour nouvelle installation -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Bienvenue dans votre système de gestion d'inventaire !</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Pour commencer, vous pouvez :</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Créer des localisations (bureaux, ateliers, etc.)</li>
                            <li>Ajouter des immobilisations à inventorier</li>
                            <li>Démarrer votre premier inventaire</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 - Total Immobilisations -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Immobilisations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalBiens, 0, ',', ' ') }}</p>
                    <p class="text-sm text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        +{{ $biensCetteAnnee }} cette année
                    </p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
            <a href="{{ route('biens.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-4 inline-block">
                Voir toutes les immobilisations →
            </a>
        </div>

        <!-- Card 2 - Localisations -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Localisations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalLocalisations, 0, ',', ' ') }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $nombreBatiments }} bâtiments</p>
                </div>
                <div class="text-4xl">📍</div>
            </div>
            <a href="{{ route('localisations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-4 inline-block">
                Gérer les localisations →
            </a>
        </div>

        <!-- Card 3 - Inventaire en cours -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Inventaire en cours</p>
                    @if($inventaireEnCours)
                        <p class="text-lg font-bold text-gray-900 mt-2">Inventaire {{ $inventaireEnCours->annee }}</p>
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progression</span>
                                <span>{{ round($statistiquesInventaire['progression'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $statistiquesInventaire['progression'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-lg font-bold text-gray-400 mt-2">Aucun inventaire actif</p>
                    @endif
                </div>
                <div class="text-4xl">📋</div>
            </div>
            @if($inventaireEnCours)
                <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" class="text-sm text-blue-600 hover:text-blue-800 mt-4 inline-block">
                    Voir l'inventaire →
                </a>
            @endif
        </div>

        <!-- Card 4 - Valeur totale -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Valeur totale</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($valeurTotale, 0, ',', ' ') }} MRU
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Valeur déclarée</p>
                </div>
                <div class="text-4xl">💰</div>
            </div>
        </div>
    </div>

    @if($inventaireEnCours)
        <!-- Section Inventaire en cours -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Inventaire {{ $inventaireEnCours->annee }} en cours</h3>
                <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" 
                   class="text-sm text-blue-600 hover:text-blue-800">
                    Voir détails complets →
                </a>
            </div>

            <!-- Tableau récapitulatif -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localisation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Immobilisations attendues</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scannés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($localisationsInventaire as $loc)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loc['localisation'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['biens_attendus'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['biens_scannes'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $loc['progression'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ round($loc['progression'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $loc['statut'] === 'termine' ? 'bg-green-100 text-green-800' : 
                                           ($loc['statut'] === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($loc['statut']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loc['agent'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucune localisation scannée pour le moment
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" 
                 @if($inventaireEnCours) wire:poll.10s="refresh" @endif>
                <!-- Graphique 1 - Pie chart statuts -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Répartition des statuts</h4>
                    <canvas id="statutsChart" height="250"></canvas>
                </div>

                <!-- Graphique 2 - Bar chart progression par service -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Progression par service</h4>
                    <canvas id="servicesChart" height="250"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Section Activité récente -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Activité récente</h3>
        <div class="space-y-4">
            @forelse($dernieresActions as $action)
                <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="text-2xl">{{ $action['icon'] }}</div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">{{ $action['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $action['time_ago'] }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-8">Aucune activité récente</p>
            @endforelse
        </div>
    </div>

    <!-- Section Actions rapides (Admin) -->
    @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Actions rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('biens.create') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                    <span class="text-2xl mr-2">➕</span>
                    <span class="font-medium">Ajouter une immobilisation</span>
                </a>
                <a href="{{ route('localisations.create') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                    <span class="text-2xl mr-2">📍</span>
                    <span class="font-medium">Ajouter une localisation</span>
                </a>
                <a href="{{ route('inventaires.create') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                    <span class="text-2xl mr-2">📋</span>
                    <span class="font-medium">Démarrer inventaire</span>
                </a>
                <a href="{{ route('users.index') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                    <span class="text-2xl mr-2">👥</span>
                    <span class="font-medium">Gérer les utilisateurs</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($inventaireEnCours && !empty($repartitionStatuts))
            // Graphique Pie - Répartition statuts
            var statutsCtx = document.getElementById('statutsChart');
            if (statutsCtx) {
                var repartitionData = {
                    present: {{ $repartitionStatuts['present'] ?? 0 }},
                    deplace: {{ $repartitionStatuts['deplace'] ?? 0 }},
                    absent: {{ $repartitionStatuts['absent'] ?? 0 }},
                    deteriore: {{ $repartitionStatuts['deteriore'] ?? 0 }}
                };
                
                new Chart(statutsCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Présents', 'Déplacés', 'Absents', 'Détériorés'],
                        datasets: [{
                            data: [
                                repartitionData.present,
                                repartitionData.deplace,
                                repartitionData.absent,
                                repartitionData.deteriore
                            ],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(107, 114, 128, 0.8)'
                            ],
                            borderColor: [
                                'rgb(34, 197, 94)',
                                'rgb(249, 115, 22)',
                                'rgb(239, 68, 68)',
                                'rgb(107, 114, 128)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Graphique Bar - Progression par service
            var servicesCtx = document.getElementById('servicesChart');
            if (servicesCtx) {
                var progressionData = @json($progressionParService ?? []);
                if (progressionData && progressionData.length > 0) {
                    new Chart(servicesCtx, {
                        type: 'bar',
                        data: {
                            labels: progressionData.map(function(item) { return item.service; }),
                            datasets: [{
                                label: 'Progression (%)',
                                data: progressionData.map(function(item) { return item.progression; }),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            }
            @endif
        });

        // Réinitialiser les graphiques lors des mises à jour Livewire
        document.addEventListener('livewire:update', function() {
            // Les graphiques seront recréés automatiquement
        });
    </script>
</div>
