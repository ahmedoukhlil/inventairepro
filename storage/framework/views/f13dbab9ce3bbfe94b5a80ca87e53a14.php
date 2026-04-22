<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    isDesktop: window.innerWidth >= 1024,
    init() {
        this.isDesktop = window.innerWidth >= 1024;
        try {
            const stored = localStorage.getItem('sidebar');
            if (stored !== null) this.sidebarOpen = stored === '1';
            else this.sidebarOpen = this.isDesktop;
        } catch(e) {}
        window.addEventListener('resize', () => {
            this.isDesktop = window.innerWidth >= 1024;
        });
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        try { localStorage.setItem('sidebar', this.sidebarOpen ? '1' : '0'); } catch(e) {}
    }
}" :class="{ 'overflow-hidden': sidebarOpen && !isDesktop }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo e(config('app.name')); ?>">
    <meta name="mobile-web-app-capable" content="yes">

    <title><?php echo e(config('app.name', 'Gesimmos')); ?> — <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo e(asset('images/icons/icon-192x192.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/icons/icon-192x192.png')); ?>">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900"
      @keydown.escape.window="if (!isDesktop) sidebarOpen = false">

<div class="flex h-screen overflow-hidden">

    
    <aside
        x-show="sidebarOpen || isDesktop"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed lg:static inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900"
        x-cloak
    >
        
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-700/60 bg-slate-950 px-5">
            <a href="<?php echo e(route('dashboard')); ?>" wire:navigate class="flex items-center gap-2.5">
                <img src="<?php echo e(asset('images/Image1.jpg')); ?>" alt="Logo" class="h-9 w-auto object-contain rounded">
            </a>
            <button @click="toggleSidebar()" class="lg:hidden rounded-md p-1 text-slate-400 hover:text-white focus:outline-none" aria-label="Fermer le menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal04122ae30a04d6531f2d4a1bff406b3c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal04122ae30a04d6531f2d4a1bff406b3c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.sidebar-menu','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.sidebar-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal04122ae30a04d6531f2d4a1bff406b3c)): ?>
<?php $attributes = $__attributesOriginal04122ae30a04d6531f2d4a1bff406b3c; ?>
<?php unset($__attributesOriginal04122ae30a04d6531f2d4a1bff406b3c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal04122ae30a04d6531f2d4a1bff406b3c)): ?>
<?php $component = $__componentOriginal04122ae30a04d6531f2d4a1bff406b3c; ?>
<?php unset($__componentOriginal04122ae30a04d6531f2d4a1bff406b3c); ?>
<?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <div class="shrink-0 border-t border-slate-700/60 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                    <?php echo e(strtoupper(substr(auth()->user()->display_name ?? 'U', 0, 1))); ?>

                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-200"><?php echo e(auth()->user()->display_name ?? 'Utilisateur'); ?></p>
                    <p class="truncate text-xs text-slate-500"><?php echo e(auth()->user()->poste ?? auth()->user()->role_name); ?></p>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" title="Déconnexion" class="rounded p-1 text-slate-500 hover:text-slate-200 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </aside>

    
    <div
        x-show="sidebarOpen && !isDesktop"
        @click="toggleSidebar()"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        x-cloak
    ></div>

    
    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

        
        <header class="flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm lg:px-6">
            
            <button @click="toggleSidebar()" class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors" aria-label="Menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <div class="flex-1"></div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <div class="flex items-center gap-2">
                
                <span class="hidden sm:inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    <?php echo e(auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                       (auth()->user()->role === 'admin_stock' ? 'bg-indigo-100 text-indigo-700' :
                       (auth()->user()->role === 'agent_stock' ? 'bg-cyan-100 text-cyan-700' :
                       'bg-slate-100 text-slate-600'))); ?>">
                    <?php echo e(auth()->user()->role_name); ?>

                </span>
                <span class="hidden sm:block text-sm font-medium text-slate-700"><?php echo e(auth()->user()->display_name); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </header>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['success' => ['border-l-green-500', 'bg-green-50', 'text-green-800', 'border-green-200'], 'error' => ['border-l-red-500', 'bg-red-50', 'text-red-800', 'border-red-200'], 'warning' => ['border-l-amber-500', 'bg-amber-50', 'text-amber-800', 'border-amber-200'], 'info' => ['border-l-blue-500', 'bg-blue-50', 'text-blue-800', 'border-blue-200']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $classes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session($type)): ?>
            <div class="border-l-4 <?php echo e($classes[0]); ?> <?php echo e($classes[1]); ?> <?php echo e($classes[2]); ?> border <?php echo e($classes[3]); ?> px-4 py-3 text-sm" role="<?php echo e($type === 'success' || $type === 'info' ? 'status' : 'alert'); ?>">
                <?php echo e(session($type)); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <main id="main-content" class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <?php echo e($slot); ?>

            </div>
        </main>
    </div>
</div>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


<script>
    const LOGIN_URL = '<?php echo e(route('login')); ?>';
    const SESSION_CHECK_URL = '<?php echo e(route('session.check')); ?>';

    function redirectToLogin() {
        window.location.href = LOGIN_URL;
    }

    // 1. Hook Livewire : intercepter 401/419 sur les requêtes AJAX
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 401 || status === 419) {
                    preventDefault();
                    redirectToLogin();
                }
            });
        });
    });

    // 2. Polling toutes les 60s : vérifier la session avant qu'elle n'expire
    //    et rediriger proprement avant que Livewire tente une requête échouée.
    setInterval(function () {
        fetch(SESSION_CHECK_URL, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) {
            if (response.status === 401 || response.status === 419) {
                redirectToLogin();
            }
        }).catch(function () {
            // Erreur réseau — ne pas rediriger (peut être temporaire)
        });
    }, 60000);

    // 3. Intercepter les rejets non gérés (erreurs fetch Livewire non catchées)
    window.addEventListener('unhandledrejection', function (event) {
        if (event.reason instanceof TypeError && event.reason.message === 'Failed to fetch') {
            redirectToLogin();
        }
    });
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?php echo e(asset('sw.js')); ?>?v=<?php echo e(filemtime(public_path('sw.js'))); ?>')
                .then(r => { setInterval(() => r.update(), 60000); })
                .catch(() => {});
        });
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>