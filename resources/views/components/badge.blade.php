@props([
    'color' => 'slate', // slate | indigo | blue | green | amber | red | purple | cyan
    'dot'   => false,
    'size'  => 'sm',    // xs | sm
])

@php
$palettes = [
    'slate'  => 'bg-slate-100 text-slate-700 ring-slate-200',
    'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'blue'   => 'bg-blue-50 text-blue-700 ring-blue-200',
    'green'  => 'bg-green-50 text-green-700 ring-green-200',
    'amber'  => 'bg-amber-50 text-amber-700 ring-amber-200',
    'red'    => 'bg-red-50 text-red-700 ring-red-200',
    'purple' => 'bg-purple-50 text-purple-700 ring-purple-200',
    'cyan'   => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
];
$dots = [
    'slate'  => 'bg-slate-400',
    'indigo' => 'bg-indigo-500',
    'blue'   => 'bg-blue-500',
    'green'  => 'bg-green-500',
    'amber'  => 'bg-amber-500',
    'red'    => 'bg-red-500',
    'purple' => 'bg-purple-500',
    'cyan'   => 'bg-cyan-500',
];
$px   = $size === 'xs' ? 'px-1.5 py-0.5 text-[0.65rem]' : 'px-2 py-0.5 text-xs';
$cls  = $palettes[$color] ?? $palettes['slate'];
$dotC = $dots[$color] ?? 'bg-slate-400';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full font-medium ring-1 ring-inset $px $cls"]) }}>
    @if($dot)
        <span class="inline-block h-1.5 w-1.5 rounded-full {{ $dotC }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
