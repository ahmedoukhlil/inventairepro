<?php

namespace App\Livewire\Localisations;

use App\Models\LocalisationImmo;
use App\Models\Emplacement;
use App\Models\Gesimmo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DetailLocalisation extends Component
{
    use WithPagination;

    /**
     * Instance de la localisation
     */
    public LocalisationImmo $localisation;

    /**
     * Toggle pour afficher/masquer la liste des emplacements
     */
    public $afficherEmplacements = true;

    /**
     * Recherche dans les emplacements
     */
    public $searchEmplacement = '';

    /**
     * Initialisation du composant
     * 
     * @param LocalisationImmo $localisation
     */
    public function mount(LocalisationImmo $localisation): void
    {
        // Eager load des relations nécessaires
        $this->localisation = $localisation->load([
            'emplacements.affectation',
            'emplacements.immobilisations',
        ]);
    }

    /**
     * Propriété calculée : Retourne les emplacements de cette localisation, filtrés
     */
    public function getEmplacementsProperty()
    {
        $query = $this->localisation->emplacements()->with(['affectation', 'immobilisations']);

        // Recherche
        if (!empty($this->searchEmplacement)) {
            $query->where(function ($q) {
                $q->where('Emplacement', 'like', '%' . $this->searchEmplacement . '%')
                    ->orWhere('CodeEmplacement', 'like', '%' . $this->searchEmplacement . '%');
            });
        }

        return $query->orderBy('Emplacement')->paginate(10, ['*'], 'emplacementsPage');
    }

    /**
     * Propriété calculée : Retourne les statistiques de la localisation
     */
    public function getStatistiquesProperty(): array
    {
        $totalEmplacements = $this->localisation->emplacements()->count();
        
        // Compter les immobilisations via les emplacements
        $totalImmobilisations = Gesimmo::whereHas('emplacement', function ($q) {
            $q->where('idLocalisation', $this->localisation->idLocalisation);
        })->count();

        return [
            'total_emplacements' => $totalEmplacements,
            'total_immobilisations' => $totalImmobilisations,
        ];
    }

    /**
     * Toggle l'affichage de la liste des emplacements
     */
    public function toggleAfficherEmplacements(): void
    {
        $this->afficherEmplacements = !$this->afficherEmplacements;
        $this->resetPage(); // Réinitialiser la pagination
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

        // Vérifier qu'aucun emplacement n'est associé
        $nombreEmplacements = $this->localisation->emplacements()->count();
        
        if ($nombreEmplacements > 0) {
            session()->flash('error', "Impossible de supprimer cette localisation : {$nombreEmplacements} emplacement(s) y sont associé(s).");
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

