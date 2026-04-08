<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',  // primary | secondary | danger | ghost | success
    'size'    => 'md',       // sm | md | lg
    'href'    => null,
    'wire'    => null,       // wire:click value
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
    'variant' => 'primary',  // primary | secondary | danger | ghost | success
    'size'    => 'md',       // sm | md | lg
    'href'    => null,
    'wire'    => null,       // wire:click value
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$variants = [
    'primary'   => 'bg-indigo-600 text-white hover:bg-indigo-500 focus:ring-indigo-500 shadow-sm',
    'secondary' => 'bg-white text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:ring-indigo-500',
    'danger'    => 'bg-red-600 text-white hover:bg-red-500 focus:ring-red-500 shadow-sm',
    'success'   => 'bg-green-600 text-white hover:bg-green-500 focus:ring-green-500 shadow-sm',
    'ghost'     => 'text-slate-700 hover:bg-slate-100 focus:ring-slate-400',
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-3.5 py-2 text-sm gap-2',
    'lg' => 'px-5 py-2.5 text-sm gap-2',
];
$cls  = $variants[$variant] ?? $variants['primary'];
$szCl = $sizes[$size] ?? $sizes['md'];
$base = "inline-flex items-center justify-center rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed $cls $szCl";
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
    <a href="<?php echo e($href); ?>" wire:navigate <?php echo e($attributes->merge(['class' => $base])); ?>><?php echo e($slot); ?></a>
<?php else: ?>
    <button <?php if($wire): ?> wire:click="<?php echo e($wire); ?>" <?php endif; ?> <?php echo e($attributes->merge(['class' => $base])); ?>><?php echo e($slot); ?></button>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\components\btn.blade.php ENDPATH**/ ?>