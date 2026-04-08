<x-layouts.app>
    <x-page-header
        title="Tableau de bord"
        description="{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}">
        <x-slot name="actions">
            @if(auth()->user()->isAdmin())
                <x-btn href="{{ route('inventaires.create') }}" variant="primary" size="md">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Démarrer un inventaire
                </x-btn>
            @endif
        </x-slot>
    </x-page-header>

    @livewire('dashboard')
</x-layouts.app>
