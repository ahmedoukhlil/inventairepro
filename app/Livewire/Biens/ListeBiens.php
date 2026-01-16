<?php

namespace App\Livewire\Biens;

use App\Models\Gesimmo;
use App\Models\LocalisationImmo;
use App\Models\Emplacement;
use App\Models\Designation;
use App\Models\Categorie;
use App\Models\Etat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListeBiens extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et la recherche
     */
    public $search = '';
    public $filterDesignation = '';
    public $filterCategorie = '';
    public $filterEmplacement = '';
    public $filterEtat = '';
    public $sortField = 'NumOrdre';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $selectedBiens = [];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        // Réinitialiser la pagination si nécessaire
        $this->resetPage();
    }

    /**
     * Propriété calculée : Retourne toutes les désignations
     */
    public function getDesignationsProperty()
    {
        return Designation::orderBy('designation')->get();
    }

    /**
     * Propriété calculée : Retourne toutes les catégories
     */
    public function getCategoriesProperty()
    {
        return Categorie::orderBy('Categorie')->get();
    }

    /**
     * Propriété calculée : Retourne tous les emplacements groupés par localisation
     */
    public function getEmplacementsProperty()
    {
        return Emplacement::with('localisation')
            ->join('localisation', 'emplacement.idLocalisation', '=', 'localisation.idLocalisation')
            ->orderBy('localisation.Localisation')
            ->orderBy('emplacement.Emplacement')
            ->select('emplacement.*')
            ->get()
            ->groupBy(function($item) {
                return $item->localisation ? $item->localisation->Localisation : 'Sans localisation';
            });
    }

    /**
     * Propriété calculée : Retourne tous les états
     */
    public function getEtatsProperty()
    {
        return Etat::orderBy('Etat')->get();
    }

    /**
     * Propriété calculée : Vérifie si tous les biens sont sélectionnés
     */
    public function getAllSelectedProperty()
    {
        $allBiensIds = $this->getBiensQuery()->pluck('NumOrdre')->toArray();
        
        if (empty($allBiensIds)) {
            return false;
        }

        return count($this->selectedBiens) === count($allBiensIds) &&
               empty(array_diff($allBiensIds, $this->selectedBiens));
    }

    /**
     * Change le tri de la colonne
     */
    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            // Inverser la direction si on clique sur la même colonne
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nouvelle colonne, tri ascendant par défaut
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // Réinitialiser la pagination lors du changement de tri
        $this->resetPage();
    }

    /**
     * Réinitialise tous les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterDesignation = '';
        $this->filterCategorie = '';
        $this->filterEmplacement = '';
        $this->filterEtat = '';
        $this->selectedBiens = [];
        $this->resetPage();
    }

    /**
     * Sélectionne ou désélectionne tous les biens (toutes pages)
     */
    public function toggleSelectAll(): void
    {
        // Récupérer tous les IDs des biens correspondant aux filtres (sans pagination)
        $allBiensIds = $this->getBiensQuery()->pluck('NumOrdre')->toArray();
        
        // Vérifier si tous les biens sont déjà sélectionnés
        $allSelected = !empty($allBiensIds) && 
                       count($this->selectedBiens) === count($allBiensIds) &&
                       empty(array_diff($allBiensIds, $this->selectedBiens));

        if ($allSelected) {
            // Tout désélectionner
            $this->selectedBiens = [];
        } else {
            // Tout sélectionner (fusionner avec les biens déjà sélectionnés)
            $this->selectedBiens = array_unique(array_merge($this->selectedBiens, $allBiensIds));
        }
    }

    /**
     * Supprime un bien
     */
    public function deleteBien($bienId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer un bien.');
            return;
        }

        $bien = Gesimmo::find($bienId);

        if ($bien) {
            $bien->delete();
            session()->flash('success', 'L\'immobilisation a été supprimée avec succès.');
            
            // Retirer de la sélection si présent
            $this->selectedBiens = array_diff($this->selectedBiens, [$bienId]);
        } else {
            session()->flash('error', 'Immobilisation introuvable.');
        }
    }

    /**
     * Exporte les biens sélectionnés
     */
    public function exportSelected()
    {
        if (empty($this->selectedBiens)) {
            session()->flash('warning', 'Veuillez sélectionner au moins un bien à exporter.');
            return;
        }

        // Rediriger vers la route d'export avec les IDs sélectionnés en paramètre de requête
        $ids = implode(',', $this->selectedBiens);
        return redirect()->route('biens.export-excel', ['ids' => $ids]);
    }

    /**
     * Construit la requête de base pour les immobilisations
     */
    protected function getBiensQuery()
    {
        $query = Gesimmo::with([
            'designation',
            'categorie',
            'etat',
            'emplacement.localisation',
            'natureJuridique',
            'sourceFinancement'
        ]);

        // Recherche globale
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('NumOrdre', 'like', '%' . $this->search . '%')
                    ->orWhereHas('designation', function ($q2) {
                        $q2->where('designation', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('emplacement', function ($q2) {
                        $q2->where('Emplacement', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filtre par désignation
        if (!empty($this->filterDesignation)) {
            $query->where('idDesignation', $this->filterDesignation);
        }

        // Filtre par catégorie
        if (!empty($this->filterCategorie)) {
            $query->where('idCategorie', $this->filterCategorie);
        }

        // Filtre par emplacement
        if (!empty($this->filterEmplacement)) {
            $query->where('idEmplacement', $this->filterEmplacement);
        }

        // Filtre par état
        if (!empty($this->filterEtat)) {
            $query->where('idEtat', $this->filterEtat);
        }

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        $biens = $this->getBiensQuery()->paginate($this->perPage);

        return view('livewire.biens.liste-biens', [
            'biens' => $biens,
        ]);
    }
}

