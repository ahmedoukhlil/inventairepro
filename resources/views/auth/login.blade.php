<x-guest-layout>
    <x-slot name="logo">
        <div class="flex justify-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name', 'Inventaire Pro') }}</h1>
        </div>
    </x-slot>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">
                {{ __('Oups ! Quelque chose s\'est mal passé.') }}
            </div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email') }}
            </label>
            <input 
                id="email" 
                class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="votre@email.com">
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Mot de passe') }}
            </label>
            <input 
                id="password" 
                class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="••••••••">
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input 
                id="remember_me" 
                type="checkbox" 
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                name="remember">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">
                {{ __('Se souvenir de moi') }}
            </label>
        </div>

        {{-- Lien vers réinitialisation de mot de passe (désactivé pour l'instant) --}}
        {{-- <div class="flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif
        </div> --}}

        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ __('Se connecter') }}
            </button>
        </div>
    </form>
</x-guest-layout>

