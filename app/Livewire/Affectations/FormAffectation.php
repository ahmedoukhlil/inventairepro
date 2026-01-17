<?php

namespace App\Livewire\Affectations;

use App\Models\Affectation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]

class FormAffectation extends Component
{
    /**
     * Instance de l'affectation (null si création)
     */
    public $affectation = null;

    /**
     * ID de l'affectation pour l'édition
     */
    public $affectationId = null;

    /**
     * Propriétés du formulaire
     */
    public $Affectation = '';
    public $CodeAffectation = '';

    /**
     * Initialisation du composant
     * 
     * @param Affectation|int|string|null $affectation Instance de l'affectation pour l'édition, ID, ou null pour la création
     */
    public function mount($affectation = null): void
    {
        if ($affectation) {
            // Si c'est un ID (string ou int) et pas une instance de Affectation, charger l'affectation
            if (!($affectation instanceof Affectation)) {
                if (is_numeric($affectation) || (is_string($affectation) && ctype_digit($affectation))) {
                    try {
                        $affectation = Affectation::findOrFail($affectation);
                    } catch (\Exception $e) {
                        Log::warning('Affectation introuvable pour édition:', ['id' => $affectation]);
                        // Si l'affectation n'existe pas, traiter comme création
                        $affectation = null;
                    }
                } else {
                    // Si ce n'est ni un ID ni une instance, traiter comme création
                    $affectation = null;
                }
            }
            
            // Si on a une instance valide de Affectation
            if ($affectation instanceof Affectation) {
                // Mode édition : charger les valeurs de l'affectation
                $this->affectation = $affectation;
                $this->affectationId = $affectation->idAffectation;
                $this->Affectation = $affectation->Affectation;
                $this->CodeAffectation = $affectation->CodeAffectation ?? '';
            }
        }
    }

    /**
     * Génère une suggestion de code automatiquement
     */
    public function generateCodeSuggestion()
    {
        // Compter les affectations existantes pour générer un numéro unique
        $count = Affectation::count() + 1;
        
        // Générer un code basé sur le nom de l'affectation
        $prefix = 'AFF';
        if (!empty($this->Affectation)) {
            // Extraire les premières lettres du nom
            $words = explode(' ', $this->Affectation);
            $initials = '';
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
            }
            if (strlen($initials) > 0) {
                $prefix = substr($initials, 0, 3);
            }
        }
        
        $this->CodeAffectation = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Propriété calculée : Vérifie si on est en mode édition
     */
    public function getIsEditProperty(): bool
    {
        return $this->affectation !== null;
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        return [
            'Affectation' => 'required|string|max:255',
            'CodeAffectation' => 'nullable|string|max:255',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'Affectation.required' => 'L\'affectation est obligatoire.',
            'Affectation.max' => 'L\'affectation ne peut pas dépasser 255 caractères.',
            'CodeAffectation.max' => 'Le code d\'affectation ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Sauvegarde l'affectation (création ou édition)
     */
    public function save()
    {
        // Normaliser les valeurs vides avant validation
        $this->Affectation = trim($this->Affectation ?? '');
        $this->CodeAffectation = trim($this->CodeAffectation ?? '') ?: null;

        // Valider les données
        try {
            $validated = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Les erreurs de validation sont automatiquement affichées par Livewire
            throw $e;
        }

        try {
            // Préparer les données pour l'insertion
            $data = [
                'Affectation' => trim($validated['Affectation']),
                'CodeAffectation' => $validated['CodeAffectation'] ?? null,
            ];

            if ($this->isEdit && $this->affectation) {
                // Mode édition : mettre à jour l'affectation existante
                $this->affectation->update($data);
                $affectation = $this->affectation->fresh();
                $message = 'Affectation modifiée avec succès';
            } else {
                // Mode création : créer une nouvelle affectation
                try {
                    DB::beginTransaction();
                    $affectation = Affectation::create($data);
                    DB::commit();
                    
                    // Vérifier que la création a réussi
                    if (!$affectation || !$affectation->idAffectation) {
                        throw new \Exception('Échec de la création de l\'affectation - aucun ID retourné');
                    }
                    
                    $message = 'Affectation créée avec succès';
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    Log::error('Erreur SQL lors de la création d\'affectation', [
                        'message' => $e->getMessage(),
                        'Affectation' => $data['Affectation'] ?? 'N/A'
                    ]);
                    throw new \Exception('Erreur lors de l\'insertion en base de données : ' . $e->getMessage());
                }
            }

            // Invalider le cache des statistiques
            \Illuminate\Support\Facades\Cache::forget('emplacements_total_count');

            session()->flash('success', $message);

            // Rediriger vers la liste (à créer si nécessaire) ou le dashboard
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde affectation', [
                'message' => $e->getMessage(),
                'Affectation' => $this->Affectation ?? 'N/A'
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde : ' . $e->getMessage());
            return;
        }
    }

    /**
     * Annule et redirige vers le dashboard
     */
    public function cancel()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.affectations.form-affectation');
    }
}
