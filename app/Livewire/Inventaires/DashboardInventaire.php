<?php

namespace App\Livewire\Inventaires;

use App\Models\Emplacement;
use App\Models\Gesimmo;
use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Models\InventaireScan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
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
     * Horodatage de la dernière synchronisation (Unix timestamp)
     * Permet l'indicateur "Dernière MAJ il y a Xs" côté vue
     */
    public $lastSyncAt;

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

        // On conserve uniquement l'instance Inventaire, les stats sont calculées via SQL agrégé
        $this->inventaire = $inventaire;
        $this->lastSyncAt = now()->timestamp;
    }

    /**
     * Propriété calculée : Statistiques complètes via agrégats SQL (2 requêtes)
     * Remplace l'ancienne version qui chargeait toutes les collections en mémoire.
     */
    #[Computed]
    public function statistiques(): array
    {
        // Agrégat sur inventaire_localisations
        $locAgg = DB::table('inventaire_localisations')
            ->selectRaw("
                COUNT(*) as total_localisations,
                SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as localisations_terminees,
                SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as localisations_en_cours,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as localisations_en_attente,
                COALESCE(SUM(nombre_biens_attendus), 0) as total_biens_attendus,
                COALESCE(SUM(nombre_biens_scannes), 0) as total_biens_scannes_loc
            ")
            ->where('inventaire_id', $this->inventaire->id)
            ->first();

        $totalBiensAttendus = (int) $locAgg->total_biens_attendus;

        // Lazy-init si 0 (première ouverture du dashboard)
        if ($totalBiensAttendus === 0 && (int) $locAgg->total_localisations > 0) {
            $this->recalculerBiensAttendus();
            $locAgg = DB::table('inventaire_localisations')
                ->selectRaw('COALESCE(SUM(nombre_biens_attendus), 0) as total_biens_attendus, COALESCE(SUM(nombre_biens_scannes), 0) as total_biens_scannes_loc')
                ->where('inventaire_id', $this->inventaire->id)
                ->first();
            $totalBiensAttendus = (int) $locAgg->total_biens_attendus;

            // Re-lecture du reste pour rester cohérent
            $locCounts = DB::table('inventaire_localisations')
                ->selectRaw("
                    COUNT(*) as total_localisations,
                    SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as localisations_terminees,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as localisations_en_cours,
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as localisations_en_attente
                ")
                ->where('inventaire_id', $this->inventaire->id)
                ->first();
            $totalLocalisations = (int) $locCounts->total_localisations;
            $localisationsTerminees = (int) $locCounts->localisations_terminees;
            $localisationsEnCours = (int) $locCounts->localisations_en_cours;
            $localisationsEnAttente = (int) $locCounts->localisations_en_attente;
        } else {
            $totalLocalisations = (int) $locAgg->total_localisations;
            $localisationsTerminees = (int) $locAgg->localisations_terminees;
            $localisationsEnCours = (int) $locAgg->localisations_en_cours;
            $localisationsEnAttente = (int) $locAgg->localisations_en_attente;
        }

        // Agrégat sur inventaire_scans
        $scanAgg = DB::table('inventaire_scans')
            ->selectRaw("
                COUNT(*) as total_scans,
                COUNT(DISTINCT bien_id) as biens_uniques,
                SUM(CASE WHEN statut_scan = 'present' THEN 1 ELSE 0 END) as biens_presents,
                SUM(CASE WHEN statut_scan = 'deplace' THEN 1 ELSE 0 END) as biens_deplaces,
                SUM(CASE WHEN statut_scan = 'absent' THEN 1 ELSE 0 END) as biens_absents,
                SUM(CASE WHEN statut_scan = 'deteriore' THEN 1 ELSE 0 END) as biens_deteriores,
                SUM(CASE WHEN etat_constate = 'mauvais' THEN 1 ELSE 0 END) as biens_defectueux,
                SUM(CASE WHEN DATE(date_scan) = ? THEN 1 ELSE 0 END) as scans_aujourdhui
            ", [now()->toDateString()])
            ->where('inventaire_id', $this->inventaire->id)
            ->first();

        $totalBiensScannesFromLoc = (int) $locAgg->total_biens_scannes_loc;
        $totalBiensScannes = max($totalBiensScannesFromLoc, (int) $scanAgg->biens_uniques, (int) $scanAgg->total_scans);

        $progressionGlobale = $totalBiensAttendus > 0
            ? round(($totalBiensScannes / $totalBiensAttendus) * 100, 1)
            : 0;

        $progressionLocalisations = $totalLocalisations > 0
            ? round(($localisationsTerminees / $totalLocalisations) * 100, 1)
            : 0;

        $biensPresents = (int) $scanAgg->biens_presents;
        $tauxConformite = $totalBiensAttendus > 0
            ? round(($biensPresents / $totalBiensAttendus) * 100, 1)
            : 0;

        $biensNonVerifies = max(0, $totalBiensAttendus - $totalBiensScannes);

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
            'biens_deplaces' => (int) $scanAgg->biens_deplaces,
            'biens_absents' => (int) $scanAgg->biens_absents,
            'biens_deteriores' => (int) $scanAgg->biens_deteriores,
            'biens_defectueux' => (int) $scanAgg->biens_defectueux,
            'progression_globale' => $progressionGlobale,
            'progression_localisations' => $progressionLocalisations,
            'taux_conformite' => $tauxConformite,
            'biens_non_verifies' => $biensNonVerifies,
            'duree_jours' => $this->inventaire->duree ?? 0,
            'scans_aujourdhui' => (int) $scanAgg->scans_aujourdhui,
            'vitesse_moyenne' => $vitesseMoyenne,
        ];
    }

    /**
     * Recalcule les biens attendus depuis les emplacements (opération coûteuse, exécutée une seule fois)
     */
    private function recalculerBiensAttendus(): void
    {
        foreach ($this->inventaire->inventaireLocalisations as $invLoc) {
            if ($invLoc->localisation && $invLoc->nombre_biens_attendus == 0) {
                $nombreBiensAttendus = $invLoc->localisation->emplacements()
                    ->withCount('immobilisations')
                    ->get()
                    ->sum('immobilisations_count');
                
                $invLoc->update(['nombre_biens_attendus' => $nombreBiensAttendus]);
            }
        }
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
     * Propriété calculée : Données pour le graphique progression temporelle
     * Agrégé en SQL (GROUP BY date) : 1 ligne/jour au lieu de N scans.
     */
    #[Computed]
    public function scansGraphData()
    {
        return DB::table('inventaire_scans')
            ->selectRaw('DATE(date_scan) as date_scan, COUNT(*) as nb')
            ->where('inventaire_id', $this->inventaire->id)
            ->whereNotNull('date_scan')
            ->groupBy(DB::raw('DATE(date_scan)'))
            ->orderBy('date_scan')
            ->get();
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
     * Propriété calculée : Retourne les 10 derniers scans (live feed)
     */
    #[Computed]
    public function derniersScans()
    {
        return $this->inventaire->inventaireScans()
            ->with(['bien.designation', 'localisationReelle', 'agent'])
            ->orderBy('date_scan', 'desc')
            ->limit(10)
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

        // Biens absents (liste les 10 premiers)
        $biensAbsents = $this->inventaire->inventaireScans()
            ->where('statut_scan', 'absent')
            ->with('bien.designation')
            ->limit(10)
            ->get();
        
        foreach ($biensAbsents as $scan) {
            $alertes['biens_absents_valeur_haute'][] = [
                'bien_id' => $scan->bien_id,
                'code' => $scan->code_inventaire,
                'designation' => $scan->designation,
                'valeur' => 0,
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
                'photo_url' => $scan->photo_url,
                'commentaire' => $scan->commentaire,
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
     * Propriété calculée : Nombre total d'alertes — 2 requêtes agrégées
     */
    #[Computed]
    public function totalAlertes(): int
    {
        $ilY24h = now()->subDay();

        $locAlertes = DB::table('inventaire_localisations')
            ->selectRaw("
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as non_demarrees,
                SUM(CASE WHEN statut = 'en_cours' AND (date_debut_scan IS NULL OR date_debut_scan < ?) THEN 1 ELSE 0 END) as bloquees,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as non_assignees
            ", [$ilY24h])
            ->where('inventaire_id', $this->inventaire->id)
            ->first();

        $scanAlertes = DB::table('inventaire_scans')
            ->selectRaw("
                SUM(CASE WHEN statut_scan = 'absent' THEN 1 ELSE 0 END) as absents,
                SUM(CASE WHEN etat_constate = 'mauvais' THEN 1 ELSE 0 END) as defectueux
            ")
            ->where('inventaire_id', $this->inventaire->id)
            ->first();

        return (int) $locAlertes->non_demarrees
            + (int) $locAlertes->bloquees
            + (int) $locAlertes->non_assignees
            + (int) $scanAlertes->absents
            + (int) $scanAlertes->defectueux;
    }

    /**
     * Recharge les statistiques (appelé par polling)
     */
    public function refresh(): void
    {
        $this->inventaire->refresh();
        $this->lastSyncAt = now()->timestamp;
    }

    /**
     * Rafraîchit les statistiques (appelé par wire:poll.5s).
     * Les #[Computed] sont invalidés automatiquement à chaque requête Livewire,
     * donc il suffit de rafraîchir l'inventaire et dispatcher l'event d'animation.
     */
    public function refreshStatistiques(): void
    {
        $this->inventaire->refresh();
        $this->lastSyncAt = now()->timestamp;
        $this->dispatch('statistiques-updated');
    }

    /**
     * Passe l'inventaire en cours
     */
    public function passerEnCours(): void
    {
        if (!Auth::user()->isAdmin()) {
            $this->toast('Seuls les administrateurs peuvent démarrer un inventaire.', 'error');
            return;
        }

        if ($this->inventaire->statut !== 'en_preparation') {
            $this->toast('Seuls les inventaires en préparation peuvent être démarrés.', 'error');
            return;
        }

        try {
            $this->inventaire->demarrer();
            $this->inventaire->refresh();
            $this->toast("L'inventaire {$this->inventaire->annee} a été démarré avec succès.", 'success');
        } catch (\Exception $e) {
            $this->toast('Erreur lors du démarrage: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Helper : flash + event browser pour toast (affiché par le layout sans reload)
     */
    private function toast(string $message, string $type = 'info'): void
    {
        session()->flash($type, $message);
        $this->dispatch('toast', message: $message, type: $type);
    }

    /**
     * Nombre de localisations non terminées (utilisé par la vue pour afficher le bouton Forcer)
     */
    #[Computed]
    public function localisationsNonTerminees(): int
    {
        return $this->inventaire->inventaireLocalisations()
            ->where('statut', '!=', 'termine')
            ->count();
    }

    /**
     * Termine l'inventaire (mode strict : toutes les localisations doivent être terminées)
     */
    public function terminerInventaire(): void
    {
        if (!Auth::user()->isAdmin()) {
            $this->toast('Seuls les administrateurs peuvent terminer un inventaire.', 'error');
            return;
        }

        if ($this->localisationsNonTerminees > 0) {
            $this->toast(
                "Toutes les localisations doivent être terminées avant de terminer l'inventaire. {$this->localisationsNonTerminees} restante(s). Utilisez \"Forcer la fin\" pour marquer les biens restants absents.",
                'error'
            );
            return;
        }

        try {
            $this->inventaire->update([
                'statut' => 'termine',
                'date_fin' => now(),
            ]);
            $this->inventaire->refresh();
            $this->toast("L'inventaire {$this->inventaire->annee} a été terminé avec succès.", 'success');
        } catch (\Exception $e) {
            $this->toast('Erreur lors de la finalisation: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Termine l'inventaire de force :
     *  - marque tous les biens non scannés des localisations restantes comme "absent"
     *  - met ces localisations en statut "termine"
     *  - puis termine l'inventaire
     */
    public function terminerInventaireForce(): void
    {
        if (!Auth::user()->isAdmin()) {
            $this->toast('Seuls les administrateurs peuvent terminer un inventaire.', 'error');
            return;
        }

        if (!in_array($this->inventaire->statut, ['en_preparation', 'en_cours'])) {
            $this->toast('Seuls les inventaires en cours peuvent être terminés.', 'error');
            return;
        }

        try {
            $resume = DB::transaction(function () {
                $biensMarquesAbsents = 0;
                $locationsForcees = 0;

                $invLocsRestantes = $this->inventaire->inventaireLocalisations()
                    ->where('statut', '!=', 'termine')
                    ->get();

                foreach ($invLocsRestantes as $invLoc) {
                    // Emplacements de cette localisation
                    $emplacementIds = Emplacement::where('idLocalisation', $invLoc->localisation_id)
                        ->pluck('idEmplacement')
                        ->toArray();

                    if (!empty($emplacementIds)) {
                        // Biens déjà scannés pour cette localisation (tous statuts confondus)
                        $biensDejaScannes = InventaireScan::where('inventaire_localisation_id', $invLoc->id)
                            ->pluck('bien_id')
                            ->toArray();

                        // Biens attendus mais non scannés -> à marquer absents
                        $biensNonScannes = Gesimmo::whereIn('idEmplacement', $emplacementIds)
                            ->whereNotIn('NumOrdre', $biensDejaScannes)
                            ->pluck('NumOrdre');

                        $now = now();
                        foreach ($biensNonScannes as $numOrdre) {
                            InventaireScan::create([
                                'inventaire_id' => $this->inventaire->id,
                                'inventaire_localisation_id' => $invLoc->id,
                                'bien_id' => $numOrdre,
                                'date_scan' => $now,
                                'statut_scan' => 'absent',
                                'etat_constate' => 'bon',
                                'user_id' => Auth::id(),
                                'commentaire' => 'Marqué absent lors de la fin forcée de l\'inventaire.',
                            ]);
                            $biensMarquesAbsents++;
                        }
                    }

                    // Mettre à jour le compteur et le statut de la localisation
                    $nbScannes = InventaireScan::where('inventaire_localisation_id', $invLoc->id)->count();
                    $invLoc->update([
                        'statut' => 'termine',
                        'nombre_biens_scannes' => $nbScannes,
                        'date_fin_scan' => now(),
                    ]);
                    $locationsForcees++;
                }

                $this->inventaire->update([
                    'statut' => 'termine',
                    'date_fin' => now(),
                ]);

                return compact('biensMarquesAbsents', 'locationsForcees');
            });

            $this->inventaire->refresh();
            $this->toast(
                "Inventaire {$this->inventaire->annee} terminé. {$resume['locationsForcees']} localisation(s) forcée(s), {$resume['biensMarquesAbsents']} bien(s) marqué(s) absent(s).",
                'success'
            );
        } catch (\Exception $e) {
            $this->toast('Erreur lors de la fin forcée: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Clôture définitivement l'inventaire
     */
    public function cloturerInventaire()
    {
        if (!Auth::user()->isAdmin()) {
            $this->toast('Seuls les administrateurs peuvent clôturer un inventaire.', 'error');
            return;
        }

        if ($this->inventaire->statut !== 'termine') {
            $this->toast('Seuls les inventaires terminés peuvent être clôturés.', 'error');
            return;
        }

        try {
            $this->inventaire->cloturer();
            session()->flash('success', "L'inventaire {$this->inventaire->annee} a été clôturé définitivement.");
            return redirect()->route('inventaires.rapport', $this->inventaire);
        } catch (\Exception $e) {
            $this->toast('Erreur lors de la clôture: ' . $e->getMessage(), 'error');
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
            $this->toast('Localisation réassignée avec succès.', 'success');
        } catch (\Exception $e) {
            $this->toast('Erreur lors de la réassignation: ' . $e->getMessage(), 'error');
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


