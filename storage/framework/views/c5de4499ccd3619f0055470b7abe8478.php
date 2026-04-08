<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold text-gray-800">
                        <?php echo e(config('app.name', 'Inventaire Pro')); ?>

                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out" href="<?php echo e(route('dashboard')); ?>">
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="ml-3 relative">
                    <span class="text-gray-700 text-sm"><?php echo e(Auth::user()->name); ?></span>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ml-4 text-sm text-gray-500 hover:text-gray-700">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\layouts\navigation.blade.php ENDPATH**/ ?>