<?php

namespace App\Livewire\Inventaires;

use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Models\InventaireScan;
use App\Models\Bien;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardInventaire extends Component
{
    /**
     * Instance de l'inventaire
     */
    public Inventaire $inventaire;

    /**
     * Propriétés pour les filtres
     */
    public $filterStatutLoc = 'all'; // all, en_attente, en_cours, termine
    public $filterAgent = 'all';
    public $searchLoc = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';

    /**
     * Initialisation du composant
     */
    public function mount(Inventaire $inventaire): void
    {
        // Vérifier autorisation (admin ou agent assigné)
        $user = Auth::user();
        
        if (!$user->isAdmin() && !$inventaire->inventaireLocalisations()->where('user_id', $user->id)->exists()) {
            abort(403, 'Vous n\'avez pas accès à cet inventaire.');
        }

        $this->inventaire = $inventaire;
    }

    /**
     * Propriété calculée : Retourne les statistiques complètes de l'inventaire
     */
    public function getStatistiquesProperty(): array
    {
        $inventaireLocalisations = $this->inventaire->inventaireLocalisations;
        $scans = $this->inventaire->inventaireScans;

        $totalLocalisations = $inventaireLocalisations->count();
        $localisationsTerminees = $inventaireLocalisations->where('statut', 'termine')->count();
        $localisationsEnCours = $inventaireLocalisations->where('statut', 'en_cours')->count();

        $totalBiensAttendus = $inventaireLocalisations->sum('nombre_biens_attendus');
        $totalBiensScannes = $scans->count();

        $biensPresents = $scans->where('statut_scan', 'present')->count();
        $biensDeplaces = $scans->where('statut_scan', 'deplace')->count();
        $biensAbsents = $scans->where('statut_scan', 'absent')->count();
        $biensDeteriores = $scans->where('statut_scan', 'deteriore')->count();

        $progressionGlobale = $totalLocalisations > 0 
            ? round(($localisationsTerminees / $totalLocalisations) * 100, 1) 
            : 0;

        $tauxConformite = $totalBiensScannes > 0 
            ? round(($biensPresents / $totalBiensScannes) * 100, 1) 
            : 0;

        $dureeJours = $this->inventaire->duree ?? 0;

        // Scans effectués aujourd'hui
        $scansAujourdhui = $scans->filter(function ($scan) {
            return $scan->date_scan && $scan->date_scan->isToday();
        })->count();

        // Vitesse moyenne (scans par jour)
        $vitesseMoyenne = 0;
        if ($this->inventaire->date_debut && $totalBiensScannes > 0) {
            $joursEcoules = max(1, now()->diffInDays($this->inventaire->date_debut));
            $vitesseMoyenne = round($totalBiensScannes / $joursEcoules, 1);
        }

        return [
            'total_localisations' => $totalLocalisations,
            'localisations_terminees' => $localisationsTerminees,
            'localisations_en_cours' => $localisationsEnCours,
            'localisations_en_attente' => $inventaireLocalisations->where('statut', 'en_attente')->count(),
            'total_biens_attendus' => $totalBiensAttendus,
            'total_biens_scannes' => $totalBiensScannes,
            'biens_presents' => $biensPresents,
            'biens_deplaces' => $biensDeplaces,
            'biens_absents' => $biensAbsents,
            'biens_deteriores' => $biensDeteriores,
            'progression_globale' => $progressionGlobale,
            'taux_conformite' => $tauxConformite,
            'duree_jours' => $dureeJours,
            'scans_aujourdhui' => $scansAujourdhui,
            'vitesse_moyenne' => $vitesseMoyenne,
        ];
    }

    /**
     * Propriété calculée : Retourne les localisations filtrées et triées
     */
    public function getInventaireLocalisationsProperty()
    {
        $query = $this->inventaire->inventaireLocalisations()
            ->with(['localisation', 'agent']);

        // Filtre par statut
        if ($this->filterStatutLoc !== 'all') {
            $query->where('statut', $this->filterStatutLoc);
        }

        // Filtre par agent
        if ($this->filterAgent !== 'all') {
            $query->where('user_id', $this->filterAgent);
        }

        // Recherche
        if (!empty($this->searchLoc)) {
            $query->whereHas('localisation', function ($q) {
                $q->where('code', 'like', '%' . $this->searchLoc . '%')
                    ->orWhere('designation', 'like', '%' . $this->searchLoc . '%');
            });
        }

        // Récupérer les résultats
        $results = $query->get();

        // Tri manuel si nécessaire (pour le code de localisation)
        if ($this->sortField === 'code') {
            $results = $results->sortBy(function ($invLoc) {
                return $invLoc->localisation->code ?? '';
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        return $results->values();
    }

    /**
     * Propriété calculée : Retourne la liste des agents assignés
     */
    public function getAgentsProperty()
    {
        return $this->inventaire->inventaireLocalisations()
            ->whereNotNull('user_id')
            ->with('agent')
            ->get()
            ->pluck('agent')
            ->unique('id')
            ->values();
    }

    /**
     * Propriété calculée : Retourne les 20 derniers scans
     */
    public function getDerniersScansProperty()
    {
        return $this->inventaire->inventaireScans()
            ->with(['bien', 'localisationReelle', 'agent'])
            ->orderBy('date_scan', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Propriété calculée : Retourne les alertes détectées
     */
    public function getAlertesProperty(): array
    {
        $alertes = [
            'localisations_non_demarrees' => [],
            'localisations_bloquees' => [],
            'biens_absents_valeur_haute' => [],
            'localisations_non_assignees' => [],
        ];

        // Localisations non démarrées
        $nonDemarrees = $this->inventaire->inventaireLocalisations()
            ->where('statut', 'en_attente')
            ->with('localisation')
            ->get();
        
        foreach ($nonDemarrees as $invLoc) {
            $alertes['localisations_non_demarrees'][] = [
                'id' => $invLoc->id,
                'code' => $invLoc->localisation->code,
                'designation' => $invLoc->localisation->designation,
            ];
        }

        // Localisations bloquées (pas de scan depuis 24h)
        $ilY24h = now()->subDay();
        $bloquees = $this->inventaire->inventaireLocalisations()
            ->where('statut', 'en_cours')
            ->where(function ($q) use ($ilY24h) {
                $q->whereNull('date_debut_scan')
                    ->orWhere('date_debut_scan', '<', $ilY24h);
            })
            ->with('localisation')
            ->get();
        
        foreach ($bloquees as $invLoc) {
            $dernierScan = $this->inventaire->inventaireScans()
                ->where('inventaire_localisation_id', $invLoc->id)
                ->orderBy('date_scan', 'desc')
                ->first();
            
            $joursSansScan = $dernierScan 
                ? $dernierScan->date_scan->diffInDays(now())
                : ($invLoc->date_debut_scan ? $invLoc->date_debut_scan->diffInDays(now()) : 0);
            
            $alertes['localisations_bloquees'][] = [
                'id' => $invLoc->id,
                'code' => $invLoc->localisation->code,
                'designation' => $invLoc->localisation->designation,
                'jours' => $joursSansScan,
            ];
        }

        // Biens absents de valeur élevée (>100k MRU)
        $biensAbsents = $this->inventaire->inventaireScans()
            ->where('statut_scan', 'absent')
            ->with('bien')
            ->get()
            ->filter(function ($scan) {
                return $scan->bien && $scan->bien->valeur_acquisition > 100000;
            })
            ->take(10);
        
        foreach ($biensAbsents as $scan) {
            $alertes['biens_absents_valeur_haute'][] = [
                'bien_id' => $scan->bien_id,
                'code' => $scan->bien->code_inventaire,
                'designation' => $scan->bien->designation,
                'valeur' => $scan->bien->valeur_acquisition,
            ];
        }

        // Localisations non assignées
        $nonAssignees = $this->inventaire->inventaireLocalisations()
            ->whereNull('user_id')
            ->with('localisation')
            ->get();
        
        foreach ($nonAssignees as $invLoc) {
            $alertes['localisations_non_assignees'][] = [
                'id' => $invLoc->id,
                'code' => $invLoc->localisation->code,
                'designation' => $invLoc->localisation->designation,
            ];
        }

        return $alertes;
    }

    /**
     * Propriété calculée : Retourne le nombre total d'alertes
     */
    public function getTotalAlertesProperty(): int
    {
        $alertes = $this->alertes;
        return count($alertes['localisations_non_demarrees']) 
            + count($alertes['localisations_bloquees'])
            + count($alertes['biens_absents_valeur_haute'])
            + count($alertes['localisations_non_assignees']);
    }

    /**
     * Recharge les statistiques (appelé par polling)
     */
    public function refresh(): void
    {
        $this->inventaire->refresh();
        // Les propriétés calculées seront recalculées automatiquement
    }

    /**
     * Passe l'inventaire en cours
     */
    public function passerEnCours(): void
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent démarrer un inventaire.');
            return;
        }

        if ($this->inventaire->statut !== 'en_preparation') {
            session()->flash('error', 'Seuls les inventaires en préparation peuvent être démarrés.');
            return;
        }

        try {
            $this->inventaire->demarrer();
            $this->inventaire->refresh();
            session()->flash('success', "L'inventaire {$this->inventaire->annee} a été démarré avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du démarrage: ' . $e->getMessage());
        }
    }

    /**
     * Termine l'inventaire
     */
    public function terminerInventaire(): void
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent terminer un inventaire.');
            return;
        }

        // Vérifier que toutes les localisations sont terminées
        $localisationsNonTerminees = $this->inventaire->inventaireLocalisations()
            ->where('statut', '!=', 'termine')
            ->count();

        if ($localisationsNonTerminees > 0) {
            session()->flash('error', "Toutes les localisations doivent être terminées avant de terminer l'inventaire. {$localisationsNonTerminees} localisation(s) restante(s).");
            return;
        }

        try {
            $this->inventaire->update([
                'statut' => 'termine',
                'date_fin' => now(),
            ]);
            $this->inventaire->refresh();
            session()->flash('success', "L'inventaire {$this->inventaire->annee} a été terminé avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la finalisation: ' . $e->getMessage());
        }
    }

    /**
     * Clôture définitivement l'inventaire
     */
    public function cloturerInventaire()
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent clôturer un inventaire.');
            return;
        }

        if ($this->inventaire->statut !== 'termine') {
            session()->flash('error', 'Seuls les inventaires terminés peuvent être clôturés.');
            return;
        }

        try {
            $this->inventaire->cloturer();
            session()->flash('success', "L'inventaire {$this->inventaire->annee} a été clôturé définitivement.");
            return redirect()->route('inventaires.rapport', $this->inventaire);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la clôture: ' . $e->getMessage());
        }
    }

    /**
     * Réassigne une localisation à un agent
     */
    public function reassignerLocalisation($invLocId, $userId): void
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent réassigner des localisations.');
            return;
        }

        $invLoc = InventaireLocalisation::find($invLocId);

        if (!$invLoc || $invLoc->inventaire_id !== $this->inventaire->id) {
            session()->flash('error', 'Localisation introuvable.');
            return;
        }

        try {
            $invLoc->update([
                'user_id' => $userId ?: null,
            ]);
            session()->flash('success', 'Localisation réassignée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la réassignation: ' . $e->getMessage());
        }
    }

    /**
     * Change le tri de la colonne
     */
    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.inventaires.dashboard-inventaire');
    }
}

