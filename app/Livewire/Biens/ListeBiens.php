<?php

namespace App\Livewire\Biens;

use App\Models\Bien;
use App\Models\Localisation;
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
    public $filterNature = '';
    public $filterLocalisation = '';
    public $filterService = '';
    public $filterEtat = '';
    public $sortField = 'code_inventaire';
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
     * Propriété calculée : Retourne toutes les localisations actives
     */
    public function getLocalisationsProperty()
    {
        return Localisation::actives()
            ->orderBy('code')
            ->get();
    }

    /**
     * Propriété calculée : Retourne la liste unique des services
     */
    public function getServicesProperty()
    {
        return Bien::query()
            ->distinct()
            ->whereNotNull('service_usager')
            ->where('service_usager', '!=', '')
            ->orderBy('service_usager')
            ->pluck('service_usager')
            ->unique()
            ->values();
    }

    /**
     * Propriété calculée : Retourne les valeurs enum de nature
     */
    public function getNaturesProperty()
    {
        return [
            'mobilier' => 'Mobilier',
            'informatique' => 'Informatique',
            'vehicule' => 'Véhicule',
            'materiel' => 'Matériel',
        ];
    }

    /**
     * Propriété calculée : Retourne les valeurs enum d'état
     */
    public function getEtatsProperty()
    {
        return [
            'neuf' => 'Neuf',
            'bon' => 'Bon',
            'moyen' => 'Moyen',
            'mauvais' => 'Mauvais',
            'reforme' => 'Réformé',
        ];
    }

    /**
     * Propriété calculée : Vérifie si tous les biens sont sélectionnés
     */
    public function getAllSelectedProperty()
    {
        $allBiensIds = $this->getBiensQuery()->pluck('id')->toArray();
        
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
        $this->filterNature = '';
        $this->filterLocalisation = '';
        $this->filterService = '';
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
        $allBiensIds = $this->getBiensQuery()->pluck('id')->toArray();
        
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
     * Supprime un bien (soft delete)
     */
    public function deleteBien($bienId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer un bien.');
            return;
        }

        $bien = Bien::find($bienId);

        if ($bien) {
            $bien->delete();
            session()->flash('success', 'Le bien a été supprimé avec succès.');
            
            // Retirer de la sélection si présent
            $this->selectedBiens = array_diff($this->selectedBiens, [$bienId]);
        } else {
            session()->flash('error', 'Bien introuvable.');
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
     * Construit la requête de base pour les biens
     */
    protected function getBiensQuery()
    {
        $query = Bien::with(['localisation', 'user']);

        // Recherche globale
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('code_inventaire', 'like', '%' . $this->search . '%')
                    ->orWhere('designation', 'like', '%' . $this->search . '%')
                    ->orWhere('service_usager', 'like', '%' . $this->search . '%');
            });
        }

        // Filtre par nature
        if (!empty($this->filterNature)) {
            $query->where('nature', $this->filterNature);
        }

        // Filtre par localisation
        if (!empty($this->filterLocalisation)) {
            $query->where('localisation_id', $this->filterLocalisation);
        }

        // Filtre par service
        if (!empty($this->filterService)) {
            $query->where('service_usager', $this->filterService);
        }

        // Filtre par état
        if (!empty($this->filterEtat)) {
            $query->where('etat', $this->filterEtat);
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

