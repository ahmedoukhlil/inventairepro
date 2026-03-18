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
        $raw = trim((string) $this->bulkDesignationIds);
        if ($raw === '') {
            session()->flash('error', 'Veuillez renseigner au moins un idDesignation.');
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
            session()->flash('error', 'Aucun idDesignation valide detecte.');
            return;
        }

        $existingDesignationIds = Designation::query()
            ->whereIn('id', $designationIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $missingDesignationIds = array_values(array_diff($designationIds, $existingDesignationIds));

        $moved = 0;
        $skippedAlreadyInTrash = 0;
        $deletedFromMain = 0;
        $batchReason = 'Suppression en lot par idDesignation depuis /designations';

        try {
            DB::transaction(function () use (
                $designationIds,
                &$moved,
                &$skippedAlreadyInTrash,
                &$deletedFromMain,
                $batchReason
            ): void {
                $numOrdresToDelete = [];

                Gesimmo::query()
                    ->whereIn('idDesignation', $designationIds)
                    ->orderBy('NumOrdre')
                    ->chunkById(200, function ($chunk) use (
                        &$moved,
                        &$skippedAlreadyInTrash,
                        &$numOrdresToDelete,
                        $batchReason
                    ): void {
                        $chunk->load(['designation', 'code', 'emplacement.localisation', 'emplacement.affectation']);

                        foreach ($chunk as $immo) {
                            $alreadyInTrash = CorbeilleImmobilisation::query()
                                ->where('original_num_ordre', $immo->NumOrdre)
                                ->exists();

                            if ($alreadyInTrash) {
                                $skippedAlreadyInTrash++;
                                continue;
                            }

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
                                'designation_label' => $immo->designation?->designation,
                                'deleted_reason' => $batchReason,
                                'deleted_by_user_id' => auth()->id(),
                                'deleted_at' => now(),
                            ]);

                            $numOrdresToDelete[] = (int) $immo->NumOrdre;
                            $moved++;
                        }
                    }, 'NumOrdre', 'NumOrdre');

                if (!empty($numOrdresToDelete)) {
                    $numOrdresToDelete = array_values(array_unique($numOrdresToDelete));
                    Code::query()->whereIn('idGesimmo', $numOrdresToDelete)->delete();
                    $deletedFromMain = Gesimmo::query()->whereIn('NumOrdre', $numOrdresToDelete)->delete();
                }
            });
        } catch (\Throwable $e) {
            session()->flash('error', "Operation impossible: {$e->getMessage()}");
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

        session()->flash('success', $message);
        $this->bulkDesignationIds = '';
        $this->resetPage();
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
