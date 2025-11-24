<?php

namespace App\Livewire\Inventaires;

use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Models\Localisation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DemarrerInventaire extends Component
{
    /**
     * Propriétés publiques du formulaire
     */
    public $annee;
    public $date_debut;
    public $observation = '';
    public $assignerLocalisations = true;
    public $localisationsSelectionnees = [];
    public $assignations = []; // [localisation_id => user_id]

    /**
     * Étape actuelle du wizard (1, 2, ou 3)
     */
    public $etapeActuelle = 1;

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        // Vérifier qu'aucun inventaire n'est déjà en cours ou en préparation
        $inventaireExistant = Inventaire::whereIn('statut', ['en_cours', 'en_preparation'])->first();

        if ($inventaireExistant) {
            session()->flash('error', "Un inventaire est déjà en cours ou en préparation pour l'année {$inventaireExistant->annee}.");
            $this->redirect(route('inventaires.show', $inventaireExistant), navigate: true);
            return;
        }

        // Initialiser les valeurs par défaut
        $this->annee = date('Y');
        $this->date_debut = now()->format('Y-m-d');

        // Pré-sélectionner toutes les localisations actives
        $this->selectToutesLocalisations();
    }

    /**
     * Propriété calculée : Retourne les années disponibles (non utilisées)
     */
    public function getAnneesDisponiblesProperty()
    {
        $anneesUtilisees = Inventaire::pluck('annee')->toArray();
        $anneeActuelle = (int) date('Y');
        $anneesDisponibles = [];

        // Générer les 5 prochaines années à partir de l'année actuelle
        for ($i = 0; $i < 5; $i++) {
            $annee = $anneeActuelle + $i;
            if (!in_array($annee, $anneesUtilisees)) {
                $anneesDisponibles[] = $annee;
            }
        }

        return $anneesDisponibles;
    }

    /**
     * Propriété calculée : Retourne toutes les localisations actives
     */
    public function getLocalisationsProperty()
    {
        return Localisation::where('actif', true)
            ->withCount('biens')
            ->orderBy('code')
            ->get();
    }

    /**
     * Propriété calculée : Retourne tous les agents (users avec role 'agent' ou 'admin')
     */
    public function getAgentsProperty()
    {
        return User::whereIn('role', ['agent', 'admin'])
            ->where('actif', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Propriété calculée : Retourne le nombre total de localisations sélectionnées
     */
    public function getTotalLocalisationsProperty(): int
    {
        return count($this->localisationsSelectionnees);
    }

    /**
     * Propriété calculée : Retourne le nombre total de biens attendus dans les localisations sélectionnées
     */
    public function getTotalBiensAttendusProperty(): int
    {
        if (empty($this->localisationsSelectionnees)) {
            return 0;
        }

        return Localisation::whereIn('id', $this->localisationsSelectionnees)
            ->withCount('biens')
            ->get()
            ->sum('biens_count');
    }

    /**
     * Propriété calculée : Retourne la valeur totale des biens dans les localisations sélectionnées
     */
    public function getValeurTotaleProperty(): float
    {
        if (empty($this->localisationsSelectionnees)) {
            return 0;
        }

        return \App\Models\Bien::whereIn('localisation_id', $this->localisationsSelectionnees)
            ->sum('valeur_acquisition');
    }

    /**
     * Propriété calculée : Retourne le nombre d'agents impliqués
     */
    public function getAgentsImpliquesProperty(): int
    {
        return count(array_filter($this->assignations));
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        return [
            'annee' => 'required|integer|unique:inventaires,annee',
            'date_debut' => 'required|date|after_or_equal:today',
            'observation' => 'nullable|string|max:1000',
            'localisationsSelectionnees' => 'required|array|min:1',
            'localisationsSelectionnees.*' => 'exists:localisations,id',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'annee.required' => 'L\'année est obligatoire.',
            'annee.unique' => 'Un inventaire existe déjà pour cette année.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' => 'La date de début ne peut pas être dans le passé.',
            'localisationsSelectionnees.required' => 'Vous devez sélectionner au moins une localisation.',
            'localisationsSelectionnees.min' => 'Vous devez sélectionner au moins une localisation.',
            'observation.max' => 'L\'observation ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Vérifie si l'année est unique (pour validation en temps réel)
     */
    public function updatedAnnee($value)
    {
        if (empty($value)) {
            return;
        }

        $exists = Inventaire::where('annee', $value)->exists();

        if ($exists) {
            $this->addError('annee', 'Un inventaire existe déjà pour cette année.');
        } else {
            $this->resetErrorBag('annee');
        }
    }

    /**
     * Toggle la sélection d'une localisation
     */
    public function toggleLocalisation($localisationId): void
    {
        $localisationId = (int) $localisationId;

        if (in_array($localisationId, $this->localisationsSelectionnees)) {
            // Retirer de la sélection
            $this->localisationsSelectionnees = array_values(
                array_filter($this->localisationsSelectionnees, fn($id) => $id !== $localisationId)
            );
            // Retirer aussi l'assignation si elle existe
            unset($this->assignations[$localisationId]);
        } else {
            // Ajouter à la sélection
            $this->localisationsSelectionnees[] = $localisationId;
        }
    }

    /**
     * Sélectionne toutes les localisations actives
     */
    public function selectToutesLocalisations(): void
    {
        $this->localisationsSelectionnees = $this->localisations->pluck('id')->toArray();
    }

    /**
     * Désélectionne toutes les localisations
     */
    public function deselectToutesLocalisations(): void
    {
        $this->localisationsSelectionnees = [];
        $this->assignations = [];
    }

    /**
     * Assigne un agent à une localisation
     */
    public function assignerAgent($localisationId, $userId): void
    {
        $localisationId = (int) $localisationId;
        $userId = $userId ? (int) $userId : null;

        if (in_array($localisationId, $this->localisationsSelectionnees)) {
            if ($userId) {
                $this->assignations[$localisationId] = $userId;
            } else {
                unset($this->assignations[$localisationId]);
            }
        }
    }

    /**
     * Assigne un agent à toutes les localisations non assignées
     */
    public function assignerAgentGlobal($userId): void
    {
        if (!$userId) {
            return;
        }

        foreach ($this->localisationsSelectionnees as $localisationId) {
            if (!isset($this->assignations[$localisationId])) {
                $this->assignations[$localisationId] = (int) $userId;
            }
        }
    }

    /**
     * Passe à l'étape suivante
     */
    public function etapeSuivante(): void
    {
        // Valider l'étape actuelle avant de passer à la suivante
        if ($this->etapeActuelle === 1) {
            $this->validate([
                'annee' => 'required|integer|unique:inventaires,annee',
                'date_debut' => 'required|date|after_or_equal:today',
            ]);
        } elseif ($this->etapeActuelle === 2) {
            $this->validate([
                'localisationsSelectionnees' => 'required|array|min:1',
            ]);
        }

        if ($this->etapeActuelle < 3) {
            $this->etapeActuelle++;
        }
    }

    /**
     * Retourne à l'étape précédente
     */
    public function etapePrecedente(): void
    {
        if ($this->etapeActuelle > 1) {
            $this->etapeActuelle--;
        }
    }

    /**
     * Démarre l'inventaire (création)
     */
    public function demarrer()
    {
        // Valider toutes les données
        $validated = $this->validate();

        try {
            // Créer l'inventaire
            $inventaire = Inventaire::create([
                'annee' => $validated['annee'],
                'date_debut' => $validated['date_debut'],
                'statut' => 'en_preparation',
                'created_by' => Auth::id(),
                'observation' => $validated['observation'] ?? null,
            ]);

            // Créer les InventaireLocalisation pour chaque localisation sélectionnée
            foreach ($this->localisationsSelectionnees as $localisationId) {
                $localisation = Localisation::find($localisationId);
                
                if ($localisation) {
                    $nombreBiensAttendus = $localisation->biens()->count();
                    $userId = $this->assignations[$localisationId] ?? null;

                    InventaireLocalisation::create([
                        'inventaire_id' => $inventaire->id,
                        'localisation_id' => $localisationId,
                        'statut' => 'en_attente',
                        'user_id' => $userId,
                        'nombre_biens_attendus' => $nombreBiensAttendus,
                        'nombre_biens_scannes' => 0,
                    ]);
                }
            }

            session()->flash('success', "L'inventaire {$inventaire->annee} a été créé avec succès. {$this->totalLocalisations} localisation(s) ont été ajoutée(s).");

            // Rediriger vers la page de détail de l'inventaire
            return redirect()->route('inventaires.show', $inventaire);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création de l\'inventaire: ' . $e->getMessage());
        }
    }

    /**
     * Annule et redirige vers la liste
     */
    public function cancel()
    {
        return redirect()->route('inventaires.index');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.inventaires.demarrer-inventaire');
    }
}

