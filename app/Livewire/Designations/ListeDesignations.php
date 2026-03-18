<?php

namespace App\Livewire\Designations;

use App\Models\Designation;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\CorbeilleImmobilisation;
use App\Models\Gesimmo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]

class ListeDesignations extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et la recherche
     */
    public $search = '';
    public $filterCategorie = '';
    public $sortField = 'designation';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $selectedDesignations = [];
    public $bulkDesignationIds = '';
    public $bulkFeedbackType = null;
    public $bulkFeedbackMessage = null;

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $this->resetPage();
    }

    /**
     * Propriété calculée : Retourne toutes les catégories pour le filtre
     */
    public function getCategoriesProperty()
    {
        return Categorie::orderBy('Categorie')->get();
    }

    /**
     * Construit la requête de base pour les désignations
     */
    protected function getDesignationsQuery()
    {
        $query = Designation::with(['categorie'])
            ->withCount('immobilisations');

        // Recherche globale
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('designation', 'like', '%' . $this->search . '%')
                  ->orWhere('CodeDesignation', 'like', '%' . $this->search . '%')
                  ->orWhereHas('categorie', function($q) {
                      $q->where('Categorie', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtre par catégorie
        if (!empty($this->filterCategorie)) {
            $query->where('idCat', $this->filterCategorie);
        }

        // Tri
        if ($this->sortField === 'categorie') {
            $query->join('categorie', 'designation.idCat', '=', 'categorie.idCategorie')
                  ->orderBy('categorie.Categorie', $this->sortDirection)
                  ->select('designation.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query;
    }

    /**
     * Change le champ de tri
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Réinitialise les filtres
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->filterCategorie = '';
        $this->resetPage();
    }

    /**
     * Supprime une désignation
     */
    public function deleteDesignation($designationId)
    {
        $designation = Designation::find($designationId);
        
        if (!$designation) {
            session()->flash('error', 'Désignation introuvable.');
            return;
        }

        $movedToTrash = 0;

        try {
            DB::transaction(function () use ($designation, &$movedToTrash) {
                $immos = $designation->immobilisations()
                    ->with(['code', 'emplacement.localisation', 'emplacement.affectation'])
                    ->get();

                foreach ($immos as $immo) {
                    $dateAcquisitionCorbeille = null;
                    if (!empty($immo->DateAcquisition)) {
                        $year = (int) $immo->DateAcquisition;
                        if ($year >= 1900 && $year <= 9999) {
                            $dateAcquisitionCorbeille = sprintf('%04d-01-01', $year);
                        }
                    }

                    CorbeilleImmobilisation::create([
                        'original_num_ordre' => $immo->NumOrdre,
                        'idDesignation' => $immo->idDesignation,
                        'idCategorie' => $immo->idCategorie,
                        'idEtat' => $immo->idEtat,
                        'idEmplacement' => $immo->idEmplacement,
                        'idNatJur' => $immo->idNatJur,
                        'idSF' => $immo->idSF,
                        'DateAcquisition' => $dateAcquisitionCorbeille,
                        'Observations' => $immo->Observations,
                        'barcode' => $immo->code?->barcode,
                        'emplacement_label' => $immo->emplacement?->Emplacement,
                        'emplacement_code' => $immo->emplacement?->CodeEmplacement,
                        'emplacement_id_affectation' => $immo->emplacement?->idAffectation,
                        'emplacement_id_localisation' => $immo->emplacement?->idLocalisation,
                        'affectation_label' => $immo->emplacement?->affectation?->Affectation,
                        'localisation_label' => $immo->emplacement?->localisation?->Localisation,
                        'designation_label' => $designation->designation,
                        'deleted_reason' => 'Suppression de designation',
                        'deleted_by_user_id' => auth()->id(),
                        'deleted_at' => now(),
                    ]);
                    $movedToTrash++;
                }

                // Supprimer d'abord les codes-barres liés pour respecter la contrainte FK codes -> gesimmo
                if ($immos->isNotEmpty()) {
                    Code::whereIn('idGesimmo', $immos->pluck('NumOrdre'))->delete();
                }

                // Supprimer ensuite les immobilisations de la table principale
                $designation->immobilisations()->delete();

                // Enfin, supprimer la designation
                $designation->delete();
            });

            if ($movedToTrash > 0) {
                session()->flash('success', "Designation supprimee. {$movedToTrash} immobilisation(s) deplacee(s) dans la corbeille.");
            } else {
                session()->flash('success', 'Designation supprimee avec succes.');
            }
        } catch (\Throwable $e) {
            session()->flash('error', "Suppression impossible: {$e->getMessage()}");
        }
    }

    /**
     * Déplace en corbeille toutes les immobilisations liées
     * aux idDesignation saisis (sans supprimer les désignations).
     *
     * Approche batch : collecte des IDs en amont, INSERT/DELETE par
     * lots de 200 dans des transactions ultra-courtes, retry automatique.
     */
    public function moveImmosToTrashByDesignationIds(): void
    {
        set_time_limit(300);

        $this->bulkFeedbackType = null;
        $this->bulkFeedbackMessage = null;

        $raw = trim((string) $this->bulkDesignationIds);
        if ($raw === '') {
            $this->setBulkFeedback('error', 'Veuillez renseigner au moins un idDesignation.');
            return;
        }

        $tokens = preg_split('/[\s,;]+/', $raw) ?: [];
        $designationIds = collect($tokens)
            ->filter(fn ($v) => is_numeric($v) && (int) $v > 0)
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        if (empty($designationIds)) {
            $this->setBulkFeedback('error', 'Aucun idDesignation valide detecte.');
            return;
        }

        $existingDesignationIds = Designation::whereIn('id', $designationIds)
            ->pluck('id')->map(fn ($id) => (int) $id)->all();
        $missingDesignationIds = array_values(array_diff($designationIds, $existingDesignationIds));

        // --- Étape 1 : collecter TOUS les NumOrdre cibles ---
        $allNumOrdres = DB::table('gesimmo')
            ->whereIn('idDesignation', $designationIds)
            ->pluck('NumOrdre')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (empty($allNumOrdres)) {
            $trashCount = DB::table('corbeille_immobilisations')
                ->whereIn('idDesignation', $designationIds)->count();
            $msg = 'Aucune immobilisation trouvee dans gesimmo pour ces idDesignation.';
            if ($trashCount > 0) {
                $msg .= " {$trashCount} sont deja en corbeille.";
            }
            if (!empty($missingDesignationIds)) {
                $msg .= ' IDs introuvables: ' . implode(', ', $missingDesignationIds) . '.';
            }
            $this->setBulkFeedback('error', $msg);
            return;
        }

        // --- Étape 2 : exclure ceux déjà en corbeille ---
        $alreadyInCorbeille = DB::table('corbeille_immobilisations')
            ->whereIn('original_num_ordre', $allNumOrdres)
            ->pluck('original_num_ordre')
            ->map(fn ($v) => (int) $v)
            ->all();

        $toProcess = array_values(array_diff($allNumOrdres, $alreadyInCorbeille));
        $skippedAlreadyInTrash = count($allNumOrdres) - count($toProcess);

        if (empty($toProcess)) {
            $this->setBulkFeedback('error',
                "Les {$skippedAlreadyInTrash} immobilisation(s) de cette selection sont deja en corbeille.");
            return;
        }

        // --- Étape 3 : lire les données enrichies (hors verrou) ---
        $moved = 0;
        $skippedRows = 0;
        $failedRows = 0;
        $lastError = '';
        $batchReason = 'Suppression en lot par idDesignation depuis /designations';
        $now = now();
        $userId = auth()->id();

        try {
            $allRows = collect();
            foreach (array_chunk($toProcess, 500) as $chunk) {
                $allRows = $allRows->merge($this->fetchEnrichedRows($chunk));
            }

            // --- Étape 4 : traitement ligne par ligne en autocommit ---
            foreach ($allRows as $row) {
                $numOrdre = (int) $row->NumOrdre;

                $alreadyExists = DB::table('corbeille_immobilisations')
                    ->where('original_num_ordre', $numOrdre)->exists();
                if ($alreadyExists) {
                    DB::table('codes')->where('idGesimmo', $numOrdre)->delete();
                    DB::table('gesimmo')->where('NumOrdre', $numOrdre)->delete();
                    $skippedRows++;
                    continue;
                }

                $dateAcq = null;
                if (!empty($row->DateAcquisition)) {
                    $year = (int) $row->DateAcquisition;
                    if ($year >= 1900 && $year <= 9999) {
                        $dateAcq = sprintf('%04d-01-01', $year);
                    }
                }

                $payload = [
                    'original_num_ordre' => $numOrdre,
                    'idDesignation'      => $row->idDesignation,
                    'idCategorie'        => $row->idCategorie,
                    'idEtat'             => $row->idEtat,
                    'idEmplacement'      => $row->idEmplacement,
                    'idNatJur'           => $row->idNatJur,
                    'idSF'               => $row->idSF,
                    'DateAcquisition'    => $dateAcq,
                    'Observations'       => $row->Observations,
                    'barcode'            => $row->barcode,
                    'emplacement_label'  => $row->emplacement_label,
                    'emplacement_code'   => $row->emplacement_code,
                    'emplacement_id_affectation'   => $row->emplacement_id_affectation,
                    'emplacement_id_localisation'  => $row->emplacement_id_localisation,
                    'affectation_label'  => $row->affectation_label,
                    'localisation_label' => $row->localisation_label,
                    'designation_label'  => $row->designation_label,
                    'deleted_reason'     => $batchReason,
                    'deleted_by_user_id' => $userId,
                    'deleted_at'         => $now,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                $ok = false;
                for ($attempt = 1; $attempt <= 3; $attempt++) {
                    try {
                        DB::table('corbeille_immobilisations')->insert($payload);
                        DB::table('codes')->where('idGesimmo', $numOrdre)->delete();
                        DB::table('gesimmo')->where('NumOrdre', $numOrdre)->delete();
                        $ok = true;
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        $code = (int) ($e->errorInfo[1] ?? 0);
                        if (in_array($code, [1205, 1213], true) && $attempt < 3) {
                            sleep($attempt);
                            continue;
                        }
                        $lastError = $e->getMessage();
                        \Log::warning("Bulk trash row error", ['numOrdre' => $numOrdre, 'error' => $lastError]);
                        break;
                    }
                }

                if ($ok) {
                    $moved++;
                } else {
                    $failedRows++;
                }
            }
        } catch (\Throwable $e) {
            $this->setBulkFeedback('error', "Erreur: {$e->getMessage()}");
            return;
        }

        $message = "Traitement termine: {$moved} deplacee(s) en corbeille";
        if ($skippedAlreadyInTrash > 0 || $skippedRows > 0) {
            $total = $skippedAlreadyInTrash + $skippedRows;
            $message .= ", {$total} ignoree(s) (deja en corbeille)";
        }
        if (!empty($missingDesignationIds)) {
            $message .= ', ' . count($missingDesignationIds) . ' idDesignation introuvable(s)';
        }
        if ($failedRows > 0) {
            $message .= ", {$failedRows} echec(s): {$lastError}";
        }
        $message .= '.';

        $this->setBulkFeedback($moved > 0 ? 'success' : 'error', $message);
        $this->bulkDesignationIds = '';
        $this->resetPage();
    }

    /**
     * Lit les données enrichies (labels) pour un lot de NumOrdre.
     * Exécuté HORS transaction pour minimiser les verrous.
     */
    private function fetchEnrichedRows(array $numOrdres)
    {
        return DB::table('gesimmo as g')
            ->leftJoinSub(
                DB::table('codes')
                    ->select('idGesimmo', DB::raw('MAX(barcode) as barcode'))
                    ->groupBy('idGesimmo'),
                'c',
                fn ($join) => $join->on('c.idGesimmo', '=', 'g.NumOrdre')
            )
            ->leftJoin('designation as d', 'd.id', '=', 'g.idDesignation')
            ->leftJoin('emplacement as e', 'e.idEmplacement', '=', 'g.idEmplacement')
            ->leftJoin('affectation as a', 'a.idAffectation', '=', 'e.idAffectation')
            ->leftJoin('localisation as l', 'l.idLocalisation', '=', 'e.idLocalisation')
            ->select([
                'g.NumOrdre', 'g.idDesignation', 'g.idCategorie', 'g.idEtat',
                'g.idEmplacement', 'g.idNatJur', 'g.idSF', 'g.DateAcquisition',
                'g.Observations', 'c.barcode',
                'e.Emplacement as emplacement_label',
                'e.CodeEmplacement as emplacement_code',
                'e.idAffectation as emplacement_id_affectation',
                'e.idLocalisation as emplacement_id_localisation',
                'a.Affectation as affectation_label',
                'l.Localisation as localisation_label',
                'd.designation as designation_label',
            ])
            ->whereIn('g.NumOrdre', $numOrdres)
            ->get();
    }

    private function setBulkFeedback(string $type, string $message): void
    {
        $this->bulkFeedbackType = $type;
        $this->bulkFeedbackMessage = $message;
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        $designations = $this->getDesignationsQuery()->paginate($this->perPage);
        $totalDesignations = Designation::count();

        return view('livewire.designations.liste-designations', [
            'designations' => $designations,
            'totalDesignations' => $totalDesignations,
        ]);
    }
}
