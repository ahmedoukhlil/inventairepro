<?php

namespace App\Livewire\Inventaires;

use App\Models\Inventaire;
use App\Models\InventaireScan;
use App\Services\InventaireService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RapportInventaire extends Component
{
    /**
     * Instance de l'inventaire
     */
    public Inventaire $inventaire;

    /**
     * Onglet actif
     */
    public $activeTab = 'resume';

    /**
     * Filtres pour les tableaux
     */
    public $filterLocalisation = 'all';
    public $filterStatut = 'all';
    public $showPhotos = false;

    /**
     * Initialisation du composant
     */
    public function mount(Inventaire $inventaire): void
    {
        // Vérifier que l'inventaire est terminé ou clôturé
        if (!in_array($inventaire->statut, ['termine', 'cloture'])) {
            session()->flash('error', 'Le rapport n\'est disponible que pour les inventaires terminés ou clôturés.');
            redirect()->route('inventaires.show', $inventaire);
            return;
        }

        // Eager load des relations nécessaires
        $this->inventaire = $inventaire->load([
            'creator',
            'closer',
            'inventaireLocalisations.localisation',
            'inventaireLocalisations.agent',
            'inventaireScans.bien',
            'inventaireScans.localisationReelle',
            'inventaireScans.agent',
        ]);
    }

    /**
     * Propriété calculée : Retourne les statistiques complètes
     */
    public function getStatistiquesProperty(): array
    {
        $service = app(InventaireService::class);
        return $service->calculerStatistiques($this->inventaire);
    }

    /**
     * Propriété calculée : Retourne les biens présents
     */
    public function getBiensPresentsProperty()
    {
        $query = $this->inventaire->inventaireScans()
            ->where('statut_scan', 'present')
            ->with(['bien.localisation', 'agent']);

        if ($this->filterLocalisation !== 'all') {
            $query->whereHas('bien', function ($q) {
                $q->where('localisation_id', $this->filterLocalisation);
            });
        }

        return $query->orderBy('date_scan', 'desc')->get();
    }

    /**
     * Propriété calculée : Retourne les biens déplacés
     */
    public function getBiensDeplacesProperty()
    {
        $query = $this->inventaire->inventaireScans()
            ->where('statut_scan', 'deplace')
            ->with(['bien.localisation', 'localisationReelle', 'agent']);

        if ($this->filterLocalisation !== 'all') {
            $query->whereHas('bien', function ($q) {
                $q->where('localisation_id', $this->filterLocalisation);
            });
        }

        return $query->orderBy('date_scan', 'desc')->get();
    }

    /**
     * Propriété calculée : Retourne les biens absents
     */
    public function getBiensAbsentsProperty()
    {
        $scans = $this->inventaire->inventaireScans()
            ->where('statut_scan', 'absent')
            ->with(['bien.localisation', 'agent'])
            ->get();

        if ($this->filterLocalisation !== 'all') {
            $scans = $scans->filter(function ($scan) {
                return $scan->bien && $scan->bien->localisation_id == $this->filterLocalisation;
            });
        }

        // Trier par valeur décroissante
        return $scans->sortByDesc(function ($scan) {
            return $scan->bien->valeur_acquisition ?? 0;
        })->values();
    }

    /**
     * Propriété calculée : Retourne les biens détériorés
     */
    public function getBiensDeterioresProperty()
    {
        return $this->inventaire->inventaireScans()
            ->where('statut_scan', 'deteriore')
            ->with(['bien.localisation', 'agent'])
            ->orderBy('date_scan', 'desc')
            ->get();
    }

    /**
     * Propriété calculée : Retourne les biens non scannés
     */
    public function getBiensNonScannesProperty()
    {
        // Récupérer tous les biens attendus dans les localisations inventoriées
        $localisationIds = $this->inventaire->inventaireLocalisations()
            ->pluck('localisation_id')
            ->toArray();

        // Récupérer les IDs des biens déjà scannés
        $biensScannesIds = $this->inventaire->inventaireScans()
            ->pluck('bien_id')
            ->toArray();

        // Retourner les biens attendus mais non scannés
        return \App\Models\Bien::whereIn('localisation_id', $localisationIds)
            ->whereNotIn('id', $biensScannesIds)
            ->with('localisation')
            ->orderBy('code_inventaire')
            ->get();
    }

    /**
     * Propriété calculée : Retourne les anomalies détectées
     */
    public function getAnomaliesProperty(): array
    {
        $service = app(InventaireService::class);
        return $service->detecterAnomalies($this->inventaire);
    }

    /**
     * Change l'onglet actif
     */
    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Exporte le rapport en PDF
     */
    public function exportPDF()
    {
        try {
            $service = app(\App\Services\RapportService::class);
            $filePath = $service->genererRapportPDF($this->inventaire);
            
            return response()->download(storage_path('app/' . $filePath));
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Exporte le rapport en Excel
     */
    public function exportExcel()
    {
        try {
            $service = app(InventaireService::class);
            $filePath = $service->genererRapportExcel($this->inventaire);
            
            return response()->download(storage_path('app/public/' . $filePath));
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la génération de l\'Excel: ' . $e->getMessage());
        }
    }

    /**
     * Ouvre le dialogue d'impression
     */
    public function imprimerRapport(): void
    {
        $this->dispatch('print-report');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.inventaires.rapport-inventaire');
    }
}

