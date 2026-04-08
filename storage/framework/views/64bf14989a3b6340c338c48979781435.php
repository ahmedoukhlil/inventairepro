<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sorties de stock</h1>
                <p class="text-gray-500 mt-1">Historique des distributions</p>
            </div>
            <a href="<?php echo e(route('stock.sorties.create')); ?>" 
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
                <p class="text-4xl font-bold mt-2"><?php echo e(number_format($totalQuantite, 0, ',', ' ')); ?></p>
                <p class="text-sm text-indigo-100 mt-1">Du <?php echo e(\Carbon\Carbon::parse($dateDebut)->format('d/m/Y')); ?> au <?php echo e(\Carbon\Carbon::parse($dateFin)->format('d/m/Y')); ?></p>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($produit->id); ?>"><?php echo e($produit->libelle); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Demandeur</label>
                <select wire:model.live="filterDemandeur" wire:loading.attr="disabled" wire:target="filterDemandeur" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les demandeurs</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $demandeurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demandeur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($demandeur->id); ?>"><?php echo e($demandeur->nom); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sorties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sortie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($sortie->date_sortie->format('d/m/Y')); ?></td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($sortie->produit->libelle ?? '-'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($sortie->produit->categorie->libelle ?? ''); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo e($sortie->demandeur->nom ?? '-'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($sortie->demandeur->poste_service ?? ''); ?></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-sm font-semibold bg-indigo-100 text-indigo-800 rounded-full border border-indigo-200">
                                -<?php echo e($sortie->quantite); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($sortie->nom_createur); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($sortie->observations ? Str::limit($sortie->observations, 40) : '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <span class="text-6xl mb-3">📤</span>
                            <p class="text-sm font-medium text-gray-500">Aucune sortie trouvée</p>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6"><?php echo e($sorties->links()); ?></div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\stock\sorties\liste-sorties.blade.php ENDPATH**/ ?>