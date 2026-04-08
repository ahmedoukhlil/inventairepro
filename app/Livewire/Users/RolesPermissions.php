<?php

namespace App\Livewire\Users;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RolesPermissions extends Component
{
    public ?int $selectedRoleId = null;

    public string $newRoleKey = '';
    public string $newRoleLabel = '';

    /** @var array<int, int> */
    public array $selectedPermissionIds = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        $this->selectedRoleId = Role::query()->orderBy('id')->value('id');
        $this->loadSelectedRolePermissions();
    }

    private function loadSelectedRolePermissions(): void
    {
        if (!$this->selectedRoleId) {
            $this->selectedPermissionIds = [];
            return;
        }

        $role = Role::query()->find($this->selectedRoleId);
        if (!$role) {
            $this->selectedPermissionIds = [];
            return;
        }

        $this->selectedPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
    }

    public function updatedSelectedRoleId(): void
    {
        $this->loadSelectedRolePermissions();
    }

    public function createRole(): void
    {
        $validated = $this->validate([
            'newRoleKey' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:roles,key'],
            'newRoleLabel' => ['required', 'string', 'max:100'],
        ]);

        $role = Role::create([
            'key' => $validated['newRoleKey'],
            'label' => $validated['newRoleLabel'],
            'description' => null,
        ]);

        $this->selectedRoleId = $role->id;
        $this->selectedPermissionIds = [];
        $this->newRoleKey = '';
        $this->newRoleLabel = '';

        session()->flash('success', 'Rôle créé avec succès.');
    }

    public function savePermissions(): void
    {
        if (!$this->selectedRoleId) {
            session()->flash('error', 'Veuillez sélectionner un rôle.');
            return;
        }

        $role = Role::query()->find($this->selectedRoleId);
        if (!$role) {
            session()->flash('error', 'Rôle introuvable.');
            return;
        }

        $role->permissions()->sync($this->selectedPermissionIds);

        session()->flash('success', 'Permissions enregistrées.');
    }

    public function render()
    {
        return view('livewire.users.roles-permissions', [
            'roles' => Role::query()->orderBy('label')->get(),
            'permissions' => Permission::query()->orderBy('label')->get(),
        ]);
    }
}

