<?php

namespace App\Livewire\Stock\Entrees;

use App\Models\StockEntree;
use App\Models\StockProduit;
use App\Models\StockFournisseur;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ListeEntrees extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProduit = '';
    public $filterFournisseur = '';
    public $dateDebut = '';
    public $dateFin = '';

    protected $queryString = ['search', 'filterProduit', 'filterFournisseur', 'dateDebut', 'dateFin'];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->canCreateEntree()) {
            abort(403, 'Accès non autorisé.');
        }

        if (empty($this->dateDebut)) {
            $this->dateDebut = now()->subMonth()->format('Y-m-d');
        }
        if (empty($this->dateFin)) {
            $this->dateFin = now()->format('Y-m-d');
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProduit() { $this->resetPage(); }
    public function updatingFilterFournisseur() { $this->resetPage(); }
    public function updatingDateDebut() { $this->resetPage(); }
    public function updatingDateFin() { $this->resetPage(); }

    public function supprimerGroupe(string $groupeId): void
    {
        if (!auth()->user()->canDeleteStockOperations()) {
            abort(403);
        }

        StockEntree::where('groupe_id', $groupeId)->each(fn($e) => $e->delete());

        session()->flash('success', 'Entrée supprimée. Les stocks ont été ajustés.');
    }

    public function supprimerEntree(int $id): void
    {
        if (!auth()->user()->canDeleteStockOperations()) {
            abort(403);
        }

        StockEntree::findOrFail($id)->delete();
        session()->flash('success', 'Article supprimé. Le stock a été ajusté.');
    }

    public function render()
    {
        $baseQuery = StockEntree::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('produit', fn($p) => $p->where('libelle', 'like', '%' . $this->search . '%'))
                       ->orWhereHas('fournisseur', fn($f) => $f->where('libelle', 'like', '%' . $this->search . '%'))
                       ->orWhere('reference_commande', 'like', '%' . $this->search . '%')
                       ->orWhere('observations', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterProduit, fn($q) => $q->where('produit_id', $this->filterProduit))
            ->when($this->filterFournisseur, fn($q) => $q->where('fournisseur_id', $this->filterFournisseur))
            ->when($this->dateDebut, fn($q) => $q->where('date_entree', '>=', $this->dateDebut))
            ->when($this->dateFin, fn($q) => $q->where('date_entree', '<=', $this->dateFin));

        $totalQuantite = (clone $baseQuery)->sum('quantite');

        $groupeIds = (clone $baseQuery)
            ->select(DB::raw('COALESCE(groupe_id, CAST(id AS CHAR)) as groupe_key'), DB::raw('MAX(date_entree) as max_date'))
            ->groupBy(DB::raw('COALESCE(groupe_id, CAST(id AS CHAR))'))
            ->orderBy('max_date', 'desc')
            ->paginate(15, ['*'], 'page');

        $groupeKeysList = $groupeIds->pluck('groupe_key')->toArray();

        $toutesLesEntrees = StockEntree::with(['produit.categorie', 'fournisseur', 'createur'])
            ->where(function ($q) use ($groupeKeysList) {
                $q->whereIn('groupe_id', $groupeKeysList)
                  ->orWhereIn('id', array_filter($groupeKeysList, fn($k) => !str_contains($k, '-')));
            })
            ->get()
            ->groupBy(fn($e) => $e->groupe_id ?? (string) $e->id);

        $produits     = StockProduit::orderBy('libelle')->get();
        $fournisseurs = StockFournisseur::orderBy('libelle')->get();

        return view('livewire.stock.entrees.liste-entrees', [
            'groupeIds'        => $groupeIds,
            'toutesLesEntrees' => $toutesLesEntrees,
            'produits'         => $produits,
            'fournisseurs'     => $fournisseurs,
            'totalQuantite'    => $totalQuantite,
            'dateDebut'        => $this->dateDebut,
            'dateFin'          => $this->dateFin,
        ]);
    }
}