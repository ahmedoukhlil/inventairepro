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
     */
    public function moveImmosToTrashByDesignationIds(): void
    {
        $this->bulkFeedbackType = null;
        $this->bulkFeedbackMessage = null;

        $raw = trim((string) $this->bulkDesignationIds);
        if ($raw === '') {
            $this->setBulkFeedback('error', 'Veuillez renseigner au moins un idDesignation.');
            return;
        }

        $tokens = preg_split('/[\s,;]+/', $raw) ?: [];
        $designationIds = collect($tokens)
            ->filter(fn ($value) => is_numeric($value) && (int) $value > 0)
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        if (empty($designationIds)) {
            $this->setBulkFeedback('error', 'Aucun idDesignation valide detecte.');
            return;
        }

        $existingDesignationIds = Designation::query()
            ->whereIn('id', $designationIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $missingDesignationIds = array_values(array_diff($designationIds, $existingDesignationIds));

        $totalInMain = Gesimmo::query()
            ->whereIn('idDesignation', $designationIds)
            ->count();

        $totalInTrash = CorbeilleImmobilisation::query()
            ->whereIn('idDesignation', $designationIds)
            ->count();

        if ($totalInMain === 0) {
            $msg = "Aucune immobilisation a deplacer dans gesimmo pour ces idDesignation.";
            if ($totalInTrash > 0) {
                $msg .= " {$totalInTrash} immobilisation(s) sont deja en corbeille pour cette selection.";
            }
            if (!empty($missingDesignationIds)) {
                $msg .= ' IDs introuvables: ' . implode(', ', $missingDesignationIds) . '.';
            }
            $this->setBulkFeedback('error', $msg);
            return;
        }

        $moved = 0;
        $skippedAlreadyInTrash = 0;
        $deletedFromMain = 0;
        $batchReason = 'Suppression en lot par idDesignation depuis /designations';
        $now = now();

        try {
            Gesimmo::query()
                ->select('NumOrdre')
                ->whereIn('idDesignation', $designationIds)
                ->orderBy('NumOrdre')
                ->chunkById(100, function ($chunk) use (
                    &$moved,
                    &$skippedAlreadyInTrash,
                    &$deletedFromMain,
                    $batchReason,
                    $now
                ): void {
                    $numOrdresChunk = $chunk->pluck('NumOrdre')->map(fn ($v) => (int) $v)->values()->all();
                    if (empty($numOrdresChunk)) {
                        return;
                    }

                    $this->runBulkChunkWithRetry(function () use (
                        $numOrdresChunk,
                        &$moved,
                        &$skippedAlreadyInTrash,
                        &$deletedFromMain,
                        $batchReason,
                        $now
                    ): void {
                        $rows = DB::table('gesimmo as g')
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
                            ->leftJoin('corbeille_immobilisations as ci', 'ci.original_num_ordre', '=', 'g.NumOrdre')
                            ->whereIn('g.NumOrdre', $numOrdresChunk)
                            ->whereNull('ci.original_num_ordre')
                            ->select([
                                'g.NumOrdre as original_num_ordre',
                                'g.idDesignation',
                                'g.idCategorie',
                                'g.idEtat',
                                'g.idEmplacement',
                                'g.idNatJur',
                                'g.idSF',
                                'g.DateAcquisition',
                                'g.Observations',
                                'c.barcode',
                                'e.Emplacement as emplacement_label',
                                'e.CodeEmplacement as emplacement_code',
                                'e.idAffectation as emplacement_id_affectation',
                                'e.idLocalisation as emplacement_id_localisation',
                                'a.Affectation as affectation_label',
                                'l.Localisation as localisation_label',
                                'd.designation as designation_label',
                            ])
                            ->get();

                        if ($rows->isEmpty()) {
                            $skippedAlreadyInTrash += count($numOrdresChunk);
                            return;
                        }

                        $toInsert = [];
                        $processedNumOrdres = [];

                        foreach ($rows as $row) {
                            $dateAcquisitionCorbeille = null;
                            if (!empty($row->DateAcquisition)) {
                                $year = (int) $row->DateAcquisition;
                                if ($year >= 1900 && $year <= 9999) {
                                    $dateAcquisitionCorbeille = sprintf('%04d-01-01', $year);
                                }
                            }

                            $toInsert[] = [
                                'original_num_ordre' => $row->original_num_ordre,
                                'idDesignation' => $row->idDesignation,
                                'idCategorie' => $row->idCategorie,
                                'idEtat' => $row->idEtat,
                                'idEmplacement' => $row->idEmplacement,
                                'idNatJur' => $row->idNatJur,
                                'idSF' => $row->idSF,
                                'DateAcquisition' => $dateAcquisitionCorbeille,
                                'Observations' => $row->Observations,
                                'barcode' => $row->barcode,
                                'emplacement_label' => $row->emplacement_label,
                                'emplacement_code' => $row->emplacement_code,
                                'emplacement_id_affectation' => $row->emplacement_id_affectation,
                                'emplacement_id_localisation' => $row->emplacement_id_localisation,
                                'affectation_label' => $row->affectation_label,
                                'localisation_label' => $row->localisation_label,
                                'designation_label' => $row->designation_label,
                                'deleted_reason' => $batchReason,
                                'deleted_by_user_id' => auth()->id(),
                                'deleted_at' => $now,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];

                            $processedNumOrdres[] = (int) $row->original_num_ordre;
                        }

                        if (!empty($toInsert)) {
                            DB::table('corbeille_immobilisations')->insert($toInsert);
                            DB::table('codes')->whereIn('idGesimmo', $processedNumOrdres)->delete();
                            $deletedFromMain += DB::table('gesimmo')->whereIn('NumOrdre', $processedNumOrdres)->delete();
                            $moved += count($processedNumOrdres);
                        }

                        $skippedAlreadyInTrash += max(0, count($numOrdresChunk) - count($processedNumOrdres));
                    });
                }, 'NumOrdre', 'NumOrdre');
        } catch (\Throwable $e) {
            $this->setBulkFeedback('error', "Operation impossible: {$e->getMessage()}");
            return;
        }

        $missingCount = count($missingDesignationIds);
        $message = "Traitement termine: {$moved} immobilisation(s) envoyee(s) en corbeille, {$deletedFromMain} supprimee(s) de gesimmo";

        if ($skippedAlreadyInTrash > 0) {
            $message .= ", {$skippedAlreadyInTrash} ignoree(s) (deja en corbeille)";
        }

        if ($missingCount > 0) {
            $message .= ", {$missingCount} idDesignation introuvable(s)";
        }

        $message .= '.';

        if ($moved === 0) {
            $this->setBulkFeedback('error', $message);
        } else {
            $this->setBulkFeedback('success', $message);
        }
        $this->bulkDesignationIds = '';
        $this->resetPage();
    }

    private function setBulkFeedback(string $type, string $message): void
    {
        $this->bulkFeedbackType = $type;
        $this->bulkFeedbackMessage = $message;
        session()->flash($type, $message);
    }

    private function runBulkChunkWithRetry(callable $callback, int $maxAttempts = 4): void
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                DB::transaction($callback, 1);
                return;
            } catch (\Illuminate\Database\QueryException $e) {
                $mysqlErrorCode = (int) ($e->errorInfo[1] ?? 0);
                $isLockError = in_array($mysqlErrorCode, [1205, 1213], true);

                if (!$isLockError || $attempt >= $maxAttempts) {
                    throw $e;
                }

                usleep(200000 * $attempt);
            }
        }
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
