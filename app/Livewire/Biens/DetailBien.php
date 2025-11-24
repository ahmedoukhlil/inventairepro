<?php

namespace App\Livewire\Biens;

use App\Models\Bien;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailBien extends Component
{
    /**
     * Instance du bien
     */
    public Bien $bien;

    /**
     * Initialisation du composant
     * 
     * @param Bien $bien
     */
    public function mount(Bien $bien): void
    {
        // Eager load des relations nécessaires
        $this->bien = $bien->load([
            'localisation',
            'user',
            'inventaireScans.inventaire',
            'inventaireScans.agent',
            'inventaireScans.localisationReelle',
        ]);
    }

    /**
     * Propriété calculée : Retourne l'historique des scans
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistoriqueScansProperty()
    {
        return $this->bien->inventaireScans()
            ->with(['inventaire', 'agent', 'localisationReelle'])
            ->orderBy('date_scan', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Propriété calculée : Calcule l'âge du bien en années
     * 
     * @return int
     */
    public function getAgeProperty(): int
    {
        return $this->bien->age;
    }

    /**
     * Propriété calculée : Retourne le nombre total de scans
     * 
     * @return int
     */
    public function getNombreScansProperty(): int
    {
        return $this->bien->inventaireScans()->count();
    }

    /**
     * Propriété calculée : Retourne le dernier scan
     * 
     * @return \App\Models\InventaireScan|null
     */
    public function getDernierScanProperty()
    {
        return $this->bien->inventaireScans()
            ->with(['inventaire', 'agent'])
            ->orderBy('date_scan', 'desc')
            ->first();
    }

    /**
     * Propriété calculée : Calcule le taux de présence (% fois trouvé)
     * 
     * @return float
     */
    public function getTauxPresenceProperty(): float
    {
        $totalScans = $this->bien->inventaireScans()->count();
        
        if ($totalScans === 0) {
            return 0;
        }

        $scansPresents = $this->bien->inventaireScans()
            ->where('statut_scan', 'present')
            ->count();

        return round(($scansPresents / $totalScans) * 100, 1);
    }

    /**
     * Propriété calculée : Retourne les mouvements (changements de localisation)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getMouvementsProperty()
    {
        $scans = $this->bien->inventaireScans()
            ->with(['localisationReelle'])
            ->whereNotNull('localisation_reelle_id')
            ->orderBy('date_scan', 'desc')
            ->get();

        $mouvements = collect();
        $derniereLocalisation = $this->bien->localisation_id;

        foreach ($scans as $scan) {
            if ($scan->localisation_reelle_id !== $derniereLocalisation) {
                $mouvements->push([
                    'date' => $scan->date_scan,
                    'ancienne_localisation_id' => $derniereLocalisation,
                    'nouvelle_localisation_id' => $scan->localisation_reelle_id,
                    'localisation' => $scan->localisationReelle,
                    'commentaire' => $scan->commentaire,
                    'inventaire' => $scan->inventaire,
                ]);
                $derniereLocalisation = $scan->localisation_reelle_id;
            }
        }

        return $mouvements;
    }

    /**
     * Génère le QR code du bien
     */
    public function genererQRCode(): void
    {
        try {
            $this->bien->generateQRCode();
            $this->bien->refresh();
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
        return redirect()->route('biens.etiquette', $this->bien);
    }

    /**
     * Supprime le bien
     */
    public function supprimer()
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer un bien.');
            return;
        }

        try {
            $this->bien->delete();
            session()->flash('success', 'Le bien a été supprimé avec succès.');
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

