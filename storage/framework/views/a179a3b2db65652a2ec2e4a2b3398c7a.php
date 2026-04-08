<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value',
    'sub'      => null,
    'href'     => null,
    'color'    => 'indigo', // indigo | green | amber | red | slate | blue | purple
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'label',
    'value',
    'sub'      => null,
    'href'     => null,
    'color'    => 'indigo', // indigo | green | amber | red | slate | blue | purple
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$colors = [
    'indigo' => ['icon_bg' => 'bg-indigo-100', 'icon_text' => 'text-indigo-600', 'link' => 'text-indigo-600 hover:text-indigo-800'],
    'blue'   => ['icon_bg' => 'bg-blue-100',   'icon_text' => 'text-blue-600',   'link' => 'text-blue-600 hover:text-blue-800'],
    'green'  => ['icon_bg' => 'bg-green-100',  'icon_text' => 'text-green-600',  'link' => 'text-green-600 hover:text-green-800'],
    'amber'  => ['icon_bg' => 'bg-amber-100',  'icon_text' => 'text-amber-600',  'link' => 'text-amber-600 hover:text-amber-800'],
    'red'    => ['icon_bg' => 'bg-red-100',    'icon_text' => 'text-red-600',    'link' => 'text-red-600 hover:text-red-800'],
    'purple' => ['icon_bg' => 'bg-purple-100', 'icon_text' => 'text-purple-600', 'link' => 'text-purple-600 hover:text-purple-800'],
    'slate'  => ['icon_bg' => 'bg-slate-100',  'icon_text' => 'text-slate-500',  'link' => 'text-slate-600 hover:text-slate-800'],
];
$c = $colors[$color] ?? $colors['indigo'];
?>

<div class="relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500"><?php echo e($label); ?></p>
            <p class="mt-2 text-3xl font-bold text-slate-900 tabular-nums"><?php echo e($value); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sub): ?>
                <p class="mt-1 text-xs text-slate-400"><?php echo e($sub); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($icon)): ?>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg <?php echo e($c['icon_bg']); ?> <?php echo e($c['icon_text']); ?>">
                <?php echo e($icon); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
        <a href="<?php echo e($href); ?>" wire:navigate class="mt-4 inline-flex items-center text-xs font-medium <?php echo e($c['link']); ?> transition-colors">
            Voir le détail
            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/components/stat-card.blade.php ENDPATH**/ ?>