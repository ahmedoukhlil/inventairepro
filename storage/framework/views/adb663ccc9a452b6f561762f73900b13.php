<div>
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="<?php echo e(route('stock.produits.index')); ?>" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo e($produit->libelle); ?></h1>
                    <p class="text-gray-500 mt-1"><?php echo e($produit->categorie->libelle ?? 'Sans catégorie'); ?></p>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && auth()->user()->canManageStock()): ?>
                <a href="<?php echo e(route('stock.produits.edit', $produit->id)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Stock actuel -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stock actuel</p>
                    <p class="text-3xl font-bold mt-2 <?php echo e($produit->en_alerte ? 'text-red-600' : ($produit->stock_faible ? 'text-yellow-600' : 'text-green-600')); ?>">
                        <?php echo e(number_format($produit->stock_actuel, 0, ',', ' ')); ?>

                    </p>
                    <p class="text-xs text-gray-500 mt-1">sur <?php echo e(number_format($produit->stock_initial, 0, ',', ' ')); ?> initial</p>
                </div>
                <div class="text-4xl">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produit->en_alerte): ?>
                        🔴
                    <?php elseif($produit->stock_faible): ?>
                        🟡
                    <?php else: ?>
                        🟢
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Seuil d'alerte -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Seuil d'alerte</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo e($produit->seuil_alerte); ?></p>
                    <p class="text-xs <?php echo e($produit->en_alerte ? 'text-red-600 font-semibold' : 'text-gray-500'); ?> mt-1">
                        <?php echo e($produit->en_alerte ? '⚠️ Alerte active' : 'Stock suffisant'); ?>

                    </p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
        </div>

        <!-- Total entrées -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total entrées</p>
                    <p class="text-3xl font-bold text-green-600 mt-2"><?php echo e(number_format($produit->total_entrees, 0, ',', ' ')); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($produit->entrees()->count()); ?> entrée(s)</p>
                </div>
                <div class="text-4xl">📥</div>
            </div>
        </div>

        <!-- Total sorties -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total sorties</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-2"><?php echo e(number_format($produit->total_sorties, 0, ',', ' ')); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($produit->sorties()->count()); ?> sortie(s)</p>
                </div>
                <div class="text-4xl">📤</div>
            </div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Magasin</p>
                <p class="text-sm text-gray-900 mt-1">🏪 <?php echo e($produit->magasin->magasin ?? '-'); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($produit->magasin->localisation ?? ''); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produit->stockage): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Emplacement</p>
                    <p class="text-sm text-gray-900 mt-1"><?php echo e($produit->stockage); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produit->descriptif): ?>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">Descriptif</p>
                    <p class="text-sm text-gray-900 mt-1"><?php echo e($produit->descriptif); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produit->observations): ?>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">Observations</p>
                    <p class="text-sm text-gray-900 mt-1"><?php echo e($produit->observations); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Onglets -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex">
                <button wire:click="setOnglet('info')" 
                        class="px-6 py-3 text-sm font-medium <?php echo e($onglet === 'info' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    📋 Informations
                </button>
                <button wire:click="setOnglet('entrees')" 
                        class="px-6 py-3 text-sm font-medium <?php echo e($onglet === 'entrees' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    📥 Entrées (<?php echo e($produit->entrees()->count()); ?>)
                </button>
                <button wire:click="setOnglet('sorties')" 
                        class="px-6 py-3 text-sm font-medium <?php echo e($onglet === 'sorties' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    📤 Sorties (<?php echo e($produit->sorties()->count()); ?>)
                </button>
                <button wire:click="setOnglet('historique')" 
                        class="px-6 py-3 text-sm font-medium <?php echo e($onglet === 'historique' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    📊 Historique complet
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Onglet Info -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($onglet === 'info'): ?>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600">Stock initial</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($produit->stock_initial); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600">Stock actuel</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($produit->stock_actuel); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600">Seuil alerte</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($produit->seuil_alerte); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600">Pourcentage</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(round($produit->pourcentage_stock, 1)); ?>%</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Onglet Entrées -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($onglet === 'entrees'): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entrees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($entree->date_entree->format('d/m/Y')); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($entree->fournisseur->libelle ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($entree->reference_commande ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 text-sm font-semibold bg-green-100 text-green-800 rounded-full">
                                            +<?php echo e($entree->quantite); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($entree->nom_createur); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Aucune entrée enregistrée</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Onglet Sorties -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($onglet === 'sorties'): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Demandeur</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observations</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sorties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sortie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($sortie->date_sortie->format('d/m/Y')); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div><?php echo e($sortie->demandeur->nom ?? '-'); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($sortie->demandeur->poste_service ?? ''); ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 text-sm font-semibold bg-indigo-100 text-indigo-800 rounded-full">
                                            -<?php echo e($sortie->quantite); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($sortie->nom_createur); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($sortie->observations ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Aucune sortie enregistrée</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Onglet Historique complet -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($onglet === 'historique'): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiers</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $historique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mouvement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e(\Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y')); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mouvement['type'] === 'entree'): ?>
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">📥 Entrée</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded">📤 Sortie</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($mouvement['tiers']); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-semibold <?php echo e($mouvement['type'] === 'entree' ? 'text-green-600' : 'text-indigo-600'); ?>">
                                            <?php echo e($mouvement['type'] === 'entree' ? '+' : '-'); ?><?php echo e($mouvement['quantite']); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($mouvement['createur']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Aucun mouvement enregistré</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\stock\produits\detail-produit.blade.php ENDPATH**/ ?>