<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    x-data="{
        sidebarOpen: (window.innerWidth >= 768) || (window.localStorage && window.localStorage.getItem('sidebarOpen') === 'true'),
        profileOpen: false,
        isDesktop: window.innerWidth >= 768,
        persistSidebarOpen(value) {
            try {
                // On persiste uniquement le choix utilisateur (pas le passage desktop automatique)
                window.localStorage.setItem('sidebarOpen', value ? 'true' : 'false');
            } catch (e) {
                // ignore storage errors
            }
        },
        init() {
            this.isDesktop = window.innerWidth >= 768;
            // On ne force pas `sidebarOpen` sur desktop (visibilité gérée par `x-show`).
        }
    }" :class="{ 'overflow-hidden': sidebarOpen }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Inventaire Pro')); ?> - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <?php echo $__env->make('layouts.app-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\components\app-layout.blade.php ENDPATH**/ ?>