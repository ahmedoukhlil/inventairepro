<div>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Rôles & Permissions</h1>
    <p class="text-gray-500 mt-1">Créer des rôles, puis attribuer les permissions nécessaires.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne gauche : rôles -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rôles existants</h2>

            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        type="button"
                        wire:click="$set('selectedRoleId', <?php echo e($role->id); ?>)"
                        class="w-full text-left px-3 py-2 rounded-lg border transition-colors
                            <?php echo e($selectedRoleId === $role->id ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-gray-200 hover:bg-gray-50'); ?>"
                    >
                        <div class="font-medium text-gray-900"><?php echo e($role->label); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5"><?php echo e($role->key); ?></div>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Créer un rôle</h2>

            <form wire:submit.prevent="createRole" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key (stockée dans `users.role`)</label>
                    <input
                        type="text"
                        wire:model="newRoleKey"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="ex: agent_stock_2"
                    />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newRoleKey'];
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label (affichage)</label>
                    <input
                        type="text"
                        wire:model="newRoleLabel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="ex: Agent stock"
                    />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newRoleLabel'];
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

                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                >
                    Créer
                </button>
            </form>
        </div>
    </div>

    <!-- Colonne droite : permissions -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Permissions du rôle</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Sélection actuelle :
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRoleId): ?>
                            <?php echo e(\App\Models\Role::query()->find($selectedRoleId)?->label ?? 'N/A'); ?>

                        <?php else: ?>
                            Aucun rôle sélectionné
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="savePermissions"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors"
                >
                    Enregistrer
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <input
                            type="checkbox"
                            class="mt-1"
                            value="<?php echo e($permission->id); ?>"
                            wire:model="selectedPermissionIds"
                        />
                        <div>
                            <div class="font-medium text-gray-900"><?php echo e($permission->label); ?></div>
                            <div class="text-xs text-gray-500 mt-1"><?php echo e($permission->description); ?></div>
                        </div>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Note</h2>
            <p class="text-sm text-gray-600">
                Le champ <code>Key</code> doit correspondre exactement à <code>users.role</code> (valeur stockée dans la base).
                Les permissions modifiées s’appliquent immédiatement (sidebar, dashboard, et middlewares).
            </p>
        </div>
    </div>
</div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\users\roles-permissions.blade.php ENDPATH**/ ?>