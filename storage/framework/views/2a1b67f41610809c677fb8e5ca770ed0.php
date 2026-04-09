<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="flex items-start gap-3 mb-8">
            <a href="<?php echo e(route('stock.produits.index')); ?>"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php echo e($produit ? 'Modifier le produit' : 'Nouveau produit'); ?>

                </h1>
                <p class="text-gray-500 mt-0.5">
                    <?php echo e($produit ? 'Modifiez les informations du produit' : 'Ajoutez un nouveau produit au stock'); ?>

                </p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Informations du produit
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    
                    <div class="md:col-span-2">
                        <label for="libelle" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="libelle"
                               wire:model="libelle"
                               placeholder="Ex : Ramettes A4, Stylos bille…"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                      <?php $__errorArgs = ['libelle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['libelle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'categorie_id','options' => $this->categorieOptions,'placeholder' => 'Sélectionner une catégorie','searchPlaceholder' => 'Rechercher...','noResultsText' => 'Aucune catégorie trouvée']);

$key = 'categorie-select';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1381133687-0', 'categorie-select');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categorie_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Magasin <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'magasin_id','options' => $this->magasinOptions,'placeholder' => 'Sélectionner un magasin','searchPlaceholder' => 'Rechercher...','noResultsText' => 'Aucun magasin trouvé']);

$key = 'magasin-select';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1381133687-1', 'magasin-select');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['magasin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="md:col-span-2">
                        <label for="stockage" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Emplacement dans le magasin
                        </label>
                        <input type="text"
                               id="stockage"
                               wire:model="stockage"
                               placeholder="Ex : Étagère A3, Rayon 2…"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="mt-1 text-xs text-gray-400">Précisez l'emplacement exact dans le magasin</p>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        Gestion du stock
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="max-w-xs">
                        <label for="seuil_alerte" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Seuil d'alerte <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="seuil_alerte"
                               wire:model="seuil_alerte"
                               min="0"
                               class="w-full px-4 py-2.5 text-sm border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                      <?php $__errorArgs = ['seuil_alerte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['seuil_alerte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                                <?php echo e($message); ?>

                            </p>
                        <?php else: ?>
                            <p class="mt-1 text-xs text-gray-400">Une alerte sera déclenchée si le stock ≤ à ce seuil</p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$produit): ?>
                        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-0.5">Stock initialisé à 0</p>
                                <p class="text-blue-700 text-xs leading-relaxed">
                                    Le stock initial et le stock actuel seront définis à <strong>0</strong> à la création.
                                    Vous pourrez ensuite approvisionner ce produit via une <strong>opération d'entrée de stock</strong>.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produit): ?>
                        <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                            <div class="text-center">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total entrées</p>
                                <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e(number_format($produit->entrees()->sum('quantite'), 0, ',', ' ')); ?></p>
                            </div>
                            <div class="text-center border-x border-gray-200">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total sorties</p>
                                <p class="text-2xl font-bold text-violet-600 mt-1"><?php echo e(number_format($produit->sorties()->sum('quantite'), 0, ',', ' ')); ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock actuel</p>
                                <p class="text-2xl font-bold
                                    <?php echo e($produit->en_alerte ? 'text-red-600' : ($produit->stock_faible ? 'text-amber-500' : 'text-emerald-600')); ?> mt-1">
                                    <?php echo e(number_format($produit->stock_actuel, 0, ',', ' ')); ?>

                                </p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descriptions
                        <span class="text-xs font-normal text-gray-400">(optionnel)</span>
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="descriptif" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Descriptif</label>
                        <textarea id="descriptif"
                                  wire:model="descriptif"
                                  rows="4"
                                  placeholder="Description détaillée du produit…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                    <div>
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Observations</label>
                        <textarea id="observations"
                                  wire:model="observations"
                                  rows="4"
                                  placeholder="Notes supplémentaires…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        wire:click="cancel"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 104.582 9H4"/>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php echo e($produit ? 'Mettre à jour' : 'Créer le produit'); ?>

                </button>
            </div>

        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/stock/produits/form-produit.blade.php ENDPATH**/ ?>