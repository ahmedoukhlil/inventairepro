<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FormUser extends Component
{
    /**
     * Propriétés publiques
     */
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'agent';
    public $telephone = '';
    public $service = '';
    public $actif = true;

    /**
     * Mode édition ou création
     */
    public $isEdit = false;

    /**
     * Initialisation du composant
     */
    public function mount($user = null): void
    {
        if ($user) {
            $this->isEdit = true;
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->telephone = $user->telephone ?? '';
            $this->service = $user->service ?? '';
            $this->actif = $user->actif;
        }
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                $this->isEdit 
                    ? 'unique:users,email,' . $this->userId
                    : 'unique:users,email',
            ],
            'role' => 'required|in:admin,agent',
            'telephone' => 'nullable|string|max:20',
            'service' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ];

        // Règles pour le mot de passe
        if ($this->isEdit) {
            // En édition, le mot de passe est optionnel
            if (!empty($this->password)) {
                $rules['password'] = ['required', 'string', 'min:8', 'max:255', 'confirmed'];
            }
        } else {
            // En création, le mot de passe est obligatoire
            $rules['password'] = ['required', 'string', 'min:8', 'max:255', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.max' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné est invalide.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'service.max' => 'Le service ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Sauvegarde l'utilisateur (création ou édition)
     */
    public function save(): void
    {
        $this->validate();

        // Vérifier si on peut désactiver le dernier admin actif
        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            
            if ($user->role === 'admin' && $user->actif && !$this->actif) {
                $activeAdminsCount = User::where('role', 'admin')
                    ->where('actif', true)
                    ->where('id', '!=', $this->userId)
                    ->count();
                
                if ($activeAdminsCount === 0) {
                    $this->addError('actif', 'Impossible de désactiver le dernier administrateur actif.');
                    return;
                }
            }

            // Vérifier si on change le rôle d'admin vers agent
            if ($user->role === 'admin' && $this->role === 'agent' && $user->actif) {
                $activeAdminsCount = User::where('role', 'admin')
                    ->where('actif', true)
                    ->where('id', '!=', $this->userId)
                    ->count();
                
                if ($activeAdminsCount === 0) {
                    $this->addError('role', 'Impossible de changer le rôle du dernier administrateur actif.');
                    return;
                }
            }
        }

        // Préparer les données
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'telephone' => $this->telephone ?: null,
            'service' => $this->service ?: null,
            'actif' => $this->actif,
        ];

        // Ajouter le mot de passe seulement s'il est fourni
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // Créer ou mettre à jour
        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('success', "L'utilisateur {$user->name} a été modifié avec succès.");
        } else {
            $user = User::create($data);
            session()->flash('success', "L'utilisateur {$user->name} a été créé avec succès.");
        }

        // Rediriger vers la liste
        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Annuler et retourner à la liste
     */
    public function cancel(): void
    {
        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.users.form-user');
    }
}

