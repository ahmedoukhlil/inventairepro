<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle entrée de stock</h1>
            <p class="text-gray-500 mt-1">Enregistrez un approvisionnement</p>
        </div>

        <form wire:submit.prevent="save">
            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_entree" class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'entrée <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_entree" wire:model="date_entree" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg <?php $__errorArgs = ['date_entree'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_entree'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label for="reference_commande" class="block text-sm font-medium text-gray-700 mb-1">Référence commande</label>
                        <input type="text" id="reference_commande" wire:model="reference_commande" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="BC-2026-001">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Produit <span class="text-red-500">*</span>
                    </label>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'produit_id','options' => $this->produitOptions,'placeholder' => 'Sélectionner un produit','searchPlaceholder' => 'Rechercher un produit...','noResultsText' => 'Aucun produit trouvé']);

$key = 'produit-select';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-561879310-0', 'produit-select');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['produit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->produitSelectionne): ?>
                        <div class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Informations du produit</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Stock actuel :</span>
                                    <span class="font-semibold text-gray-900 ml-2"><?php echo e(number_format($this->produitSelectionne->stock_actuel, 0, ',', ' ')); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Stock initial :</span>
                                    <span class="font-semibold text-gray-900 ml-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->produitSelectionne->stock_initial == 0): ?>
                                            <span class="text-orange-600">Non défini</span>
                                        <?php else: ?>
                                            <?php echo e(number_format($this->produitSelectionne->stock_initial, 0, ',', ' ')); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Seuil d'alerte :</span>
                                    <span class="font-semibold text-gray-900 ml-2"><?php echo e(number_format($this->produitSelectionne->seuil_alerte, 0, ',', ' ')); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Magasin :</span>
                                    <span class="font-semibold text-gray-900 ml-2"><?php echo e($this->produitSelectionne->magasin->magasin ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->produitSelectionne->stock_initial == 0): ?>
                                <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800">
                                        <strong>⚠️ Première entrée :</strong> Cette entrée définira le <strong>stock initial</strong> du produit.
                                    </p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->produitSelectionne->en_alerte): ?>
                                <div class="mt-2 bg-red-50 border border-red-200 rounded p-2">
                                    <p class="text-xs text-red-800">
                                        <strong>⚠️ Alerte :</strong> Le stock est en dessous du seuil d'alerte !
                                    </p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fournisseur <span class="text-red-500">*</span>
                    </label>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'fournisseur_id','options' => $this->fournisseurOptions,'placeholder' => 'Sélectionner un fournisseur','searchPlaceholder' => 'Rechercher un fournisseur...','noResultsText' => 'Aucun fournisseur trouvé']);

$key = 'fournisseur-select';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-561879310-1', 'fournisseur-select');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantité <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantite" wire:model="quantite" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea id="observations" wire:model="observations" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Notes..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer l'entrée
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/stock/entrees/form-entree.blade.php ENDPATH**/ ?>