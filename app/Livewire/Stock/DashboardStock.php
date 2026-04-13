<?php

namespace App\Livewire\Stock;

use App\Models\StockProduit;
use App\Models\StockMagasin;
use App\Models\StockCategorie;
use App\Models\StockEntree;
use App\Models\StockSortie;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DashboardStock extends Component
{
    public $totalProduits = 0;
    public $produitsEnAlerte = 0;
    public $produitsFaibles = 0;
    public $produitsSuffisants = 0;
    public $totalMagasins = 0;
    public $totalCategories = 0;
    public $entreesduMois = 0;
    public $sortiesDuMois = 0;
    public $nbEntreesMois = 0;
    public $nbSortiesMois = 0;
    public $soldeFluxMois = 0;
    public $produitsASurveillerDetails = [];
    public $stockParMagasin = [];
    public $stockParCategorie = [];
    public $derniersMovements = [];
    public $magasinsEnAlerte = 0;
    public $categoriesEnAlerte = 0;
    public $tauxAlerte = 0.0;
    public $tauxFaible = 0.0;
    public $tauxSuffisant = 0.0;

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->canViewDashboardStock()) {
            abort(403, 'Accès non autorisé.');
        }
    }

    // Appelé à chaque render (mount + updates), garantit des données fraîches
    // même après wire:navigate qui ne rappelle pas mount().
    public function booted()
    {
        $this->loadStatistics();
    }

    public function refresh()
    {
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        // Statistiques globales
        $this->totalProduits = StockProduit::count();
        $this->produitsEnAlerte = StockProduit::whereColumn('stock_actuel', '<=', 'seuil_alerte')->count();
        $this->produitsFaibles = StockProduit::whereColumn('stock_actuel', '>', 'seuil_alerte')
            ->whereRaw('stock_actuel <= seuil_alerte * 1.5')
            ->count();
        $this->produitsSuffisants = max(
            $this->totalProduits - $this->produitsEnAlerte - $this->produitsFaibles,
            0
        );

        if ($this->totalProduits > 0) {
            $this->tauxAlerte = round(($this->produitsEnAlerte / $this->totalProduits) * 100, 1);
            $this->tauxFaible = round(($this->produitsFaibles / $this->totalProduits) * 100, 1);
            $this->tauxSuffisant = round(100 - $this->tauxAlerte - $this->tauxFaible, 1);
        }

        $this->totalMagasins = StockMagasin::count();
        $this->totalCategories = StockCategorie::count();

        // Mouvements du mois en cours
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $this->entreesduMois = StockEntree::whereBetween('date_entree', [$debutMois, $finMois])->sum('quantite');
        $this->nbEntreesMois = StockEntree::whereBetween('date_entree', [$debutMois, $finMois])->count();
        $this->sortiesDuMois = StockSortie::whereBetween('date_sortie', [$debutMois, $finMois])->sum('quantite');
        $this->nbSortiesMois = StockSortie::whereBetween('date_sortie', [$debutMois, $finMois])->count();

        $this->soldeFluxMois = $this->entreesduMois - $this->sortiesDuMois;

        // Produits à surveiller (alerte + faible)
        $this->produitsASurveillerDetails = StockProduit::with(['categorie', 'magasin'])
            ->where(function ($q) {
                $q->whereColumn('stock_actuel', '<=', 'seuil_alerte')
                    ->orWhere(function ($q2) {
                        $q2->whereColumn('stock_actuel', '>', 'seuil_alerte')
                            ->whereRaw('stock_actuel <= seuil_alerte * 1.5');
                    });
            })
            ->orderBy('stock_actuel')
            ->limit(12)
            ->get()
            ->map(function ($produit) {
                return [
                    'id' => $produit->id,
                    'libelle' => $produit->libelle,
                    'categorie' => $produit->categorie->libelle ?? '-',
                    'magasin' => $produit->magasin->magasin ?? '-',
                    'stock_actuel' => $produit->stock_actuel,
                    'seuil_alerte' => $produit->seuil_alerte,
                    'statut' => $produit->statut_stock,
                    'css' => $produit->stock_css_class,
                    'ratio' => $produit->seuil_alerte > 0 ? round(($produit->stock_actuel / $produit->seuil_alerte) * 100, 1) : null,
                ];
            })
            ->toArray();

        // Stock par magasin
        $magasinsRows = StockMagasin::query()
            ->leftJoin('stock_produits', 'stock_produits.magasin_id', '=', 'stock_magasins.id')
            ->select(
                'stock_magasins.magasin',
                'stock_magasins.localisation',
                DB::raw('COUNT(stock_produits.id) as nombre_produits'),
                DB::raw('SUM(CASE WHEN stock_produits.stock_actuel <= stock_produits.seuil_alerte THEN 1 ELSE 0 END) as produits_en_alerte')
            )
            ->groupBy('stock_magasins.id', 'stock_magasins.magasin', 'stock_magasins.localisation')
            ->get();

        $this->stockParMagasin = $magasinsRows->map(function ($magasin) {
            $nombreProduits = (int) ($magasin->nombre_produits ?? 0);
            $produitsEnAlerte = (int) ($magasin->produits_en_alerte ?? 0);

            return [
                'magasin' => $magasin->magasin,
                'localisation' => $magasin->localisation,
                'nombre_produits' => $nombreProduits,
                'produits_en_alerte' => $produitsEnAlerte,
                'risque_ratio' => $nombreProduits > 0 ? ($produitsEnAlerte / $nombreProduits) : 0,
            ];
        })->toArray();

        $this->magasinsEnAlerte = collect($this->stockParMagasin)
            ->where('produits_en_alerte', '>', 0)
            ->count();

        // Stock par catégorie
        $categoriesRows = StockCategorie::query()
            ->leftJoin('stock_produits', 'stock_produits.categorie_id', '=', 'stock_categories.id')
            ->select(
                'stock_categories.libelle',
                DB::raw('COUNT(stock_produits.id) as nombre_produits'),
                DB::raw('SUM(CASE WHEN stock_produits.stock_actuel <= stock_produits.seuil_alerte THEN 1 ELSE 0 END) as produits_en_alerte'),
                DB::raw('COALESCE(SUM(stock_produits.stock_actuel), 0) as stock_total')
            )
            ->groupBy('stock_categories.id', 'stock_categories.libelle')
            ->get();

        $this->stockParCategorie = $categoriesRows->map(function ($categorie) {
            $nombreProduits = (int) ($categorie->nombre_produits ?? 0);
            $produitsEnAlerte = (int) ($categorie->produits_en_alerte ?? 0);

            return [
                'categorie' => $categorie->libelle,
                'nombre_produits' => $nombreProduits,
                'produits_en_alerte' => $produitsEnAlerte,
                'stock_total' => (int) ($categorie->stock_total ?? 0),
                'risque_ratio' => $nombreProduits > 0 ? ($produitsEnAlerte / $nombreProduits) : 0,
            ];
        })->toArray();

        $this->categoriesEnAlerte = collect($this->stockParCategorie)
            ->where('produits_en_alerte', '>', 0)
            ->count();

        // Derniers mouvements (10 derniers)
        $dernieresEntrees = StockEntree::with(['produit', 'fournisseur', 'createur'])
            ->orderBy('date_entree', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($entree) {
                return [
                    'type' => 'entree',
                    'date' => $entree->date_entree,
                    'produit' => $entree->produit->libelle ?? 'N/A',
                    'tiers' => $entree->fournisseur->libelle ?? 'N/A',
                    'quantite' => $entree->quantite,
                    'createur' => $entree->nom_createur,
                ];
            });

        $dernieresSorties = StockSortie::with(['produit', 'demandeur', 'createur'])
            ->orderBy('date_sortie', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($sortie) {
                return [
                    'type' => 'sortie',
                    'date' => $sortie->date_sortie,
                    'produit' => $sortie->produit->libelle ?? 'N/A',
                    'tiers' => $sortie->demandeur->nom ?? 'N/A',
                    'quantite' => $sortie->quantite,
                    'createur' => $sortie->nom_createur,
                ];
            });

        $this->derniersMovements = $dernieresEntrees->concat($dernieresSorties)
            ->sortByDesc('date')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.stock.dashboard-stock');
    }
}
