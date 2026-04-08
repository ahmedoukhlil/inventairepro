<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Transfert d'immobilisations</h1>
                    <p class="text-gray-500 mt-1">Déplacer une ou plusieurs immobilisations vers un nouvel emplacement</p>
                </div>
                <a href="<?php echo e(route('biens.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>

        <!-- Messages Flash -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dernierGroupeId): ?>
                <a href="<?php echo e(route('biens.transfert.decision', $dernierGroupeId)); ?>" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer la décision de transfert
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-sm font-medium text-red-800"><?php echo e(session('error')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form wire:submit.prevent="transferer">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Colonne gauche : Sélection des immobilisations -->
                <div class="bg-white rounded-lg shadow p-6 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900">1. Sélectionner les immobilisations</h2>
                    
                    <!-- Champ de recherche -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rechercher une immobilisation
                        </label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchBien"
                            placeholder="Rechercher par NumOrdre (ex: 1001), désignation, emplacement ou localisation..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchBien)): ?>
                            <p class="mt-1 text-xs text-gray-500">
                                Recherche: "<?php echo e($searchBien); ?>" - <?php echo e(count($this->bienOptions)); ?> résultat(s) trouvé(s)
                            </p>
                        <?php else: ?>
                            <p class="mt-1 text-xs text-gray-500">
                                Tapez pour rechercher (limite: 100 résultats). Affichage des 50 premiers par défaut.
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Liste des immobilisations disponibles -->
                    <div class="border border-gray-200 rounded-lg" style="max-height: 400px; overflow-y: auto;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->bienOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $estSelectionne = in_array($option['value'], $bienIds);
                                // Utiliser les informations supplémentaires si disponibles
                                $numOrdre = $option['numOrdre'] ?? $option['value'];
                                $designation = $option['designation'] ?? 'N/A';
                                $emplacement = $option['emplacement'] ?? 'Sans emplacement';
                                $affectation = $option['affectation'] ?? 'N/A';
                                $localisation = $option['localisation'] ?? 'N/A';
                            ?>
                            <div class="p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors <?php echo e($estSelectionne ? 'bg-indigo-50 border-indigo-200' : ''); ?>">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <!-- NumOrdre en évidence -->
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-bold">
                                                Ordre: <?php echo e($numOrdre); ?>

                                            </span>
                                        </div>
                                        <!-- Désignation (nom) -->
                                        <p class="text-sm font-semibold text-gray-900 mb-2">
                                            <?php echo e($designation); ?>

                                        </p>
                                        <!-- Localisation, Affectation et Emplacement -->
                                        <div class="space-y-1 text-xs text-gray-600">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation != 'N/A'): ?>
                                                <p class="flex items-center">
                                                    <span class="mr-1">🏢</span>
                                                    <span class="font-medium">Localisation:</span>
                                                    <span class="ml-1"><?php echo e($localisation); ?></span>
                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($affectation != 'N/A'): ?>
                                                <p class="flex items-center">
                                                    <span class="mr-1">🏛️</span>
                                                    <span class="font-medium">Affectation:</span>
                                                    <span class="ml-1"><?php echo e($affectation); ?></span>
                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <p class="flex items-center">
                                                <span class="mr-1">📍</span>
                                                <span class="font-medium">Emplacement:</span>
                                                <span class="ml-1"><?php echo e($emplacement); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($estSelectionne): ?>
                                        <button 
                                            type="button"
                                            wire:click="retirerBien(<?php echo e($option['value']); ?>)"
                                            class="ml-3 px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm font-medium transition-colors">
                                            Retirer
                                        </button>
                                    <?php else: ?>
                                        <button 
                                            type="button"
                                            wire:click="ajouterBien(<?php echo e($option['value']); ?>)"
                                            class="ml-3 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm font-medium transition-colors">
                                            Ajouter
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-8 text-center text-gray-500">
                                <p>Aucune immobilisation trouvée</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Liste des immobilisations sélectionnées -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($bienIds)): ?>
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-indigo-900 mb-2">
                                Immobilisations sélectionnées (<?php echo e(count($bienIds)); ?>)
                            </h3>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensSelectionnes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between bg-white rounded p-2 text-sm">
                                        <span class="font-medium text-gray-900">
                                            Ordre: <?php echo e($bien['NumOrdre']); ?>

                                        </span>
                                        <button 
                                            type="button"
                                            wire:click="retirerBien(<?php echo e($bien['NumOrdre']); ?>)"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bienIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Colonne droite : Nouvel emplacement -->
                <div class="bg-white rounded-lg shadow p-6 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900">2. Nouvel emplacement</h2>

                    <!-- Localisation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Localisation <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'idLocalisation','options' => $this->localisationOptions,'placeholder' => 'Sélectionner une localisation','searchPlaceholder' => 'Rechercher une localisation...','noResultsText' => 'Aucune localisation trouvée']);

$key = 'localisation-select-transfer';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3182883854-0', 'localisation-select-transfer');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idLocalisation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Affectation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Affectation <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'idAffectation','options' => $this->affectationOptions,'placeholder' => 'Sélectionner une affectation','searchPlaceholder' => 'Rechercher une affectation...','noResultsText' => 'Aucune affectation trouvée','disabled' => empty($idLocalisation),'containerClass' => empty($idLocalisation) && !empty($idAffectation) ? 'ring-2 ring-yellow-300' : '']);

$key = 'affectation-select-transfer-' . ($idLocalisation ?? 'none');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3182883854-1', 'affectation-select-transfer-' . ($idLocalisation ?? 'none'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($idLocalisation) && !empty($idAffectation)): ?>
                            <p class="mt-1 text-xs text-yellow-600">
                                Sélectionnez d'abord une localisation
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idAffectation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Emplacement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Emplacement <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'idEmplacement','options' => $this->emplacementOptions,'placeholder' => 'Sélectionner un emplacement','searchPlaceholder' => 'Rechercher un emplacement...','noResultsText' => 'Aucun emplacement trouvé','disabled' => empty($idAffectation),'containerClass' => empty($idAffectation) && !empty($idEmplacement) ? 'ring-2 ring-yellow-300' : '']);

$key = 'emplacement-select-transfer-' . ($idLocalisation ?? 'none') . '-' . ($idAffectation ?? 'none');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3182883854-2', 'emplacement-select-transfer-' . ($idLocalisation ?? 'none') . '-' . ($idAffectation ?? 'none'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($idAffectation) && !empty($idEmplacement)): ?>
                            <p class="mt-1 text-xs text-yellow-600">
                                Sélectionnez d'abord une affectation
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idEmplacement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Raison du transfert (optionnel) -->
                    <div>
                        <label for="raison" class="block text-sm font-medium text-gray-700 mb-1">
                            Raison du transfert (optionnel)
                        </label>
                        <textarea 
                            id="raison"
                            wire:model="raison" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Ex: Réorganisation, déménagement, correction..."></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['raison'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Résumé -->
                    <?php if(!empty($bienIds) && !empty($idEmplacement)): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-green-900 mb-2">Résumé du transfert</h3>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• <strong><?php echo e(count($bienIds)); ?></strong> immobilisation(s) à transférer</li>
                                <li>• Vers: <strong><?php echo e($this->emplacementOptions[array_search($idEmplacement, array_column($this->emplacementOptions, 'value'))]['text'] ?? 'N/A'); ?></strong></li>
                            </ul>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <button type="button" 
                                wire:click="cancel"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center"
                                :disabled="empty($bienIds) || empty($idEmplacement)">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Effectuer le transfert (<?php echo e(count($bienIds)); ?>)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/biens/transfert-bien.blade.php ENDPATH**/ ?>