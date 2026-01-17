<?php

namespace App\Livewire\Localisations;

use App\Models\Localisation;
use App\Models\LocalisationImmo;
use App\Models\Emplacement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class ListeLocalisations extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et la recherche
     */
    public $search = '';
    public $filterBatiment = '';
    public $filterEtage = '';
    public $filterService = '';
    public $filterActif = 'all';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $selectedLocalisations = [];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        // Réinitialiser la pagination si nécessaire
        $this->resetPage();
    }

    /**
     * Propriété calculée : Retourne tous les emplacements associés aux localisations
     */
    public function getEmplacementsProperty()
    {
        return Emplacement::with('localisation', 'affectation')
            ->orderBy('Emplacement')
            ->get();
    }

    /**
     * Propriété calculée : Retourne la liste des bâtiments distincts
     */
    public function getBatimentsProperty()
    {
        return Localisation::whereNotNull('batiment')
            ->where('batiment', '!=', '')
            ->distinct('batiment')
            ->orderBy('batiment')
            ->pluck('batiment')
            ->toArray();
    }

    /**
     * Propriété calculée : Retourne la liste des étages distincts
     */
    public function getEtagesProperty()
    {
        return Localisation::whereNotNull('etage')
            ->distinct('etage')
            ->orderBy('etage')
            ->pluck('etage')
            ->toArray();
    }

    /**
     * Propriété calculée : Retourne la liste des services distincts
     */
    public function getServicesProperty()
    {
        return Localisation::whereNotNull('service_rattache')
            ->where('service_rattache', '!=', '')
            ->distinct('service_rattache')
            ->orderBy('service_rattache')
            ->pluck('service_rattache')
            ->toArray();
    }

    /**
     * Propriété calculée : Vérifie si toutes les localisations sont sélectionnées
     * Optimisé : ne recalcule que si nécessaire
     */
    public function getAllSelectedProperty()
    {
        // Si aucune sélection, retourner false immédiatement
        if (empty($this->selectedLocalisations)) {
            return false;
        }

        // Compter seulement les IDs correspondant aux filtres (sans les récupérer tous)
        $totalCount = $this->getLocalisationsQuery()->count();
        
        if ($totalCount === 0) {
            return false;
        }

        // Si le nombre de sélections ne correspond pas, retourner false
        if (count($this->selectedLocalisations) !== $totalCount) {
            return false;
        }

        // Vérifier que tous les IDs sélectionnés correspondent aux filtres
        $allLocalisationsIds = $this->getLocalisationsQuery()->pluck('id')->toArray();
        return empty(array_diff($allLocalisationsIds, $this->selectedLocalisations));
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
        $this->filterBatiment = '';
        $this->filterEtage = '';
        $this->filterService = '';
        $this->filterActif = 'all';
        $this->selectedLocalisations = [];
        $this->resetPage();
    }

    /**
     * Sélectionne ou désélectionne toutes les localisations (toutes pages)
     */
    public function toggleSelectAll(): void
    {
        // Récupérer tous les IDs des localisations correspondant aux filtres (sans pagination)
        $allLocalisationsIds = $this->getLocalisationsQuery()->pluck('id')->toArray();
        
        // Vérifier si toutes les localisations sont déjà sélectionnées
        $allSelected = !empty($allLocalisationsIds) && 
                       count($this->selectedLocalisations) === count($allLocalisationsIds) &&
                       empty(array_diff($allLocalisationsIds, $this->selectedLocalisations));

        if ($allSelected) {
            // Tout désélectionner
            $this->selectedLocalisations = [];
        } else {
            // Tout sélectionner (fusionner avec les localisations déjà sélectionnées)
            $this->selectedLocalisations = array_unique(array_merge($this->selectedLocalisations, $allLocalisationsIds));
        }
    }

    /**
     * Supprime une localisation
     */
    public function deleteLocalisation($localisationId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer une localisation.');
            return;
        }

        $localisation = Localisation::find($localisationId);

        if (!$localisation) {
            session()->flash('error', 'Localisation introuvable.');
            return;
        }

        // Vérifier qu'aucun bien n'est associé à cette localisation
        $nombreBiens = $localisation->biens()->count();
        
        if ($nombreBiens > 0) {
            session()->flash('error', "Impossible de supprimer cette localisation : {$nombreBiens} bien(s) y sont associé(s).");
            return;
        }

        try {
            $localisation->delete();
            // Invalider le cache des statistiques
            Cache::forget('localisations_total_count');
            Cache::forget('batiments_total_count');
            session()->flash('success', 'La localisation a été supprimée avec succès.');
            
            // Retirer de la sélection si présent
            $this->selectedLocalisations = array_diff($this->selectedLocalisations, [$localisationId]);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Toggle le statut actif/inactif d'une localisation
     */
    public function toggleActif($localisationId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour modifier cette localisation.');
            return;
        }

        $localisation = Localisation::find($localisationId);

        if (!$localisation) {
            session()->flash('error', 'Localisation introuvable.');
            return;
        }

        try {
            $localisation->actif = !$localisation->actif;
            $localisation->save();
            
            // Invalider le cache
            Cache::forget('localisations_total_count');
            session()->flash('success', 'Le statut de la localisation a été modifié avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les localisations sélectionnées
     */
    public function exportSelected()
    {
        if (empty($this->selectedLocalisations)) {
            session()->flash('warning', 'Veuillez sélectionner au moins une localisation à exporter.');
            return;
        }

        // Rediriger vers la route d'export avec les IDs sélectionnés en paramètre de requête (query string)
        $ids = implode(',', $this->selectedLocalisations);
        return redirect()->to(route('localisations.export-excel') . '?ids=' . urlencode($ids));
    }

    /**
     * Construit la requête de base pour les localisations
     */
    protected function getLocalisationsQuery()
    {
        $query = Localisation::withCount('biens');

        // Recherche globale
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('designation', 'like', '%' . $this->search . '%')
                    ->orWhere('responsable', 'like', '%' . $this->search . '%')
                    ->orWhere('batiment', 'like', '%' . $this->search . '%')
                    ->orWhere('service_rattache', 'like', '%' . $this->search . '%');
            });
        }

        // Filtre par bâtiment
        if (!empty($this->filterBatiment)) {
            $query->where('batiment', $this->filterBatiment);
        }

        // Filtre par étage
        if (!empty($this->filterEtage)) {
            $query->where('etage', $this->filterEtage);
        }

        // Filtre par service
        if (!empty($this->filterService)) {
            $query->where('service_rattache', $this->filterService);
        }

        // Filtre par statut actif/inactif
        if ($this->filterActif !== 'all') {
            $query->where('actif', $this->filterActif === 'actif');
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
        $localisations = $this->getLocalisationsQuery()->paginate($this->perPage);

        // Statistiques pour le header (avec cache)
        $totalLocalisations = Cache::remember('localisations_total_count', 300, function () {
            return Localisation::count();
        });
        $totalEmplacements = Cache::remember('emplacements_total_count', 300, function () {
            return Emplacement::count();
        });
        $totalBatiments = Cache::remember('batiments_total_count', 300, function () {
            // Compter les bâtiments distincts
            return Localisation::whereNotNull('batiment')
                ->where('batiment', '!=', '')
                ->distinct('batiment')
                ->count('batiment');
        });

        return view('livewire.localisations.liste-localisations', [
            'localisations' => $localisations,
            'totalLocalisations' => $totalLocalisations,
            'totalEmplacements' => $totalEmplacements,
            'totalBatiments' => $totalBatiments,
        ]);
    }
}

