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
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Collecte initiale
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Consultation des lignes de collecte et export Excel
            </p>
        </div>
    </div>

    <div class="space-y-4 mt-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form method="GET" action="<?php echo e(route('collecte-initiale.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="emplacement">Emplacement</label>
                    <input
                        id="emplacement"
                        name="emplacement"
                        type="text"
                        value="<?php echo e($filters['emplacement'] ?? ''); ?>"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Bureau DG"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="lot_uid">Lot UID</label>
                    <input
                        id="lot_uid"
                        name="lot_uid"
                        type="text"
                        value="<?php echo e($filters['lot_uid'] ?? ''); ?>"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="UUID lot"
                    >
                </div>

                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Afficher
                    </button>
                    <a href="<?php echo e(route('collecte-initiale.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Reinitialiser
                    </a>
                    <a
                        href="<?php echo e(route('collecte-initiale.export-excel', request()->query())); ?>"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                    >
                        Export Excel
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Lot UID</th>
                            <th class="px-3 py-2 text-left">Emplacement</th>
                            <th class="px-3 py-2 text-left">Designation</th>
                            <th class="px-3 py-2 text-left">Quantite</th>
                            <th class="px-3 py-2 text-left">Etat</th>
                            <th class="px-3 py-2 text-left">Observations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2"><?php echo e($row->id); ?></td>
                                <td class="px-3 py-2"><?php echo e(optional($row->created_at)->format('d/m/Y H:i')); ?></td>
                                <td class="px-3 py-2 font-mono text-xs"><?php echo e($row->lot_uid); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->emplacement_label); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->designation); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->quantite); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->etat ?? '-'); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->observations ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                    Aucune ligne de collecte trouvee.
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
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\collecte-initiale\index.blade.php ENDPATH**/ ?>