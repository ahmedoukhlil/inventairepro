<?php

namespace App\Livewire\Emplacements;

use App\Models\Emplacement;
use App\Models\LocalisationImmo;
use App\Models\Affectation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
    public $filterImmobilisationsCount = '';
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
        $this->filterImmobilisationsCount = '';
        $this->selectedEmplacements = [];
        $this->resetPage();
    }

    /**
     * Supprime un emplacement
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

        // Vérifier qu'aucune immobilisation n'est associée à cet emplacement
        $nombreImmobilisations = $emplacement->immobilisations()->count();
        
        if ($nombreImmobilisations > 0) {
            session()->flash('error', "Impossible de supprimer cet emplacement : {$nombreImmobilisations} immobilisation(s) y sont associée(s).");
            return;
        }

        try {
            $emplacement->delete();
            Cache::forget('emplacements_total_count');
            session()->flash('success', 'L\'emplacement a été supprimé avec succès.');
            
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
        $query = Emplacement::with(['localisation', 'affectation', 'immobilisations'])
            ->withCount('immobilisations');

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

        // Filtre par nombre exact d'immobilisations (y compris 0)
        // Utiliser HAVING car immoblilisations_count est un alias de withCount()
        if ($this->filterImmobilisationsCount !== '' && $this->filterImmobilisationsCount !== null) {
            $query->having('immobilisations_count', '=', (int) $this->filterImmobilisationsCount);
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

    /**
     * Exporter la liste (filtrée) des emplacements en Excel (CSV)
     */
    public function exportExcel()
    {
        try {
            // Utiliser la même requête que pour le tableau, mais sans pagination
            $emplacements = $this->getEmplacementsQuery()->get();

            if ($emplacements->isEmpty()) {
                session()->flash('warning', 'Aucun emplacement à exporter.');
                return;
            }

            $filename = 'emplacements_' . now()->format('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($emplacements) {
                $file = fopen('php://output', 'w');

                // BOM UTF-8 pour Excel
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // En-têtes de colonnes
                fputcsv($file, [
                    'ID Emplacement',
                    'Emplacement',
                    'Code Emplacement',
                    'Localisation',
                    'Affectation',
                    'Nombre d\'immobilisations',
                ], ';');

                foreach ($emplacements as $emplacement) {
                    // Utiliser le compteur optimisé si disponible
                    $nbImmobilisations = $emplacement->immobilisations_count ?? ($emplacement->immobilisations ? $emplacement->immobilisations->count() : 0);

                    fputcsv($file, [
                        $emplacement->idEmplacement,
                        $emplacement->Emplacement,
                        $emplacement->CodeEmplacement ?? '',
                        $emplacement->localisation->Localisation ?? '',
                        $emplacement->affectation->Affectation ?? '',
                        $nbImmobilisations,
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'export des emplacements: ' . $e->getMessage());
        }
    }
}
