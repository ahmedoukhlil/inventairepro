<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div
        class="space-y-4"
        x-data="{
            deleteModalOpen: false,
            deleteAction: '',
            deleteLabel: '',
        }"
    >
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Corbeille des immobilisations</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Restaurer, supprimer définitivement ou exporter les éléments supprimés
                </p>
            </div>
        </div>

        <?php
            $hasActiveFilters = !empty($filterDesignation) || !empty($filterEmplacement) || !empty($filterCategorie) || !empty($filterEtat) || !empty($filterNatJur) || !empty($filterSF) || !empty($filterDateAcquisition) || !empty($search);
        ?>

        <div x-data="{ open: <?php echo e($hasActiveFilters ? 'true' : 'false'); ?> }" class="bg-white rounded-lg shadow-sm border border-gray-200">
            <button
                @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <span class="font-medium text-gray-900">Filtres de recherche</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActiveFilters): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Actifs</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="border-t border-gray-200 p-4"
                style="overflow: visible !important;"
                @option-selected.window="$nextTick(() => $refs.filterForm.submit())"
                @option-cleared.window="$nextTick(() => $refs.filterForm.submit())"
            >
                <form x-ref="filterForm" method="GET" action="<?php echo e(route('corbeille.immobilisations.index')); ?>" class="space-y-4">

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="search">Recherche globale</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input
                                    id="search"
                                    name="search"
                                    type="text"
                                    value="<?php echo e($search); ?>"
                                    placeholder="NumOrdre, désignation, emplacement, catégorie..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    x-on:input.debounce.600ms="$refs.filterForm.submit()"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Désignation</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_designation','value' => (string) ($filterDesignation ?? ''),'options' => collect($designationOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'] . ' (' . $option['id'] . ')',
                                ])->prepend(['value' => '', 'text' => 'Toutes les désignations'])->toArray(),'placeholder' => 'Toutes les désignations','searchPlaceholder' => 'Rechercher une désignation...','noResultsText' => 'Aucune désignation trouvée','allowClear' => true]);

$key = 'corbeille-filter-designation-' . ($filterDesignation ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-0', 'corbeille-filter-designation-' . ($filterDesignation ?: 'all'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_categorie','value' => (string) ($filterCategorie ?? ''),'options' => collect($categorieOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les catégories'])->toArray(),'placeholder' => 'Toutes les catégories','searchPlaceholder' => 'Rechercher une catégorie...','noResultsText' => 'Aucune catégorie trouvée','allowClear' => true]);

$key = 'corbeille-filter-categorie-' . ($filterCategorie ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-1', 'corbeille-filter-categorie-' . ($filterCategorie ?: 'all'));

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

                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Filtrage par emplacement
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_emplacement','value' => (string) ($filterEmplacement ?? ''),'options' => collect($emplacementOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'] . ' (' . $option['id'] . ')',
                                ])->prepend(['value' => '', 'text' => 'Tous les emplacements'])->toArray(),'placeholder' => 'Tous les emplacements','searchPlaceholder' => 'Rechercher un emplacement...','noResultsText' => 'Aucun emplacement trouvé','allowClear' => true]);

$key = 'corbeille-filter-emplacement-' . ($filterEmplacement ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-2', 'corbeille-filter-emplacement-' . ($filterEmplacement ?: 'all'));

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

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_etat','value' => (string) ($filterEtat ?? ''),'options' => collect($etatOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Tous les états'])->toArray(),'placeholder' => 'Tous les états','searchPlaceholder' => 'Rechercher un état...','noResultsText' => 'Aucun état trouvé','allowClear' => true]);

$key = 'corbeille-filter-etat-' . ($filterEtat ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-3', 'corbeille-filter-etat-' . ($filterEtat ?: 'all'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nature Juridique</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_natjur','value' => (string) ($filterNatJur ?? ''),'options' => collect($natJurOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les natures juridiques'])->toArray(),'placeholder' => 'Toutes les natures juridiques','searchPlaceholder' => 'Rechercher une nature juridique...','noResultsText' => 'Aucune nature juridique trouvée','allowClear' => true]);

$key = 'corbeille-filter-natjur-' . ($filterNatJur ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-4', 'corbeille-filter-natjur-' . ($filterNatJur ?: 'all'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source de Financement</label>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.searchable-select', ['name' => 'filter_sf','value' => (string) ($filterSF ?? ''),'options' => collect($sourceFinOptions)->map(fn ($option) => [
                                    'value' => (string) $option['id'],
                                    'text' => $option['label'],
                                ])->prepend(['value' => '', 'text' => 'Toutes les sources'])->toArray(),'placeholder' => 'Toutes les sources','searchPlaceholder' => 'Rechercher une source de financement...','noResultsText' => 'Aucune source trouvée','allowClear' => true]);

$key = 'corbeille-filter-sf-' . ($filterSF ?: 'all');

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-34764030-5', 'corbeille-filter-sf-' . ($filterSF ?: 'all'));

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Année d'acquisition</label>
                            <input
                                id="filter_date_acquisition"
                                name="filter_date_acquisition"
                                type="number"
                                min="1900"
                                max="<?php echo e(now()->year + 1); ?>"
                                value="<?php echo e($filterDateAcquisition); ?>"
                                placeholder="Ex: <?php echo e(now()->year); ?>"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                x-on:change="$refs.filterForm.submit()"
                            >
                        </div>
                    </div>

                    
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Rechercher
                            </button>
                            <a href="<?php echo e(route('corbeille.immobilisations.index')); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Réinitialiser
                            </a>
                            <a href="<?php echo e(route('corbeille.immobilisations.export-excel', request()->query())); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </a>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium"><?php echo e($rows->total()); ?></span> élément(s) trouvé(s)
                        </div>
                    </div>
                </form>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($filterDesignation) || !empty($filterEmplacement)): ?>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-semibold text-gray-700">Restaurer en lot :</span>

                            <?php if(!empty($filterDesignation)): ?>
                                <form method="POST" action="<?php echo e(route('corbeille.immobilisations.restore-by-designation-selection')); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="designation_id" value="<?php echo e($filterDesignation); ?>">
                                    <?php
                                        $selectedDesignation = collect($designationOptions)->firstWhere('id', (int) $filterDesignation);
                                    ?>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors text-sm"
                                        onclick="return confirm('Restaurer toutes les immobilisations de la désignation : <?php echo e(addslashes($selectedDesignation['label'] ?? '')); ?> ?')"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer désignation : <?php echo e(Str::limit($selectedDesignation['label'] ?? '?', 30)); ?>

                                    </button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($filterEmplacement)): ?>
                                <form method="POST" action="<?php echo e(route('corbeille.immobilisations.restore-by-emplacement-selection')); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="emplacement_id" value="<?php echo e($filterEmplacement); ?>">
                                    <?php
                                        $selectedEmplacement = collect($emplacementOptions)->firstWhere('id', (int) $filterEmplacement);
                                    ?>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors text-sm"
                                        onclick="return confirm('Restaurer toutes les immobilisations de l\'emplacement : <?php echo e(addslashes($selectedEmplacement['label'] ?? '')); ?> ?')"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer emplacement : <?php echo e(Str::limit($selectedEmplacement['label'] ?? '?', 30)); ?>

                                    </button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="w-full overflow-x-auto overflow-y-hidden">
                <table class="min-w-[1300px] divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NumOrdre</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Désignation</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année d'acquisition</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date suppression</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($row->original_num_ordre); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-600"><?php echo e($row->code_display); ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900"><?php echo e($row->designation_display); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($row->categorie_display); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo e($row->etat_display); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo e($row->emplacement_display); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->affectation_display): ?>
                                            <br><span class="text-xs text-gray-500"><?php echo e($row->affectation_display); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->localisation_display): ?>
                                            <br><span class="text-xs text-gray-500"><?php echo e($row->localisation_display); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo e(($row->DateAcquisition && (int) $row->DateAcquisition > 1970) ? (int) $row->DateAcquisition : 'N/A'); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-600"><?php echo e(optional($row->deleted_at)->format('d/m/Y H:i')); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="<?php echo e(route('corbeille.immobilisations.restore', $row->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button
                                                type="submit"
                                                title="Restaurer"
                                                onclick="return confirm('Restaurer cette immobilisation ?')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Restaurer
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            title="Supprimer définitivement"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-colors"
                                            x-on:click="
                                                deleteAction = <?php echo \Illuminate\Support\Js::from(route('corbeille.immobilisations.force-delete', $row->id))->toHtml() ?>;
                                                deleteLabel = <?php echo \Illuminate\Support\Js::from(($row->designation_display ?? 'Immobilisation') . ' (#' . $row->original_num_ordre . ')')->toHtml() ?>;
                                                deleteModalOpen = true;
                                            "
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                    Corbeille vide.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <?php echo e($rows->links()); ?>

        </div>

        <div
            x-show="deleteModalOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-modal-title"
        >
            <div class="absolute inset-0 bg-black/50" x-on:click="deleteModalOpen = false"></div>

            <div class="relative w-full max-w-lg rounded-xl bg-white shadow-xl border border-gray-200">
                <div class="p-6">
                    <h2 id="delete-modal-title" class="text-lg font-semibold text-gray-900">
                        Confirmer la suppression définitive
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Cette action est irréversible. L'élément
                        <span class="font-medium text-gray-900" x-text="deleteLabel"></span>
                        sera supprimé définitivement.
                    </p>
                </div>

                <div class="px-6 pb-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200"
                        x-on:click="deleteModalOpen = false"
                    >
                        Annuler
                    </button>
                    <form method="POST" :action="deleteAction">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700"
                        >
                            Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/corbeille/immobilisations/index.blade.php ENDPATH**/ ?>