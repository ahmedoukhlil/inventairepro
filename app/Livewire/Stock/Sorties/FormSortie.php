<?php

namespace App\Livewire\Stock\Sorties;

use App\Models\StockSortie;
use App\Models\StockProduit;
use App\Models\StockDemandeur;
use App\Livewire\Traits\WithCachedOptions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class FormSortie extends Component
{
    use WithCachedOptions;

    public $date_sortie;
    public $demandeur_id = '';
    public $observations = '';

    // Lignes d'articles : [['produit_id' => '', 'quantite' => 1, 'stock_disponible' => 0, 'produit_libelle' => '']]
    public array $lignes = [];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->canCreateSortie()) {
            abort(403, 'Accès non autorisé.');
        }

        $this->date_sortie = now()->format('Y-m-d');
        $this->lignes = [['produit_id' => '', 'quantite' => 1, 'stock_disponible' => 0, 'produit_libelle' => '']];
    }

    public function ajouterLigne()
    {
        $this->lignes[] = ['produit_id' => '', 'quantite' => 1, 'stock_disponible' => 0, 'produit_libelle' => ''];
    }

    public function supprimerLigne(int $index)
    {
        if (count($this->lignes) > 1) {
            array_splice($this->lignes, $index, 1);
            $this->lignes = array_values($this->lignes);
        }
    }

    public function updatedLignes($value, $key)
    {
        // key format : "0.produit_id"
        [$index, $field] = explode('.', $key, 2);
        $index = (int) $index;

        if ($field === 'produit_id') {
            $produit = $value ? StockProduit::find($value) : null;
            $this->lignes[$index]['stock_disponible'] = $produit ? ($produit->stock_actuel ?? 0) : 0;
            $this->lignes[$index]['produit_libelle']  = $produit ? $produit->libelle : '';
            // Ajuster la quantité si nécessaire
            $stock = $this->lignes[$index]['stock_disponible'];
            if ($stock > 0 && $this->lignes[$index]['quantite'] > $stock) {
                $this->lignes[$index]['quantite'] = $stock;
            }
        }
    }

    public function getProduitOptionsProperty()
    {
        return cache()->remember('stock_produits_options_with_stock', 300, function () {
            return StockProduit::with('categorie')
                ->orderBy('libelle')
                ->get()
                ->map(fn($p) => [
                    'value' => (string) $p->id,
                    'text'  => $p->libelle . ' (Stock: ' . $p->stock_actuel . ')' . ($p->en_alerte ? ' 🔴' : ($p->stock_faible ? ' 🟡' : '')),
                ])
                ->toArray();
        });
    }

    public function getDemandeurOptionsProperty()
    {
        return cache()->remember('stock_demandeurs_options', 300, function () {
            return StockDemandeur::orderBy('nom')
                ->get()
                ->map(fn($d) => [
                    'value' => (string) $d->id,
                    'text'  => $d->nom . ' - ' . $d->poste_service,
                ])
                ->toArray();
        });
    }

    public function save()
    {
        $this->validate([
            'date_sortie'  => 'required|date',
            'demandeur_id' => 'required|exists:stock_demandeurs,id',
            'lignes'       => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:stock_produits,id',
            'lignes.*.quantite'   => 'required|integer|min:1',
        ], [
            'date_sortie.required'         => 'La date de sortie est obligatoire.',
            'demandeur_id.required'        => 'Le demandeur est obligatoire.',
            'lignes.*.produit_id.required' => 'Sélectionnez un produit pour chaque ligne.',
            'lignes.*.quantite.required'   => 'La quantité est obligatoire.',
            'lignes.*.quantite.min'        => 'La quantité doit être au moins 1.',
        ]);

        // Vérification des stocks et doublons
        $produitIds = array_column($this->lignes, 'produit_id');
        if (count($produitIds) !== count(array_unique($produitIds))) {
            $this->addError('lignes', 'Vous avez sélectionné le même produit plusieurs fois. Fusionnez les lignes.');
            return;
        }

        $produits = StockProduit::whereIn('id', $produitIds)->get()->keyBy('id');
        foreach ($this->lignes as $i => $ligne) {
            $produit = $produits[$ligne['produit_id']] ?? null;
            if (!$produit) {
                $this->addError("lignes.$i.produit_id", 'Produit introuvable.');
                return;
            }
            if ($produit->stock_actuel <= 0) {
                $this->addError("lignes.$i.quantite", "Stock épuisé pour « {$produit->libelle} ».");
                return;
            }
            if ($ligne['quantite'] > $produit->stock_actuel) {
                $this->addError("lignes.$i.quantite", "Stock insuffisant pour « {$produit->libelle} » (disponible : {$produit->stock_actuel}).");
                return;
            }
        }

        try {
            DB::transaction(function () use ($produits) {
                foreach ($this->lignes as $ligne) {
                    StockSortie::create([
                        'date_sortie'  => $this->date_sortie,
                        'produit_id'   => $ligne['produit_id'],
                        'demandeur_id' => $this->demandeur_id,
                        'quantite'     => $ligne['quantite'],
                        'observations' => $this->observations,
                        'created_by'   => auth()->user()->idUser,
                    ]);
                }
            });

            $nb = count($this->lignes);
            session()->flash('success', $nb . ' sortie(s) enregistrée(s) avec succès.');
            return redirect()->route('stock.sorties.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('stock.sorties.index');
    }

    public function render()
    {
        return view('livewire.stock.sorties.form-sortie');
    }
}