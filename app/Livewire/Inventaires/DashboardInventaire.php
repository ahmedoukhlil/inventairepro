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
        
        if (!$user->isAdmin() && !$inventaire->inventaireLocalisations()->where('user_id', $user->idUser)->exists()) {
            abort(403, 'Vous n\'avez pas accès à cet inventaire.');
        }

        // Charger les relations nécessaires dès le départ
        $this->inventaire = $inventaire->load([
            'inventaireLocalisations.localisation',
            'inventaireLocalisations.agent',
            'inventaireScans'
        ]);
    }

    /**
     * Propriété calculée : Retourne les statistiques complètes de l'inventaire
     */
    public function getStatistiquesProperty(): array
    {
        // Recharger les relations pour avoir les données à jour
        $this->inventaire->refresh();
        $this->inventaire->load([
            'inventaireLocalisations.localisation',
            'inventaireScans'
        ]);
        
        $inventaireLocalisations = $this->inventaire->inventaireLocalisations;
        $scans = $this->inventaire->inventaireScans;

        $totalLocalisations = $inventaireLocalisations->count();
        $localisationsTerminees = $inventaireLocalisations->where('statut', 'termine')->count();
        $localisationsEnCours = $inventaireLocalisations->where('statut', 'en_cours')->count();
        $localisationsEnAttente = $inventaireLocalisations->where('statut', 'en_attente')->count();

        // Calculer le total de biens attendus (somme de tous les biens attendus dans toutes les localisations)
        $totalBiensAttendus = $inventaireLocalisations->sum('nombre_biens_attendus');
        
        // Si le total est 0, recalculer depuis les emplacements
        if ($totalBiensAttendus == 0) {
            foreach ($inventaireLocalisations as $invLoc) {
                if ($invLoc->localisation) {
                    $nombreBiensAttendus = $invLoc->localisation->emplacements()
                        ->withCount('immobilisations')
                        ->get()
                        ->sum('immobilisations_count');
                    
                    if ($invLoc->nombre_biens_attendus == 0) {
                        $invLoc->update(['nombre_biens_attendus' => $nombreBiensAttendus]);
                    }
                }
            }
            // Recharger après mise à jour
            $this->inventaire->refresh();
            $inventaireLocalisations = $this->inventaire->inventaireLocalisations;
            $totalBiensAttendus = $inventaireLocalisations->sum('nombre_biens_attendus');
        }

        // Total de biens scannés : utiliser le nombre depuis les localisations (plus fiable)
        // car il est mis à jour lors de la finalisation du scan
        $totalBiensScannesFromLoc = $inventaireLocalisations->sum('nombre_biens_scannes');
        
        // Compter aussi les scans uniques dans la table inventaire_scans
        $totalBiensScannesFromScans = $scans->unique('bien_id')->count();
        
        // Utiliser le maximum entre les deux pour être sûr d'avoir le bon nombre
        $totalBiensScannes = max($totalBiensScannesFromLoc, $totalBiensScannesFromScans);
        
        // Si toujours 0, utiliser le count simple des scans
        if ($totalBiensScannes == 0) {
            $totalBiensScannes = $scans->count();
        }

        $biensPresents = $scans->where('statut_scan', 'present')->count();
        $biensDeplaces = $scans->where('statut_scan', 'deplace')->count();
        $biensAbsents = $scans->where('statut_scan', 'absent')->count();
        $biensDeteriores = $scans->where('statut_scan', 'deteriore')->count();
        $biensDefectueux = $scans->where('etat_constate', 'mauvais')->count();

        // Progression globale : basée sur les biens scannés vs attendus
        $progressionGlobale = $totalBiensAttendus > 0 
            ? round(($totalBiensScannes / $totalBiensAttendus) * 100, 1) 
            : 0;
        
        // Progression par localisations (pour information)
        $progressionLocalisations = $totalLocalisations > 0 
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
            'localisations_en_attente' => $localisationsEnAttente,
            'total_biens_attendus' => $totalBiensAttendus,
            'total_biens_scannes' => $totalBiensScannes,
            'biens_presents' => $biensPresents,
            'biens_deplaces' => $biensDeplaces,
            'biens_absents' => $biensAbsents,
            'biens_deteriores' => $biensDeteriores,
            'biens_defectueux' => $biensDefectueux,
            'progression_globale' => $progressionGlobale,
            'progression_localisations' => $progressionLocalisations,
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
        // Recharger les relations pour avoir les données à jour
        $this->inventaire->load(['inventaireLocalisations.localisation', 'inventaireLocalisations.agent']);
        
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
                $q->where('CodeLocalisation', 'like', '%' . $this->searchLoc . '%')
                    ->orWhere('Localisation', 'like', '%' . $this->searchLoc . '%');
            });
        }

        // Récupérer les résultats
        $results = $query->get();

        // Tri manuel si nécessaire (pour le code de localisation)
        if ($this->sortField === 'code') {
            $results = $results->sortBy(function ($invLoc) {
                return $invLoc->localisation->CodeLocalisation ?? $invLoc->localisation->Localisation ?? '';
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        return $results->values();
    }

    /**
     * Propriété calculée : Données pour le graphique progression par localisation
     */
    public function getLocalisationsGraphDataProperty()
    {
        return $this->inventaireLocalisations->take(10)->values();
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
            ->unique('idUser')
            ->values();
    }

    /**
     * Propriété calculée : Retourne les 20 derniers scans
     */
    public function getDerniersScansProperty()
    {
        // Recharger les scans pour avoir les données à jour (compatible PWA: gesimmo)
        $this->inventaire->load([
            'inventaireScans.gesimmo.designation',
            'inventaireScans.gesimmo.categorie',
            'inventaireScans.bien',
            'inventaireScans.localisationReelle',
            'inventaireScans.agent'
        ]);
        
        return $this->inventaire->inventaireScans()
            ->with(['gesimmo.designation', 'gesimmo.categorie', 'bien', 'localisationReelle', 'agent'])
            ->orderBy('date_scan', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($scan) {
                // InventaireScan a des accesseurs code_inventaire et designation (compatible PWA)
                return $scan;
            });
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
            'biens_defectueux' => [],
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
                'code' => $invLoc->localisation->CodeLocalisation ?? $invLoc->localisation->Localisation ?? 'N/A',
                'designation' => $invLoc->localisation->Localisation ?? 'N/A',
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
                'code' => $invLoc->localisation->CodeLocalisation ?? $invLoc->localisation->Localisation ?? 'N/A',
                'designation' => $invLoc->localisation->Localisation ?? 'N/A',
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
                'code' => $scan->code_inventaire,
                'designation' => $scan->designation,
                'valeur' => $scan->bien->valeur_acquisition ?? 0,
            ];
        }

        // Biens défectueux (etat_constate = mauvais, signalés via PWA)
        $biensDefectueux = $this->inventaire->inventaireScans()
            ->where('etat_constate', 'mauvais')
            ->with(['gesimmo.designation', 'localisationReelle'])
            ->get();
        
        foreach ($biensDefectueux as $scan) {
            $alertes['biens_defectueux'][] = [
                'bien_id' => $scan->bien_id,
                'code' => $scan->code_inventaire,
                'designation' => $scan->designation,
                'localisation' => $scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? 'N/A',
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
                'code' => $invLoc->localisation->CodeLocalisation ?? $invLoc->localisation->Localisation ?? 'N/A',
                'designation' => $invLoc->localisation->Localisation ?? 'N/A',
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
            + count($alertes['biens_defectueux'] ?? [])
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
     * Rafraîchit uniquement les statistiques de manière légère
     * Émet un événement pour déclencher l'animation
     */
    public function refreshStatistiques(): void
    {
        // Recharger l'inventaire avec toutes ses relations
        $this->inventaire->refresh();
        $this->inventaire->load([
            'inventaireLocalisations.localisation',
            'inventaireLocalisations.agent',
            'inventaireScans'
        ]);
        
        // Émettre un événement pour l'indicateur visuel
        $this->dispatch('statistiques-updated');
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


