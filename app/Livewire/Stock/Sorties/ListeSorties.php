<?php

namespace App\Livewire\Stock\Sorties;

use App\Models\StockSortie;
use App\Models\StockProduit;
use App\Models\StockDemandeur;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ListeSorties extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProduit = '';
    public $filterDemandeur = '';
    public $dateDebut = '';
    public $dateFin = '';

    protected $queryString = ['search', 'filterProduit', 'filterDemandeur', 'dateDebut', 'dateFin'];

    public function mount()
    {
        if (empty($this->dateDebut)) {
            $this->dateDebut = now()->subMonth()->format('Y-m-d');
        }
        if (empty($this->dateFin)) {
            $this->dateFin = now()->format('Y-m-d');
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProduit() { $this->resetPage(); }
    public function updatingFilterDemandeur() { $this->resetPage(); }
    public function updatingDateDebut() { $this->resetPage(); }
    public function updatingDateFin() { $this->resetPage(); }

    public function supprimerCommande(string $groupeId): void
    {
        if (!auth()->user()->canDeleteStockOperations()) {
            abort(403);
        }

        // Supprimer toutes les sorties du groupe (les events deleting remettent le stock)
        StockSortie::where('groupe_id', $groupeId)->each(fn($s) => $s->delete());

        session()->flash('success', 'Commande supprimée. Les stocks ont été rétablis.');
    }

    public function supprimerSortie(int $id): void
    {
        if (!auth()->user()->canDeleteStockOperations()) {
            abort(403);
        }

        StockSortie::findOrFail($id)->delete();
        session()->flash('success', 'Article supprimé. Le stock a été rétabli.');
    }

    public function render()
    {
        $user = auth()->user();

        // Base query pour filtres
        $baseQuery = StockSortie::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('produit', fn($p) => $p->where('libelle', 'like', '%' . $this->search . '%'))
                       ->orWhereHas('demandeur', fn($d) => $d->where('nom', 'like', '%' . $this->search . '%'))
                       ->orWhere('observations', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterProduit, fn($q) => $q->where('produit_id', $this->filterProduit))
            ->when($this->filterDemandeur, fn($q) => $q->where('demandeur_id', $this->filterDemandeur))
            ->when($this->dateDebut, fn($q) => $q->where('date_sortie', '>=', $this->dateDebut))
            ->when($this->dateFin, fn($q) => $q->where('date_sortie', '<=', $this->dateFin))
            ->when($user && !$user->canViewAllMovements(), fn($q) => $q->where('created_by', $user->idUser));

        // Total quantité pour KPI
        $totalQuantite = (clone $baseQuery)->sum('quantite');

        // Groupes distincts paginés (groupe_id ou id pour les anciennes sorties sans groupe)
        $groupeIds = (clone $baseQuery)
            ->select(DB::raw('COALESCE(groupe_id, CAST(id AS CHAR)) as groupe_key'), DB::raw('MAX(date_sortie) as max_date'))
            ->groupBy(DB::raw('COALESCE(groupe_id, CAST(id AS CHAR))'))
            ->orderBy('max_date', 'desc')
            ->paginate(15, ['*'], 'page');

        // Charger toutes les sorties de ces groupes en une requête
        $groupeKeysList = $groupeIds->pluck('groupe_key')->toArray();

        $toutesLesSorties = StockSortie::with(['produit.categorie', 'demandeur', 'createur'])
            ->where(function ($q) use ($groupeKeysList) {
                $q->whereIn('groupe_id', $groupeKeysList)
                  ->orWhereIn('id', array_filter($groupeKeysList, fn($k) => !str_contains($k, '-')));
            })
            ->get()
            ->groupBy(fn($s) => $s->groupe_id ?? (string) $s->id);

        $produits   = StockProduit::orderBy('libelle')->get();
        $demandeurs = StockDemandeur::orderBy('nom')->get();

        return view('livewire.stock.sorties.liste-sorties', [
            'groupeIds'       => $groupeIds,
            'toutesLesSorties'=> $toutesLesSorties,
            'produits'        => $produits,
            'demandeurs'      => $demandeurs,
            'totalQuantite'   => $totalQuantite,
        ]);
    }
}
