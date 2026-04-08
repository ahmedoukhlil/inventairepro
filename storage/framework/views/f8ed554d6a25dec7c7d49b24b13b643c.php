<div class="max-w-5xl mx-auto">
    
    <div class="mb-8">
        <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-indigo-600 transition-colors">Tableau de bord</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li><a href="<?php echo e(route('inventaires.index')); ?>" class="hover:text-indigo-600 transition-colors">Inventaires</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li class="text-gray-400">Nouveau</li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Nouvel inventaire</h1>
        <p class="mt-1 text-sm text-gray-500">Configurez et lancez un inventaire en 3 étapes simples.</p>
    </div>

    <div x-data="{ etape: <?php if ((object) ('etapeActuelle') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('etapeActuelle'->value()); ?>')<?php echo e('etapeActuelle'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('etapeActuelle'); ?>')<?php endif; ?> }">
        
        
        
        <div class="mb-8">
            <div class="flex items-center">
                
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors
                        <?php echo e($etapeActuelle >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($etapeActuelle > 1): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="text-sm font-medium <?php echo e($etapeActuelle >= 1 ? 'text-gray-900' : 'text-gray-400'); ?>">Informations</span>
                </div>

                <div class="flex-1 h-px mx-4 <?php echo e($etapeActuelle >= 2 ? 'bg-indigo-600' : 'bg-gray-200'); ?> transition-colors"></div>

                
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors
                        <?php echo e($etapeActuelle >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($etapeActuelle > 2): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="text-sm font-medium <?php echo e($etapeActuelle >= 2 ? 'text-gray-900' : 'text-gray-400'); ?>">Périmètre</span>
                </div>

                <div class="flex-1 h-px mx-4 <?php echo e($etapeActuelle >= 3 ? 'bg-indigo-600' : 'bg-gray-200'); ?> transition-colors"></div>

                
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors
                        <?php echo e($etapeActuelle >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium <?php echo e($etapeActuelle >= 3 ? 'text-gray-900' : 'text-gray-400'); ?>">Équipe</span>
                </div>
            </div>
        </div>

        
        
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2">

                
                <div x-show="etape === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900">Informations générales</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Définissez l'année et la date de début de l'inventaire.</p>
                        </div>
                        <div class="p-6 space-y-5">
                            
                            <div>
                                <label for="annee" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Année <span class="text-red-500">*</span>
                                </label>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'annee','options' => $this->anneeOptions,'placeholder' => 'Sélectionner une année','searchPlaceholder' => 'Rechercher...','noResultsText' => 'Aucune année disponible','allowClear' => true,'name' => 'annee']);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3724946335-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['annee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="mt-1.5 text-xs text-gray-400">Chaque année ne peut avoir qu'un seul inventaire.</p>
                            </div>

                            
                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Date de début <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="date_debut"
                                    wire:model.defer="date_debut"
                                    min="<?php echo e(now()->format('Y-m-d')); ?>"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors <?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-500/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div>
                                <label for="observation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Observation <span class="text-xs font-normal text-gray-400">(optionnel)</span>
                                </label>
                                <textarea
                                    id="observation"
                                    wire:model.defer="observation"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Notes ou contexte sur cet inventaire..."
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors <?php $__errorArgs = ['observation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['observation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div x-show="etape === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Périmètre de l'inventaire</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <span class="font-medium text-indigo-600"><?php echo e($this->totalLocalisations); ?></span> localisation(s)
                                        &middot;
                                        <span class="font-medium text-gray-700"><?php echo e($this->totalBiensAttendus); ?></span> immobilisation(s)
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        wire:click="selectToutesLocalisations"
                                        class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        Tout cocher
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="deselectToutesLocalisations"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        Tout décocher
                                    </button>
                                </div>
                            </div>

                            
                            <div class="mt-4 relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="rechercheLocalisation"
                                    placeholder="Filtrer les localisations..."
                                    class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['localisationsSelectionnees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->localisationsFiltrees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $estSelectionnee = in_array($localisation->idLocalisation, $localisationsSelectionnees);
                                    ?>
                                    <div
                                        wire:click="toggleLocalisation(<?php echo e($localisation->idLocalisation); ?>)"
                                        wire:key="loc-<?php echo e($localisation->idLocalisation); ?>"
                                        class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all
                                            <?php echo e($estSelectionnee
                                                ? 'border-indigo-500 bg-indigo-50/60 shadow-sm'
                                                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'); ?>">

                                        
                                        <div class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                            <?php echo e($estSelectionnee ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300 bg-white'); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($estSelectionnee): ?>
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo e($localisation->Localisation); ?>

                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($localisation->CodeLocalisation): ?>
                                                    <span class="text-xs text-gray-400"><?php echo e($localisation->CodeLocalisation); ?></span>
                                                    <span class="text-gray-300">&middot;</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <span class="text-xs text-gray-500"><?php echo e($localisation->biens_count ?? 0); ?> bien(s)</span>
                                            </div>
                                        </div>

                                        
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                                                <?php echo e($estSelectionnee ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500'); ?>">
                                                <?php echo e($localisation->biens_count ?? 0); ?>

                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="sm:col-span-2 text-center py-8">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($rechercheLocalisation)): ?>
                                            <p class="text-sm text-gray-400">Aucune localisation ne correspond à « <?php echo e($rechercheLocalisation); ?> »</p>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-400">Aucune localisation disponible</p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div x-show="etape === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Assignation des agents</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Attribuez un agent par localisation. Vous pourrez modifier les assignations plus tard.</p>

                            
                            <div class="mt-4 flex flex-col sm:flex-row gap-3 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-indigo-700 mb-1.5">Assignation rapide</label>
                                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['wire:model' => 'agentGlobalSelect','options' => $this->agentOptions,'placeholder' => 'Choisir un agent...','searchPlaceholder' => 'Rechercher...','noResultsText' => 'Aucun agent','allowClear' => true]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3724946335-1', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                                </div>
                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        wire:click="assignerAgentGlobal(<?php echo e($agentGlobalSelect ?: 0); ?>)"
                                        <?php if(!$agentGlobalSelect): ?> disabled <?php endif; ?>
                                        class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                        Appliquer à toutes
                                    </button>
                                </div>
                            </div>
                        </div>

                        
                        <div class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->localisations->whereIn('idLocalisation', $localisationsSelectionnees); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $currentAgentId = isset($assignations[$localisation->idLocalisation]) ? (string)$assignations[$localisation->idLocalisation] : '';
                                    $agentName = '';
                                    if ($currentAgentId) {
                                        $agent = collect($this->agentOptions)->firstWhere('value', $currentAgentId);
                                        $agentName = $agent['text'] ?? '';
                                    }
                                ?>
                                <div wire:key="assign-<?php echo e($localisation->idLocalisation); ?>" class="flex flex-col sm:flex-row sm:items-center gap-3 px-6 py-4">
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?php echo e($localisation->Localisation); ?>

                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <?php echo e($localisation->biens_count ?? 0); ?> immobilisation(s)
                                        </p>
                                    </div>

                                    
                                    <div class="sm:w-64 flex-shrink-0">
                                        <?php
                                            $agentOptionsWithEmpty = array_merge(
                                                [['value' => '', 'text' => 'Non assigné']],
                                                array_filter($this->agentOptions, fn($opt) => $opt['value'] !== '')
                                            );
                                        ?>
                                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['value' => $currentAgentId,'options' => $agentOptionsWithEmpty,'placeholder' => 'Non assigné','searchPlaceholder' => 'Rechercher...','noResultsText' => 'Aucun agent','allowClear' => true,'xOn:optionSelected.window' => '$wire.assignerAgent('.e($localisation->idLocalisation).', $event.detail.value)','xOn:optionCleared.window' => '$wire.assignerAgent('.e($localisation->idLocalisation).', \'\')']);

$key = 'agent-select-'.e($localisation->idLocalisation).'';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3724946335-2', 'agent-select-'.e($localisation->idLocalisation).'');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="p-6 bg-gray-50 border-t border-gray-100 rounded-b-xl">
                            <div class="flex items-center justify-around text-center">
                                <div>
                                    <p class="text-2xl font-bold text-indigo-600"><?php echo e(count(array_filter($assignations))); ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5">Assignée(s)</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200"></div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-400"><?php echo e($this->totalLocalisations - count(array_filter($assignations))); ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5">Non assignée(s)</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200"></div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo e($this->agentsImpliques); ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5">Agent(s)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            
            
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-4">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 bg-gradient-to-br from-indigo-600 to-indigo-700 text-white">
                            <p class="text-xs font-medium text-indigo-200 uppercase tracking-wider">Inventaire</p>
                            <p class="text-3xl font-bold mt-1"><?php echo e($annee ?: '—'); ?></p>
                            <p class="text-sm text-indigo-200 mt-1">
                                <?php echo e($date_debut ? \Carbon\Carbon::parse($date_debut)->translatedFormat('d F Y') : 'Date non définie'); ?>

                            </p>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Localisations</span>
                                <span class="text-sm font-bold text-gray-900"><?php echo e($this->totalLocalisations); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Immobilisations</span>
                                <span class="text-sm font-bold text-gray-900"><?php echo e($this->totalBiensAttendus); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Agents assignés</span>
                                <span class="text-sm font-bold text-gray-900"><?php echo e($this->agentsImpliques); ?></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($observation): ?>
                                <div class="pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 uppercase font-medium mb-1">Observation</p>
                                    <p class="text-sm text-gray-600"><?php echo e(Str::limit($observation, 100)); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Mode préparation</p>
                                <p class="text-xs text-amber-600 mt-0.5">L'inventaire sera créé en mode préparation. Vous pourrez le lancer depuis le tableau de bord.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        
        
        <div class="mt-8 flex items-center justify-between">
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($etapeActuelle > 1): ?>
                    <button
                        type="button"
                        wire:click="etapePrecedente"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Précédent
                    </button>
                <?php else: ?>
                    <a
                        href="<?php echo e(route('inventaires.index')); ?>"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($etapeActuelle < 3): ?>
                    <button
                        type="button"
                        wire:click="etapeSuivante"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                        Suivant
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                <?php else: ?>
                    <button
                        type="button"
                        wire:click="demarrer"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="demarrer" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Créer l'inventaire
                        </span>
                        <span wire:loading wire:target="demarrer" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Création...
                        </span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    
    
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\inventaires\demarrer-inventaire.blade.php ENDPATH**/ ?>