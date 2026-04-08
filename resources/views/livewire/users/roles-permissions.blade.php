<div>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Rôles & Permissions</h1>
    <p class="text-gray-500 mt-1">Créer des rôles, puis attribuer les permissions nécessaires.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne gauche : rôles -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rôles existants</h2>

            <div class="space-y-2">
                @foreach($roles as $role)
                    <button
                        type="button"
                        wire:click="$set('selectedRoleId', {{ $role->id }})"
                        class="w-full text-left px-3 py-2 rounded-lg border transition-colors
                            {{ $selectedRoleId === $role->id ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-gray-200 hover:bg-gray-50' }}"
                    >
                        <div class="font-medium text-gray-900">{{ $role->label }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $role->key }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Créer un rôle</h2>

            <form wire:submit.prevent="createRole" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key (stockée dans `users.role`)</label>
                    <input
                        type="text"
                        wire:model="newRoleKey"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="ex: agent_stock_2"
                    />
                    @error('newRoleKey')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label (affichage)</label>
                    <input
                        type="text"
                        wire:model="newRoleLabel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="ex: Agent stock"
                    />
                    @error('newRoleLabel')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                >
                    Créer
                </button>
            </form>
        </div>
    </div>

    <!-- Colonne droite : permissions -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Permissions du rôle</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Sélection actuelle :
                        @if($selectedRoleId)
                            {{ \App\Models\Role::query()->find($selectedRoleId)?->label ?? 'N/A' }}
                        @else
                            Aucun rôle sélectionné
                        @endif
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="savePermissions"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors"
                >
                    Enregistrer
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($permissions as $permission)
                    <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <input
                            type="checkbox"
                            class="mt-1"
                            value="{{ $permission->id }}"
                            wire:model="selectedPermissionIds"
                        />
                        <div>
                            <div class="font-medium text-gray-900">{{ $permission->label }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $permission->description }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Note</h2>
            <p class="text-sm text-gray-600">
                Le champ <code>Key</code> doit correspondre exactement à <code>users.role</code> (valeur stockée dans la base).
                Les permissions modifiées s’appliquent immédiatement (sidebar, dashboard, et middlewares).
            </p>
        </div>
    </div>
</div>
</div>
