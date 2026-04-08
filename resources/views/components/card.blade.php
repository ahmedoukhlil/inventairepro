@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm' . ($padding ? ' p-5 sm:p-6' : '')]) }}>
    @if(isset($header))
        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
            {{ $header }}
        </div>
    @endif

    @if($padding)
        {{ $slot }}
    @else
        {{ $slot }}
    @endif

    @if(isset($footer))
        <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endif
</div>
