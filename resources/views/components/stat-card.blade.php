@props([
    'label',
    'value',
    'sub'      => null,
    'href'     => null,
    'color'    => 'indigo', // indigo | green | amber | red | slate | blue | purple
])

@php
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
@endphp

<div class="relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-900 tabular-nums">{{ $value }}</p>
            @if($sub)
                <p class="mt-1 text-xs text-slate-400">{{ $sub }}</p>
            @endif
        </div>
        @if(isset($icon))
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $c['icon_bg'] }} {{ $c['icon_text'] }}">
                {{ $icon }}
            </div>
        @endif
    </div>

    @if($href)
        <a href="{{ $href }}" wire:navigate class="mt-4 inline-flex items-center text-xs font-medium {{ $c['link'] }} transition-colors">
            Voir le détail
            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif
</div>
