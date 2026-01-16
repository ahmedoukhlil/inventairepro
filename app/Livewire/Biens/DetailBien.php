<?php

namespace App\Livewire\Biens;

use App\Models\Gesimmo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailBien extends Component
{
    /**
     * Instance de l'immobilisation
     */
    public Gesimmo $bien;

    /**
     * Initialisation du composant
     * 
     * @param Gesimmo $bien
     */
    public function mount(Gesimmo $bien): void
    {
        // Eager load des relations nécessaires (sans 'code' car généré côté client)
        $this->bien = $bien->load([
            'designation.categorie',
            'categorie',
            'etat',
            'emplacement.localisation',
            'emplacement.affectation',
            'natureJuridique',
            'sourceFinancement',
        ]);
    }

    /**
     * Propriété calculée : Calcule l'âge de l'immobilisation en années
     * 
     * @return int|null
     */
    public function getAgeProperty(): ?int
    {
        // DateAcquisition contient l'année (ex: 2019)
        if (!$this->bien->DateAcquisition || $this->bien->DateAcquisition <= 1970) {
            return null;
        }
        
        $age = now()->year - $this->bien->DateAcquisition;
        
        // Ne retourner que si l'âge est positif et raisonnable (< 100 ans)
        return ($age > 0 && $age < 100) ? $age : null;
    }

    /**
     * Propriété calculée : Retourne le code d'immobilisation formaté
     * 
     * @return string
     */
    public function getCodeFormateProperty(): string
    {
        return $this->bien->code_formate ?? '';
    }


    /**
     * Lance l'impression de l'étiquette
     */
    public function telechargerEtiquette()
    {
        // L'URL sera ouverte dans une nouvelle fenêtre avec JavaScript
        // et l'impression sera lancée automatiquement
        $this->dispatch('print-etiquette', url: route('biens.etiquette', $this->bien));
    }

    /**
     * Supprime l'immobilisation
     */
    public function supprimer()
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer une immobilisation.');
            return;
        }

        try {
            $this->bien->delete();
            session()->flash('success', 'L\'immobilisation a été supprimée avec succès.');
            return $this->redirect(route('biens.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.biens.detail-bien');
    }
}

