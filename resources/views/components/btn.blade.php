@props([
    'variant' => 'primary',  // primary | secondary | danger | ghost | success
    'size'    => 'md',       // sm | md | lg
    'href'    => null,
    'wire'    => null,       // wire:click value
])

@php
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
@endphp

@if($href)
    <a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</a>
@else
    <button @if($wire) wire:click="{{ $wire }}" @endif {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</button>
@endif
