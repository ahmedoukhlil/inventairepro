<?php

namespace App\Livewire\Stock\Produits;

use App\Models\StockProduit;
use App\Models\StockCategorie;
use App\Models\StockMagasin;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ListeProduits extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategorie = '';
    public $filterMagasin = '';
    public $filterStatut = ''; // alerte, faible, suffisant, tous
    public $confirmingDeletion = false;
    public $produitToDelete = null;

    protected $queryString = ['search', 'filterCategorie', 'filterMagasin', 'filterStatut'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategorie()
    {
        $this->resetPage();
    }

    public function updatingFilterMagasin()
    {
        $this->resetPage();
    }

    public function updatingFilterStatut()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $user = auth()->user();
        if (!$user || !$user->canManageStock()) {
            session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer des produits.');
            return;
        }

        $this->produitToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->produitToDelete = null;
    }

    public function delete()
    {
        $user = auth()->user();
        if (!$user || !$user->canManageStock()) {
            session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer des produits.');
            $this->cancelDelete();
            return;
        }

        $produit = StockProduit::find($this->produitToDelete);

        if ($produit) {
            $nomProduit = $produit->libelle;
            $quantiteActuelle = $produit->stock_actuel;
            $nombreEntrees = $produit->entrees()->count();
            $nombreSorties = $produit->sorties()->count();

            // Supprimer définitivement le produit et tous ses mouvements associés
            $produit->entrees()->delete();
            $produit->sorties()->delete();
            $produit->delete();

            $message = "Produit « $nomProduit » supprimé définitivement";
            if ($quantiteActuelle > 0) {
                $message .= " (quantité restante : $quantiteActuelle)";
            }
            if ($nombreEntrees > 0 || $nombreSorties > 0) {
                $message .= " — $nombreEntrees entrée(s) et $nombreSorties sortie(s) supprimée(s)";
            }
            $message .= ".";

            session()->flash('success', $message);
        }

        $this->cancelDelete();
    }

    public function render()
    {
        $produits = StockProduit::query()
            ->with(['categorie', 'magasin'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('libelle', 'like', '%' . $this->search . '%')
                      ->orWhere('descriptif', 'like', '%' . $this->search . '%')
                      ->orWhere('stockage', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategorie, function ($query) {
                $query->where('categorie_id', $this->filterCategorie);
            })
            ->when($this->filterMagasin, function ($query) {
                $query->where('magasin_id', $this->filterMagasin);
            })
            ->when($this->filterStatut, function ($query) {
                if ($this->filterStatut === 'alerte') {
                    $query->whereColumn('stock_actuel', '<=', 'seuil_alerte');
                } elseif ($this->filterStatut === 'faible') {
                    $query->whereColumn('stock_actuel', '>', 'seuil_alerte')
                          ->whereRaw('stock_actuel <= seuil_alerte * 1.5');
                } elseif ($this->filterStatut === 'suffisant') {
                    $query->whereRaw('stock_actuel > seuil_alerte * 1.5');
                }
            })
            ->orderBy('libelle')
            ->paginate(20);

        $categories = StockCategorie::orderBy('libelle')->get();
        $magasins = StockMagasin::orderBy('magasin')->get();

        return view('livewire.stock.produits.liste-produits', [
            'produits' => $produits,
            'categories' => $categories,
            'magasins' => $magasins,
        ]);
    }
}
