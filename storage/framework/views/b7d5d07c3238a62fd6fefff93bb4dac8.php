<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle sortie de stock</h1>
            <p class="text-gray-500 mt-1">Enregistrez une distribution de produit</p>
        </div>

        <form wire:submit.prevent="save">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 space-y-6">
                
                <div>
                    <label for="date_sortie" class="block text-sm font-medium text-gray-700 mb-1">
                        Date de sortie <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date_sortie" wire:model="date_sortie" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 <?php $__errorArgs = ['date_sortie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_sortie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-842020301-0', 'produit-select');

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
                </div>

                <!-- Affichage du stock disponible -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produitSelectionne): ?>
                    <div class="<?php if($produitSelectionne->en_alerte): ?> bg-red-50 border-2 border-red-300 <?php elseif($produitSelectionne->stock_faible): ?> bg-yellow-50 border-2 border-yellow-300 <?php else: ?> bg-indigo-50 border-2 border-indigo-300 <?php endif; ?> rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Stock disponible</p>
                                <p class="text-3xl font-bold <?php if($produitSelectionne->en_alerte): ?> text-red-700 <?php elseif($produitSelectionne->stock_faible): ?> text-yellow-700 <?php else: ?> text-indigo-700 <?php endif; ?> mt-1">
                                    <?php echo e(number_format($stockDisponible, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-gray-700 mt-1">
                                    Seuil d'alerte : <span class="font-semibold"><?php echo e($produitSelectionne->seuil_alerte); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produitSelectionne->en_alerte): ?>
                                        <span class="text-red-700 font-bold ml-2">⚠️ ALERTE ACTIVE</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                            <div class="text-4xl">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($produitSelectionne->en_alerte): ?> 🔴
                                <?php elseif($produitSelectionne->stock_faible): ?> 🟡
                                <?php else: ?> 🟢
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Demandeur <span class="text-red-500">*</span>
                    </label>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'demandeur_id','options' => $this->demandeurOptions,'placeholder' => 'Sélectionner un demandeur','searchPlaceholder' => 'Rechercher un demandeur...','noResultsText' => 'Aucun demandeur trouvé']);

$key = 'demandeur-select';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-842020301-1', 'demandeur-select');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['demandeur_id'];
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
                    <input type="number" id="quantite" wire:model="quantite" min="1" <?php if($stockDisponible > 0): ?> max="<?php echo e($stockDisponible); ?>" <?php endif; ?>
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 <?php $__errorArgs = ['quantite'];
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockDisponible > 0): ?>
                        <p class="mt-1 text-sm text-gray-600 font-medium">Maximum disponible : <span class="text-indigo-600 font-bold"><?php echo e(number_format($stockDisponible, 0, ',', ' ')); ?></span></p>
                    <?php elseif($produitSelectionne): ?>
                        <p class="mt-1 text-sm text-red-700 font-bold">⚠️ Stock épuisé - Impossible de créer une sortie</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea id="observations" wire:model="observations" rows="3"
                              class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="Notes..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors font-medium">Annuler</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$produitSelectionne || $stockDisponible <= 0): ?>
                        <button type="submit" 
                                disabled
                                style="background-color: #9ca3af !important; color: #ffffff !important; border: 2px solid #6b7280 !important;"
                                class="px-4 py-2 rounded-lg inline-flex items-center font-medium shadow-md cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: #ffffff !important;">Enregistrer la sortie</span>
                        </button>
                    <?php else: ?>
                        <button type="submit" 
                                style="background-color: #4f46e5 !important; color: #ffffff !important; border: 2px solid #4338ca !important;"
                                class="px-4 py-2 rounded-lg inline-flex items-center font-medium shadow-md transition-all duration-200 focus:outline-none"
                                onmouseover="this.style.backgroundColor='#4338ca'"
                                onmouseout="this.style.backgroundColor='#4f46e5'"
                                onclick="this.style.backgroundColor='#3730a3'">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: #ffffff !important; font-weight: 600;">Enregistrer la sortie</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/stock/sorties/form-sortie.blade.php ENDPATH**/ ?>