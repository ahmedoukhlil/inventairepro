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
    public $quantite = 1;

    /**
     * Mise à jour automatique de la catégorie lorsque la désignation change
     */
    public function updatedIdDesignation($value)
    {
        if (!empty($value)) {
            $designation = Designation::with('categorie')->find($value);
            if ($designation && $designation->categorie) {
                $this->idCategorie = $designation->categorie->idCategorie;
            } else {
                $this->idCategorie = '';
            }
        } else {
            $this->idCategorie = '';
        }
    }

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
        } else {
            // Mode création : valeurs par défaut (année actuelle)
            $this->DateAcquisition = now()->year;
            $this->quantite = 1;
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
     * Propriété calculée : Retourne tous les emplacements avec leurs relations
     * Groupés par localisation pour faciliter la sélection
     */
    public function getEmplacementsProperty()
    {
        return Emplacement::with(['localisation', 'affectation'])
            ->orderBy('Emplacement')
            ->get()
            ->map(function ($emplacement) {
                // Ajouter un attribut calculé pour l'affichage
                $emplacement->display_name = $this->getEmplacementDisplayName($emplacement);
                return $emplacement;
            });
    }
    
    /**
     * Génère le nom d'affichage d'un emplacement avec ses relations
     */
    private function getEmplacementDisplayName($emplacement): string
    {
        $parts = [];
        
        // Localisation
        if ($emplacement->localisation) {
            $parts[] = $emplacement->localisation->Localisation ?? '';
            if ($emplacement->localisation->CodeLocalisation) {
                $parts[] = '(' . $emplacement->localisation->CodeLocalisation . ')';
            }
        }
        
        // Affectation
        if ($emplacement->affectation) {
            $parts[] = '- ' . ($emplacement->affectation->Affectation ?? '');
        }
        
        // Emplacement
        $parts[] = '- ' . ($emplacement->Emplacement ?? '');
        
        return implode(' ', array_filter($parts));
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
        $rules = [
            'idDesignation' => 'required|exists:designation,id',
            'idCategorie' => 'required|exists:categorie,idCategorie',
            'idEtat' => 'required|exists:etat,idEtat',
            'idEmplacement' => 'required|exists:emplacement,idEmplacement',
            'idNatJur' => 'required|exists:naturejurdique,idNatJur',
            'idSF' => 'required|exists:sourcefinancement,idSF',
            'DateAcquisition' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
        ];

        // Ajouter la validation de quantité uniquement en mode création
        if (!$this->isEdit) {
            $rules['quantite'] = 'required|integer|min:1|max:1000';
        }

        return $rules;
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
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être au moins de 1.',
            'quantite.max' => 'La quantité ne peut pas dépasser 1000.',
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
                ]);

                $bien = $this->bien->fresh();
                $message = 'Immobilisation modifiée avec succès';
            } else {
                // Mode création : créer une ou plusieurs immobilisations selon la quantité
                $quantite = (int)($validated['quantite'] ?? 1);
                $biensCrees = [];
                
                // Données communes pour toutes les immobilisations
                $donneesCommunes = [
                    'idDesignation' => $validated['idDesignation'],
                    'idCategorie' => $validated['idCategorie'],
                    'idEtat' => $validated['idEtat'],
                    'idEmplacement' => $validated['idEmplacement'],
                    'idNatJur' => $validated['idNatJur'],
                    'idSF' => $validated['idSF'],
                    'DateAcquisition' => !empty($validated['DateAcquisition']) ? (int)$validated['DateAcquisition'] : null,
                ];
                
                // Créer les immobilisations
                for ($i = 0; $i < $quantite; $i++) {
                    $bien = Gesimmo::create($donneesCommunes);
                    
                    // Charger les relations nécessaires pour le code formaté et l'affichage
                    $bien->load([
                        'designation',
                        'categorie',
                        'natureJuridique',
                        'sourceFinancement',
                        'emplacement.localisation',
                        'emplacement.affectation'
                    ]);
                    
                    $biensCrees[] = $bien;
                }
                
                // Utiliser le dernier bien créé pour la redirection
                $bien = end($biensCrees);
                
                if ($quantite > 1) {
                    $message = $quantite . ' immobilisations créées avec succès';
                } else {
                    $message = 'Immobilisation créée avec succès';
                }
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

