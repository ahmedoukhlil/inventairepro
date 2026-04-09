<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="flex items-start gap-3 mb-8">
            <a href="<?php echo e(route('stock.fournisseurs.index')); ?>"
               class="mt-1 flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php echo e($fournisseur ? 'Modifier le fournisseur' : 'Nouveau fournisseur'); ?>

                </h1>
                <p class="text-gray-500 mt-0.5">
                    <?php echo e($fournisseur ? 'Modifiez les informations du fournisseur' : 'Créez un nouveau fournisseur'); ?>

                </p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Informations du fournisseur
                    </h2>
                </div>
                <div class="p-6 space-y-5">

                    
                    <div>
                        <label for="libelle" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Nom du fournisseur <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="libelle"
                               wire:model="libelle"
                               placeholder="Ex : Société ABC, SARL Fournitures…"
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
                        <label for="observations" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Observations
                            <span class="font-normal text-gray-400 normal-case">(optionnel)</span>
                        </label>
                        <textarea id="observations"
                                  wire:model="observations"
                                  rows="4"
                                  placeholder="Contact, adresse, notes…"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fournisseur): ?>
                        <?php $nbEntrees = $fournisseur->entrees()->count(); ?>
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">
                                    <?php echo e($nbEntrees); ?> entrée<?php echo e($nbEntrees > 1 ? 's' : ''); ?> liée<?php echo e($nbEntrees > 1 ? 's' : ''); ?> à ce fournisseur
                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nbEntrees > 0): ?>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        La suppression est impossible tant que des entrées y sont rattachées.
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nbEntrees > 0): ?>
                                <a href="<?php echo e(route('stock.entrees.index')); ?>"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium flex-shrink-0">
                                    Voir les entrées →
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel"
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
                    <?php echo e($fournisseur ? 'Mettre à jour' : 'Créer le fournisseur'); ?>

                </button>
            </div>

        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/stock/fournisseurs/form-fournisseur.blade.php ENDPATH**/ ?>