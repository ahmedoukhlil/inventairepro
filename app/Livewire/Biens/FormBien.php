<?php

namespace App\Livewire\Biens;

use App\Models\Gesimmo;
use App\Models\Designation;
use App\Models\Categorie;
use App\Models\Etat;
use App\Models\Emplacement;
use App\Models\NatureJuridique;
use App\Models\SourceFinancement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormBien extends Component
{
    /**
     * Instance de l'immobilisation (null si création)
     */
    public $bien = null;

    /**
     * ID de l'immobilisation pour l'édition
     */
    public $bienId = null;

    /**
     * Propriétés du formulaire
     */
    public $idDesignation = '';
    public $idCategorie = '';
    public $idEtat = '';
    public $idEmplacement = '';
    public $idNatJur = '';
    public $idSF = '';
    public $DateAcquisition = '';
    public $Observations = '';
    public $genererQRCode = false;

    /**
     * Initialisation du composant
     * 
     * @param Gesimmo|null $bien Instance de l'immobilisation pour l'édition, null pour la création
     */
    public function mount($bien = null): void
    {
        if ($bien) {
            // Mode édition : charger les valeurs
            $this->bien = $bien;
            $this->bienId = $bien->NumOrdre;
            $this->idDesignation = $bien->idDesignation;
            $this->idCategorie = $bien->idCategorie;
            $this->idEtat = $bien->idEtat;
            $this->idEmplacement = $bien->idEmplacement;
            $this->idNatJur = $bien->idNatJur;
            $this->idSF = $bien->idSF;
            // DateAcquisition est un entier (année), pas une date
            $this->DateAcquisition = $bien->DateAcquisition ?? '';
            $this->Observations = $bien->Observations ?? '';
        } else {
            // Mode création : valeurs par défaut (année actuelle)
            $this->DateAcquisition = now()->year;
        }
    }

    /**
     * Propriété calculée : Retourne toutes les désignations
     */
    public function getDesignationsProperty()
    {
        return Designation::with('categorie')
            ->orderBy('designation')
            ->get();
    }

    /**
     * Propriété calculée : Retourne toutes les catégories
     */
    public function getCategoriesProperty()
    {
        return Categorie::orderBy('Categorie')->get();
    }

    /**
     * Propriété calculée : Retourne tous les états
     */
    public function getEtatsProperty()
    {
        return Etat::orderBy('Etat')->get();
    }

    /**
     * Propriété calculée : Retourne tous les emplacements
     */
    public function getEmplacementsProperty()
    {
        return Emplacement::with('localisation', 'affectation')
            ->orderBy('Emplacement')
            ->get();
    }

    /**
     * Propriété calculée : Retourne toutes les natures juridiques
     */
    public function getNatureJuridiquesProperty()
    {
        return NatureJuridique::orderBy('NatJur')->get();
    }

    /**
     * Propriété calculée : Retourne toutes les sources de financement
     */
    public function getSourceFinancementsProperty()
    {
        return SourceFinancement::orderBy('SourceFin')->get();
    }

    /**
     * Propriété calculée : Vérifie si on est en mode édition
     */
    public function getIsEditProperty(): bool
    {
        return $this->bien !== null;
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        return [
            'idDesignation' => 'required|exists:designation,id',
            'idCategorie' => 'required|exists:categorie,idCategorie',
            'idEtat' => 'required|exists:etat,idEtat',
            'idEmplacement' => 'required|exists:emplacement,idEmplacement',
            'idNatJur' => 'required|exists:naturejurdique,idNatJur',
            'idSF' => 'required|exists:sourcefinancement,idSF',
            'DateAcquisition' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'Observations' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'idDesignation.required' => 'La désignation est obligatoire.',
            'idDesignation.exists' => 'La désignation sélectionnée n\'existe pas.',
            'idCategorie.required' => 'La catégorie est obligatoire.',
            'idCategorie.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'idEtat.required' => 'L\'état est obligatoire.',
            'idEtat.exists' => 'L\'état sélectionné n\'existe pas.',
            'idEmplacement.required' => 'L\'emplacement est obligatoire.',
            'idEmplacement.exists' => 'L\'emplacement sélectionné n\'existe pas.',
            'idNatJur.required' => 'La nature juridique est obligatoire.',
            'idNatJur.exists' => 'La nature juridique sélectionnée n\'existe pas.',
            'idSF.required' => 'La source de financement est obligatoire.',
            'idSF.exists' => 'La source de financement sélectionnée n\'existe pas.',
            'DateAcquisition.integer' => 'L\'année d\'acquisition doit être un nombre.',
            'DateAcquisition.min' => 'L\'année d\'acquisition doit être supérieure ou égale à 1900.',
            'DateAcquisition.max' => 'L\'année d\'acquisition ne peut pas être dans le futur.',
            'Observations.max' => 'Les observations ne peuvent pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Sauvegarde l'immobilisation (création ou édition)
     */
    public function save()
    {
        // Valider les données
        $validated = $this->validate();

        try {
            if ($this->isEdit) {
                // Mode édition : mettre à jour l'immobilisation existante
                $this->bien->update([
                    'idDesignation' => $validated['idDesignation'],
                    'idCategorie' => $validated['idCategorie'],
                    'idEtat' => $validated['idEtat'],
                    'idEmplacement' => $validated['idEmplacement'],
                    'idNatJur' => $validated['idNatJur'],
                    'idSF' => $validated['idSF'],
                    'DateAcquisition' => !empty($validated['DateAcquisition']) ? (int)$validated['DateAcquisition'] : null,
                    'Observations' => $validated['Observations'] ?? null,
                ]);

                $bien = $this->bien->fresh();
                $message = 'Immobilisation modifiée avec succès';
            } else {
                // Mode création : créer une nouvelle immobilisation
                $bien = Gesimmo::create([
                    'idDesignation' => $validated['idDesignation'],
                    'idCategorie' => $validated['idCategorie'],
                    'idEtat' => $validated['idEtat'],
                    'idEmplacement' => $validated['idEmplacement'],
                    'idNatJur' => $validated['idNatJur'],
                    'idSF' => $validated['idSF'],
                    'DateAcquisition' => !empty($validated['DateAcquisition']) ? (int)$validated['DateAcquisition'] : null,
                    'Observations' => $validated['Observations'] ?? null,
                ]);

                $message = 'Immobilisation créée avec succès';
            }

            session()->flash('success', $message);

            // Rediriger vers la page de détail
            return redirect()->route('biens.show', $bien);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Annule et redirige vers la liste
     */
    public function cancel()
    {
        return redirect()->route('biens.index');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.biens.form-bien');
    }
}

