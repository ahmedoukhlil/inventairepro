<?php

namespace App\Livewire\Inventaires;

use App\Models\Inventaire;
use App\Models\InventaireScan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListeInventaires extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et le tri
     */
    public $filterStatut = 'all'; // all, en_preparation, en_cours, termine, cloture
    public $filterAnnee = '';
    public $sortField = 'annee';
    public $sortDirection = 'desc';
    public $perPage = 10;

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        // Réinitialiser la pagination si nécessaire
        $this->resetPage();
    }

    /**
     * Propriété calculée : Retourne la liste des années disponibles
     */
    public function getAnneesProperty()
    {
        return Inventaire::query()
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee')
            ->values();
    }

    /**
     * Propriété calculée : Retourne l'inventaire actuellement en cours
     */
    public function getInventaireEnCoursProperty()
    {
        return Inventaire::where('statut', 'en_cours')
            ->orWhere('statut', 'en_preparation')
            ->with(['creator', 'inventaireLocalisations', 'inventaireScans'])
            ->first();
    }

    /**
     * Propriété calculée : Retourne les statistiques globales sur tous les inventaires
     */
    public function getStatistiquesGlobalesProperty(): array
    {
        $totalInventaires = Inventaire::count();
        $inventaireEnCours = $this->inventaireEnCours;
        
        // Calculer le taux moyen de conformité
        $inventairesTermines = Inventaire::whereIn('statut', ['termine', 'cloture'])->get();
        $tauxMoyen = 0;
        
        if ($inventairesTermines->count() > 0) {
            $sommeTaux = $inventairesTermines->sum(function ($inv) {
                return $inv->taux_conformite;
            });
            $tauxMoyen = round($sommeTaux / $inventairesTermines->count(), 1);
        }

        // Dernier inventaire clôturé
        $dernierCloture = Inventaire::where('statut', 'cloture')
            ->orderBy('date_fin', 'desc')
            ->first();

        return [
            'total_inventaires' => $totalInventaires,
            'inventaire_en_cours' => $inventaireEnCours,
            'taux_moyen_conformite' => $tauxMoyen,
            'dernier_cloture' => $dernierCloture,
        ];
    }

    /**
     * Propriété calculée : Statistiques des résultats d'inventaire (inventaires terminés et clôturés)
     */
    public function getStatistiquesResultatsProperty(): array
    {
        $inventaireIds = Inventaire::whereIn('statut', ['termine', 'cloture'])->pluck('id');

        $totalScans = InventaireScan::whereIn('inventaire_id', $inventaireIds)->count();
        $biensPresents = InventaireScan::whereIn('inventaire_id', $inventaireIds)->where('statut_scan', 'present')->count();
        $biensDeplaces = InventaireScan::whereIn('inventaire_id', $inventaireIds)->where('statut_scan', 'deplace')->count();
        $biensAbsents = InventaireScan::whereIn('inventaire_id', $inventaireIds)->where('statut_scan', 'absent')->count();
        $biensDefectueux = InventaireScan::whereIn('inventaire_id', $inventaireIds)->where('etat_constate', 'mauvais')->count();
        $biensDeteriores = InventaireScan::whereIn('inventaire_id', $inventaireIds)->where('statut_scan', 'deteriore')->count();

        $tauxConformite = $totalScans > 0 ? round(($biensPresents / $totalScans) * 100, 1) : 0;

        return [
            'total_scans' => $totalScans,
            'biens_presents' => $biensPresents,
            'biens_deplaces' => $biensDeplaces,
            'biens_absents' => $biensAbsents,
            'biens_defectueux' => $biensDefectueux,
            'biens_deteriores' => $biensDeteriores,
            'taux_conformite' => $tauxConformite,
            'nombre_inventaires_termines' => $inventaireIds->count(),
        ];
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
        $this->filterStatut = 'all';
        $this->filterAnnee = '';
        $this->resetPage();
    }

    /**
     * Archive un inventaire (change le statut à 'cloture' si 'termine')
     */
    public function archiverInventaire($inventaireId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour clôturer un inventaire.');
            return;
        }

        $inventaire = Inventaire::find($inventaireId);

        if (!$inventaire) {
            session()->flash('error', 'Inventaire introuvable.');
            return;
        }

        if ($inventaire->statut !== 'termine') {
            session()->flash('error', 'Seuls les inventaires terminés peuvent être clôturés.');
            return;
        }

        try {
            $inventaire->cloturer();
            session()->flash('success', "L'inventaire {$inventaire->annee} a été clôturé avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la clôture: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un inventaire
     */
    public function supprimerInventaire($inventaireId): void
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer un inventaire.');
            return;
        }

        $inventaire = Inventaire::find($inventaireId);

        if (!$inventaire) {
            session()->flash('error', 'Inventaire introuvable.');
            return;
        }

        // Ne pas supprimer un inventaire en cours (travail en cours)
        if ($inventaire->statut === 'en_cours') {
            session()->flash('error', 'Impossible de supprimer un inventaire en cours. Terminez-le ou clôturez-le d\'abord.');
            return;
        }

        try {
            // Cascade delete : les scans et localisations seront supprimés automatiquement
            $inventaire->delete();
            session()->flash('success', "L'inventaire {$inventaire->annee} a été supprimé avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Construit la requête de base pour les inventaires
     */
    protected function getInventairesQuery()
    {
        $query = Inventaire::with(['creator', 'closer'])
            ->withCount(['inventaireLocalisations', 'inventaireScans']);

        // Filtre par statut
        if ($this->filterStatut !== 'all') {
            $query->where('statut', $this->filterStatut);
        }

        // Filtre par année
        if (!empty($this->filterAnnee)) {
            $query->where('annee', $this->filterAnnee);
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
        $inventaires = $this->getInventairesQuery()->paginate($this->perPage);

        return view('livewire.inventaires.liste-inventaires', [
            'inventaires' => $inventaires,
        ]);
    }
}

