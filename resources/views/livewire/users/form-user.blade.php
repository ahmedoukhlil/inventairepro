<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $isEdit ? 'Modifier l\'utilisateur' : 'Créer un utilisateur' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $isEdit ? 'Modifiez les informations de l\'utilisateur' : 'Ajoutez un nouvel utilisateur au système' }}
                </p>
            </div>
            <a 
                href="{{ route('users.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
        </div>

        {{-- Messages d'erreur --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Des erreurs ont été détectées
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire --}}
        <form wire:submit.prevent="save" class="space-y-6">
            <div 
                wire:loading.class="opacity-50 pointer-events-none"
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                {{-- Section 1 : Informations personnelles --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Informations personnelles
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nom --}}
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text"
                                id="name"
                                wire:model="name"
                                placeholder="Ex: Jean Dupont"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email"
                                id="email"
                                wire:model="email"
                                placeholder="Ex: jean.dupont@example.com"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                                Téléphone
                            </label>
                            <input 
                                type="text"
                                id="telephone"
                                wire:model="telephone"
                                placeholder="Ex: +222 45 67 89 01"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('telephone') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Service --}}
                        <div>
                            <label for="service" class="block text-sm font-medium text-gray-700 mb-1">
                                Service
                            </label>
                            <input 
                                type="text"
                                id="service"
                                wire:model="service"
                                placeholder="Ex: Direction Générale"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('service') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('service')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2 : Authentification --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Authentification
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Mot de passe --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe @if(!$isEdit)<span class="text-red-500">*</span>@else<span class="text-gray-400">(laisser vide pour ne pas modifier)</span>@endif
                            </label>
                            <input 
                                type="password"
                                id="password"
                                wire:model="password"
                                placeholder="Minimum 8 caractères"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmation mot de passe --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmer le mot de passe @if(!$isEdit)<span class="text-red-500">*</span>@endif
                            </label>
                            <input 
                                type="password"
                                id="password_confirmation"
                                wire:model="password_confirmation"
                                placeholder="Répétez le mot de passe"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password_confirmation') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 3 : Rôle et statut --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Rôle et statut
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Rôle --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                Rôle <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="role"
                                wire:model="role"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('role') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                                <option value="agent">Agent</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <strong>Agent :</strong> Peut gérer les localisations, biens et inventaires.<br>
                                <strong>Administrateur :</strong> Accès complet, y compris la gestion des utilisateurs.
                            </p>
                        </div>

                        {{-- Statut actif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Statut
                            </label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox"
                                        wire:model="actif"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        wire:loading.attr="disabled">
                                    <span class="ml-2 text-sm text-gray-700">Utilisateur actif</span>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Les utilisateurs inactifs ne peuvent pas se connecter à l'application.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button 
                        type="button"
                        wire:click="cancel"
                        class="px-6 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}
                        </span>
                        <span wire:loading wire:target="save">
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

