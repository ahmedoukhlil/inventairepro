<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Gesimmos')); ?> — Connexion</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="font-sans antialiased">

<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex flex-col items-center justify-center px-4 py-12">

    
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-indigo-800/20 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
        
        <div class="mb-8 text-center">
            <?php echo e($logo); ?>

        </div>

        
        <div class="rounded-2xl border border-white/10 bg-white/[0.06] backdrop-blur-xl shadow-2xl px-8 py-8">
            <?php echo e($slot); ?>

        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            © <?php echo e(now()->year); ?> <?php echo e(config('app.name')); ?>

        </p>
    </div>
</div>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/components/guest-layout.blade.php ENDPATH**/ ?>