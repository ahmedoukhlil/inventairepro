<div>
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Historique des transferts</h1>
                <p class="text-gray-500 mt-1">Consulter l'historique de tous les transferts d'immobilisations</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?php echo e(route('biens.transfert')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau transfert
                </a>
                <a href="<?php echo e(route('biens.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Recherche -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Ordre, emplacement, raison..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            <!-- Groupe -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Groupe de transfert</label>
                <select 
                    wire:model.live="filterGroupe"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">Tous les groupes</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($groupe); ?>"><?php echo e($groupe); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            <!-- Date début -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input 
                    type="date" 
                    wire:model.live="filterDateDebut"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            <!-- Date fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input 
                    type="date" 
                    wire:model.live="filterDateFin"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterGroupe || $filterDateDebut || $filterDateFin): ?>
            <div class="mt-4">
                <button 
                    wire:click="resetFilters"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Réinitialiser les filtres
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Tableau de l'historique -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Immobilisation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ancien emplacement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nouvel emplacement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groupe</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $historique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($transfert->date_transfert->format('d/m/Y H:i')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Ordre: <?php echo e($transfert->NumOrdre); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transfert->immobilisation && $transfert->immobilisation->designation): ?>
                                    <br><span class="text-xs text-gray-500"><?php echo e($transfert->immobilisation->designation->Designation); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div class="font-medium"><?php echo e($transfert->ancien_emplacement_libelle ?? 'Sans emplacement'); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($transfert->ancien_affectation_libelle ?? 'N/A'); ?> 
                                        → <?php echo e($transfert->ancien_localisation_libelle ?? 'N/A'); ?>

                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div class="font-medium text-indigo-600"><?php echo e($transfert->nouveau_emplacement_libelle); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($transfert->nouveau_affectation_libelle); ?> 
                                        → <?php echo e($transfert->nouveau_localisation_libelle); ?>

                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo e($transfert->raison ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($transfert->utilisateur->users ?? 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transfert->groupe_transfert_id): ?>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-medium">
                                        <?php echo e($transfert->groupe_transfert_id); ?>

                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">Aucun transfert trouvé</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($historique->links()); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/biens/historique-transferts.blade.php ENDPATH**/ ?>