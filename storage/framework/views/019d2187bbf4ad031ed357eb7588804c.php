<div>
    
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="<?php echo e(route('biens.index')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Immobilisations</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                            <?php echo e($this->isEdit ? 'Modifier' : 'Ajouter'); ?>

                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900">
            <?php echo e($this->isEdit ? 'Modifier un bien' : 'Ajouter un bien'); ?>

        </h1>
        <p class="mt-1 text-sm text-gray-500">
            <?php echo e($this->isEdit ? 'Modifiez les informations du bien' : 'Remplissez le formulaire pour ajouter un nouveau bien à l\'inventaire'); ?>

        </p>
    </div>

    
    <form wire:submit.prevent="save" class="space-y-6">
        <div 
            wire:loading.class="opacity-50 pointer-events-none"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                    Informations générales
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label for="idDesignation" class="block text-sm font-medium text-gray-700 mb-1">
                            Désignation <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live' => 'idDesignation','options' => $this->designationOptions,'placeholder' => 'Sélectionner une désignation','searchPlaceholder' => 'Rechercher une désignation...','noResultsText' => 'Aucune désignation trouvée','allowClear' => true,'name' => 'idDesignation']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idDesignation'];
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

                    
                    <div>
                        <label for="idCategorie" class="block text-sm font-medium text-gray-700 mb-1">
                            Catégorie <span class="text-red-500">*</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idDesignation): ?>
                                <span class="text-xs text-gray-500 font-normal ml-2">(automatiquement remplie)</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'idCategorie','options' => $this->categorieOptions,'placeholder' => 'Sélectionner une catégorie','searchPlaceholder' => 'Rechercher une catégorie...','noResultsText' => 'Aucune catégorie trouvée','allowClear' => !$idDesignation,'disabled' => !!$idDesignation,'name' => 'idCategorie']);

$key = 'categorie-'.e($idDesignation).'';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-1', 'categorie-'.e($idDesignation).'');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idCategorie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idDesignation): ?>
                            <p class="mt-1 text-xs text-gray-500">
                                La catégorie est automatiquement définie selon la désignation sélectionnée.
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label for="idEtat" class="block text-sm font-medium text-gray-700 mb-1">
                            État <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'idEtat','options' => $this->etatOptions,'placeholder' => 'Sélectionner un état','searchPlaceholder' => 'Rechercher un état...','noResultsText' => 'Aucun état trouvé','allowClear' => true,'name' => 'idEtat']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-2', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idEtat'];
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

                    
                    <div>
                        <label for="idLocalisation" class="block text-sm font-medium text-gray-700 mb-1">
                            Localisation <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live.debounce.150ms' => 'idLocalisation','options' => $this->localisationOptions,'placeholder' => 'Sélectionner une localisation','searchPlaceholder' => 'Rechercher une localisation...','noResultsText' => 'Aucune localisation trouvée','allowClear' => true,'name' => 'idLocalisation']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-3', null);

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

                    
                    <div>
                        <label for="idAffectation" class="block text-sm font-medium text-gray-700 mb-1">
                            Affectation <span class="text-red-500">*</span>
                        </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($idLocalisation)): ?>
                            <div class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 text-sm italic">
                                Sélectionnez d'abord une localisation
                            </div>
                        <?php else: ?>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model.live.debounce.150ms' => 'idAffectation','options' => $this->affectationOptions,'placeholder' => 'Sélectionner une affectation','searchPlaceholder' => 'Rechercher une affectation...','noResultsText' => 'Aucune affectation pour cette localisation','allowClear' => true,'name' => 'idAffectation']);

$key = 'affectation-'.e($idLocalisation).'';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-4', 'affectation-'.e($idLocalisation).'');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
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

                    
                    <div>
                        <label for="idEmplacement" class="block text-sm font-medium text-gray-700 mb-1">
                            Emplacement <span class="text-red-500">*</span>
                        </label>
                        <?php if(empty($idLocalisation) || empty($idAffectation)): ?>
                            <div class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 text-sm italic">
                                Sélectionnez d'abord une localisation et une affectation
                            </div>
                        <?php else: ?>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'idEmplacement','options' => $this->emplacementOptions,'placeholder' => 'Sélectionner un emplacement','searchPlaceholder' => 'Rechercher un emplacement...','noResultsText' => 'Aucun emplacement pour cette affectation','allowClear' => true,'name' => 'idEmplacement']);

$key = 'emplacement-'.e($idLocalisation).'-'.e($idAffectation).'';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-5', 'emplacement-'.e($idLocalisation).'-'.e($idAffectation).'');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
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

                    
                    <div>
                        <label for="idNatJur" class="block text-sm font-medium text-gray-700 mb-1">
                            Nature Juridique <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'idNatJur','options' => $this->natureJuridiqueOptions,'placeholder' => 'Sélectionner une nature juridique','searchPlaceholder' => 'Rechercher une nature juridique...','noResultsText' => 'Aucune nature juridique trouvée','allowClear' => true,'name' => 'idNatJur']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-6', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idNatJur'];
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

                    
                    <div>
                        <label for="idSF" class="block text-sm font-medium text-gray-700 mb-1">
                            Source de Financement <span class="text-red-500">*</span>
                        </label>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'idSF','options' => $this->sourceFinancementOptions,'placeholder' => 'Sélectionner une source de financement','searchPlaceholder' => 'Rechercher une source de financement...','noResultsText' => 'Aucune source de financement trouvée','allowClear' => true,'name' => 'idSF']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3443952371-7', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['idSF'];
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

                    
                    <div>
                        <label for="DateAcquisition" class="block text-sm font-medium text-gray-700 mb-1">
                            Année d'acquisition
                        </label>
                        <input 
                            type="number"
                            id="DateAcquisition"
                            wire:model.defer="DateAcquisition"
                            min="1900"
                            max="<?php echo e(now()->year + 1); ?>"
                            placeholder="Ex: <?php echo e(now()->year); ?>"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['DateAcquisition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            wire:loading.attr="disabled">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['DateAcquisition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="mt-1 text-xs text-gray-500">Saisissez uniquement l'année (ex: 2024)</p>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$this->isEdit): ?>
                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number"
                            id="quantite"
                            wire:model.defer="quantite"
                            min="1"
                            max="1000"
                            placeholder="1"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            wire:loading.attr="disabled">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="mt-1 text-xs text-gray-500">
                            Nombre d'immobilisations identiques à créer. Chaque immobilisation aura un NumOrdre unique.
                        </p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>

        
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg shadow-lg -mx-6 -mb-6">
            <div class="flex justify-end gap-3">
                <button 
                    type="button"
                    wire:click="cancel"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Annuler
                </button>
                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span wire:loading.remove wire:target="save">
                        <?php echo e($this->isEdit ? 'Modifier' : 'Enregistrer'); ?>

                    </span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </form>

    

    
    <?php $__env->startPush('styles'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Select2 sur tous les selects avec la classe select2-search
            function initSelect2() {
                $('.select2-search').select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: function() {
                        return $(this).find('option[value=""]').text() || 'Rechercher...';
                    },
                    language: {
                        noResults: function() {
                            return "Aucun résultat trouvé";
                        },
                        searching: function() {
                            return "Recherche en cours...";
                        }
                    },
                    minimumResultsForSearch: 0 // Toujours afficher la recherche
                });

                // Synchroniser Select2 avec Livewire
                $('.select2-search').on('change', function(e) {
                    var select = $(this);
                    var wireModel = select.attr('wire:model') || select.attr('wire:model.defer') || select.attr('wire:model.live');
                    if (wireModel) {
                        var propertyName = wireModel.replace('wire:model.defer=', '').replace('wire:model=', '').replace('wire:model.live=', '');
                        var value = select.val();
                        // Utiliser window.Livewire.find('<?php echo e($_instance->getId()); ?>') pour mettre à jour la propriété Livewire
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set(propertyName, value);
                    }
                });
            }

            // Initialiser au chargement
            initSelect2();

            // Réinitialiser après les mises à jour Livewire
            document.addEventListener('livewire:update', function() {
                setTimeout(function() {
                    $('.select2-search').select2('destroy');
                    initSelect2();
                    
                    // Mettre à jour l'état disabled du champ catégorie après mise à jour Livewire
                    var categorieSelect = $('#idCategorie');
                    if (categorieSelect.length) {
                        var isDisabled = categorieSelect.prop('disabled') || categorieSelect.attr('disabled') !== undefined;
                        if (isDisabled) {
                            categorieSelect.addClass('bg-gray-50');
                        } else {
                            categorieSelect.removeClass('bg-gray-50');
                        }
                    }
                }, 100);
            });

            // Réinitialiser après les erreurs de validation
            document.addEventListener('livewire:error', function() {
                setTimeout(function() {
                    $('.select2-search').select2('destroy');
                    initSelect2();
                }, 100);
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\biens\form-bien.blade.php ENDPATH**/ ?>