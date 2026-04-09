<div>
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Entrées de stock</h1>
            <p class="text-gray-500 mt-1">Historique des approvisionnements</p>
        </div>
        <a href="<?php echo e(route('stock.entrees.create')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle entrée
        </a>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center gap-5">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total reçu sur la période</p>
            <p class="text-3xl font-bold text-emerald-600 mt-0.5"><?php echo e(number_format($totalQuantite, 0, ',', ' ')); ?> unités</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Du <?php echo e(\Carbon\Carbon::parse($dateDebut)->format('d/m/Y')); ?>

                au <?php echo e(\Carbon\Carbon::parse($dateFin)->format('d/m/Y')); ?>

            </p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div wire:loading.delay wire:target="search,filterProduit,filterFournisseur,dateDebut,dateFin"
             class="mb-4 flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
            </svg>
            Mise à jour en cours…
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recherche</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           wire:loading.attr="disabled"
                           wire:target="search"
                           placeholder="Produit, référence…"
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Produit</label>
                <select wire:model.live="filterProduit" wire:loading.attr="disabled" wire:target="filterProduit"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <option value="">Tous les produits</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($produit->id); ?>"><?php echo e($produit->libelle); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Fournisseur</label>
                <select wire:model.live="filterFournisseur" wire:loading.attr="disabled" wire:target="filterFournisseur"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <option value="">Tous les fournisseurs</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($fournisseur->id); ?>"><?php echo e($fournisseur->libelle); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date début</label>
                <input type="date" wire:model.live="dateDebut" wire:loading.attr="disabled" wire:target="dateDebut"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>

            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Date fin</label>
                <input type="date" wire:model.live="dateFin" wire:loading.attr="disabled" wire:target="dateFin"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterProduit || $filterFournisseur): ?>
            <div class="flex items-center gap-2 mt-4 flex-wrap">
                <span class="text-xs text-gray-400">Filtres actifs :</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                        "<?php echo e($search); ?>"
                        <button wire:click="$set('search', '')" class="hover:text-blue-900">&times;</button>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterProduit): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-medium">
                        <?php echo e($produits->find($filterProduit)?->libelle ?? 'Produit'); ?>

                        <button wire:click="$set('filterProduit', '')" class="hover:text-violet-900">&times;</button>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterFournisseur): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                        <?php echo e($fournisseurs->find($filterFournisseur)?->libelle ?? 'Fournisseur'); ?>

                        <button wire:click="$set('filterFournisseur', '')" class="hover:text-emerald-900">&times;</button>
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Fournisseur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Référence</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entrees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-800"><?php echo e($entree->date_entree->format('d/m/Y')); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-gray-800"><?php echo e($entree->produit->libelle ?? '—'); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entree->produit?->categorie): ?>
                                    <p class="text-xs text-gray-400"><?php echo e($entree->produit->categorie->libelle); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-gray-700"><?php echo e($entree->fournisseur->libelle ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-gray-500 font-mono"><?php echo e($entree->reference_commande ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700">
                                    +<?php echo e($entree->quantite); ?>

                                </span>
                            </td>
                            <td class="px-6 py-3 hidden sm:table-cell">
                                <p class="text-sm text-gray-500"><?php echo e($entree->nom_createur); ?></p>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Aucune entrée trouvée</p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterProduit || $filterFournisseur): ?>
                                        <button wire:click="$set('search', ''); $set('filterProduit', ''); $set('filterFournisseur', '')"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Effacer les filtres
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrees->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-500">
                    <?php echo e($entrees->firstItem()); ?>–<?php echo e($entrees->lastItem()); ?> sur <?php echo e($entrees->total()); ?> entrée<?php echo e($entrees->total() > 1 ? 's' : ''); ?>

                </p>
                <?php echo e($entrees->links()); ?>

            </div>
        <?php else: ?>
            <div class="px-6 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400"><?php echo e($entrees->total()); ?> entrée<?php echo e($entrees->total() > 1 ? 's' : ''); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/stock/entrees/liste-entrees.blade.php ENDPATH**/ ?>