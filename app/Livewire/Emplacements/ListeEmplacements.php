<?php

namespace App\Livewire\Emplacements;

use App\Models\Emplacement;
use App\Models\LocalisationImmo;
use App\Models\Affectation;
use App\Models\Code;
use App\Models\CorbeilleImmobilisation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]

class ListeEmplacements extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et la recherche
     */
    public $search = '';
    public $filterLocalisation = '';
    public $filterAffectation = '';
    public $sortField = 'Emplacement';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $selectedEmplacements = [];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $this->resetPage();
    }

    /**
     * Propriété calculée : Retourne toutes les localisations
     */
    public function getLocalisationsProperty()
    {
        return LocalisationImmo::orderBy('Localisation')->get();
    }

    /**
     * Options pour SearchableSelect : Localisations
     */
    public function getLocalisationOptionsProperty()
    {
        $options = [[
            'value' => '',
            'text' => 'Toutes',
        ]];

        $localisations = LocalisationImmo::orderBy('Localisation')
            ->get()
            ->map(function ($localisation) {
                return [
                    'value' => (string)$localisation->idLocalisation,
                    'text' => ($localisation->CodeLocalisation ? $localisation->CodeLocalisation . ' - ' : '') . $localisation->Localisation,
                ];
            })
            ->toArray();

        return array_merge($options, $localisations);
    }

    /**
     * Propriété calculée : Retourne toutes les affectations
     */
    public function getAffectationsProperty()
    {
        return Affectation::orderBy('Affectation')->get();
    }

    /**
     * Options pour SearchableSelect : Affectations
     */
    public function getAffectationOptionsProperty()
    {
        $options = [[
            'value' => '',
            'text' => 'Toutes',
        ]];

        $affectations = Affectation::orderBy('Affectation')
            ->get()
            ->map(function ($affectation) {
                return [
                    'value' => (string)$affectation->idAffectation,
                    'text' => ($affectation->CodeAffectation ? $affectation->CodeAffectation . ' - ' : '') . $affectation->Affectation,
                ];
            })
            ->toArray();

        return array_merge($options, $affectations);
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
        $this->resetPage();
    }

    /**
     * Réinitialise tous les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterLocalisation = '';
        $this->filterAffectation = '';
        $this->selectedEmplacements = [];
        $this->resetPage();
    }

    /**
     * Vide un emplacement: deplace ses immobilisations vers la corbeille.
     * L'emplacement lui-meme n'est pas supprime.
     */
    public function deleteEmplacement($emplacementId): void
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer un emplacement.');
            return;
        }

        $emplacement = Emplacement::find($emplacementId);

        if (!$emplacement) {
            session()->flash('error', 'Emplacement introuvable.');
            return;
        }

        try {
            $nombreImmobilisations = 0;

            DB::transaction(function () use ($emplacement, &$nombreImmobilisations): void {
                $immobilisations = $emplacement->immobilisations()
                    ->with(['designation', 'code', 'emplacement.localisation', 'emplacement.affectation'])
                    ->get();

                foreach ($immobilisations as $immo) {
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
                        'emplacement_label' => $emplacement->Emplacement,
                        'emplacement_code' => $emplacement->CodeEmplacement,
                        'emplacement_id_affectation' => $emplacement->idAffectation,
                        'emplacement_id_localisation' => $emplacement->idLocalisation,
                        'affectation_label' => $emplacement->affectation?->Affectation,
                        'localisation_label' => $emplacement->localisation?->Localisation,
                        'designation_label' => $immo->designation?->designation,
                        'deleted_reason' => 'Suppression de emplacement',
                        'deleted_by_user_id' => auth()->id(),
                        'deleted_at' => now(),
                    ]);
                    $nombreImmobilisations++;
                }

                if ($immobilisations->isNotEmpty()) {
                    Code::whereIn('idGesimmo', $immobilisations->pluck('NumOrdre'))->delete();
                    $emplacement->immobilisations()->delete();
                }
            });

            if ($nombreImmobilisations > 0) {
                session()->flash('success', "{$nombreImmobilisations} immobilisation(s) deplacee(s) vers la corbeille. L'emplacement est conserve.");
            } else {
                session()->flash('warning', "Aucune immobilisation a supprimer pour cet emplacement. L'emplacement est conserve.");
            }
            
            $this->selectedEmplacements = array_diff($this->selectedEmplacements, [$emplacementId]);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Construit la requête de base pour les emplacements
     */
    protected function getEmplacementsQuery()
    {
        $query = Emplacement::with(['localisation', 'affectation', 'immobilisations']);

        // Recherche globale
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('Emplacement', 'like', '%' . $this->search . '%')
                    ->orWhere('CodeEmplacement', 'like', '%' . $this->search . '%');
            });
        }

        // Filtre par localisation
        if (!empty($this->filterLocalisation)) {
            $query->where('idLocalisation', $this->filterLocalisation);
        }

        // Filtre par affectation
        if (!empty($this->filterAffectation)) {
            $query->where('idAffectation', $this->filterAffectation);
        }

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        $emplacements = $this->getEmplacementsQuery()->paginate($this->perPage);

        // Statistiques pour le header (avec cache)
        $totalEmplacements = Cache::remember('emplacements_total_count', 300, function () {
            return Emplacement::count();
        });

        return view('livewire.emplacements.liste-emplacements', [
            'emplacements' => $emplacements,
            'totalEmplacements' => $totalEmplacements,
        ]);
    }
}
