<?php

namespace App\Livewire\Localisations;

use App\Models\Localisation;
use App\Models\Bien;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DetailLocalisation extends Component
{
    use WithPagination;

    /**
     * Instance de la localisation
     */
    public Localisation $localisation;

    /**
     * Toggle pour afficher/masquer la liste des biens
     */
    public $afficherBiens = true;

    /**
     * Recherche dans les biens
     */
    public $searchBien = '';

    /**
     * Filtre par nature pour les biens
     */
    public $filterNature = '';

    /**
     * Initialisation du composant
     * 
     * @param Localisation $localisation
     */
    public function mount(Localisation $localisation): void
    {
        // Eager load des relations nécessaires (sans charger tous les biens en mémoire)
        $this->localisation = $localisation->load([
            'inventaireLocalisations.inventaire',
            'inventaireLocalisations.agent',
        ]);
        // Ne pas charger tous les biens ici, ils seront paginés dans getBiensProperty()
    }

    /**
     * Propriété calculée : Retourne les biens de cette localisation, filtrés
     */
    public function getBiensProperty()
    {
        $query = $this->localisation->biens();

        // Recherche
        if (!empty($this->searchBien)) {
            $query->where(function ($q) {
                $q->where('code_inventaire', 'like', '%' . $this->searchBien . '%')
                    ->orWhere('designation', 'like', '%' . $this->searchBien . '%');
            });
        }

        // Filtre par nature
        if (!empty($this->filterNature)) {
            $query->where('nature', $this->filterNature);
        }

        return $query->orderBy('code_inventaire')->paginate(10, ['*'], 'biensPage');
    }

    /**
     * Propriété calculée : Retourne les statistiques de la localisation
     * Optimisé : utilise des requêtes directes au lieu de charger tous les biens
     */
    public function getStatistiquesProperty(): array
    {
        // Utiliser des requêtes directes au lieu de charger tous les biens en mémoire
        $totalBiens = $this->localisation->biens()->count();
        $valeurTotale = $this->localisation->biens()->sum('valeur_acquisition');

        // Répartition par nature (requête directe)
        $parNature = $this->localisation->biens()
            ->selectRaw('nature, COUNT(*) as count')
            ->groupBy('nature')
            ->pluck('count', 'nature')
            ->toArray();

        // Répartition par état (requête directe)
        $parEtat = $this->localisation->biens()
            ->selectRaw('etat, COUNT(*) as count')
            ->groupBy('etat')
            ->pluck('count', 'etat')
            ->toArray();

        return [
            'total_biens' => $totalBiens,
            'valeur_totale' => $valeurTotale,
            'par_nature' => $parNature,
            'par_etat' => $parEtat,
        ];
    }

    /**
     * Propriété calculée : Retourne les 3 derniers inventaires concernant cette localisation
     */
    public function getDerniersInventairesProperty()
    {
        return $this->localisation->inventaireLocalisations()
            ->with(['inventaire', 'agent'])
            ->orderBy('date_debut_scan', 'desc')
            ->limit(3)
            ->get();
    }

    /**
     * Propriété calculée : Retourne tous les inventaires pour l'onglet détaillé
     */
    public function getTousInventairesProperty()
    {
        return $this->localisation->inventaireLocalisations()
            ->with(['inventaire', 'agent'])
            ->orderBy('date_debut_scan', 'desc')
            ->get();
    }

    /**
     * Propriété calculée : Retourne les mouvements récents (biens entrés/sortis)
     */
    public function getMouvementsRecentsProperty()
    {
        // Biens entrés : scans où localisation_reelle_id = cette localisation mais bien.localisation_id était différent avant
        $biensEntres = \App\Models\InventaireScan::query()
            ->where('localisation_reelle_id', $this->localisation->id)
            ->with(['bien', 'inventaire', 'agent'])
            ->whereHas('bien', function ($q) {
                $q->where('localisation_id', '!=', $this->localisation->id);
            })
            ->orderBy('date_scan', 'desc')
            ->limit(10)
            ->get();

        // Biens sortis : scans où localisation_reelle_id != cette localisation mais bien.localisation_id = cette localisation
        $biensSortis = \App\Models\InventaireScan::query()
            ->where('localisation_reelle_id', '!=', $this->localisation->id)
            ->whereNotNull('localisation_reelle_id')
            ->with(['bien', 'localisationReelle', 'inventaire', 'agent'])
            ->whereHas('bien', function ($q) {
                $q->where('localisation_id', $this->localisation->id);
            })
            ->orderBy('date_scan', 'desc')
            ->limit(10)
            ->get();

        return [
            'entres' => $biensEntres,
            'sortis' => $biensSortis,
        ];
    }

    /**
     * Toggle l'affichage de la liste des biens
     */
    public function toggleAfficherBiens(): void
    {
        $this->afficherBiens = !$this->afficherBiens;
        $this->resetPage(); // Réinitialiser la pagination
    }

    /**
     * Génère le QR code de la localisation
     */
    public function genererQRCode(): void
    {
        try {
            $this->localisation->generateQRCode();
            $this->localisation->refresh();
            session()->flash('success', 'QR code généré avec succès');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la génération du QR code: ' . $e->getMessage());
        }
    }

    /**
     * Télécharge l'étiquette
     */
    public function telechargerEtiquette()
    {
        return redirect()->route('localisations.etiquette', $this->localisation);
    }

    /**
     * Supprime la localisation
     */
    public function supprimer()
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer une localisation.');
            return;
        }

        // Vérifier qu'aucun bien n'est affecté
        $nombreBiens = $this->localisation->biens()->count();
        
        if ($nombreBiens > 0) {
            session()->flash('error', "Impossible de supprimer cette localisation : {$nombreBiens} bien(s) y sont affecté(s). Veuillez d'abord réaffecter ces biens à une autre localisation.");
            return;
        }

        try {
            $this->localisation->delete();
            session()->flash('success', 'La localisation a été supprimée avec succès.');
            return redirect()->route('localisations.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.localisations.detail-localisation');
    }
}

