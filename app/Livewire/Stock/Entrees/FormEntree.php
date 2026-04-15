<?php

namespace App\Livewire\Stock\Entrees;

use App\Models\StockEntree;
use App\Models\StockProduit;
use App\Models\StockFournisseur;
use App\Livewire\Traits\WithCachedOptions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class FormEntree extends Component
{
    use WithCachedOptions;

    public $date_entree;
    public $reference_commande = '';
    public $fournisseur_id = '';
    public $observations = '';

    // Lignes d'articles : [['produit_id' => '', 'quantite' => 1, 'stock_actuel' => 0, 'produit_libelle' => '']]
    public array $lignes = [];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->canCreateEntree()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent créer des entrées.');
        }

        $this->date_entree = now()->format('Y-m-d');
        $this->lignes = [['produit_id' => '', 'quantite' => 1, 'stock_actuel' => 0, 'produit_libelle' => '']];
    }

    public function ajouterLigne()
    {
        $this->lignes[] = ['produit_id' => '', 'quantite' => 1, 'stock_actuel' => 0, 'produit_libelle' => ''];
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
        [$index, $field] = explode('.', $key, 2);
        $index = (int) $index;

        if ($field === 'produit_id') {
            $produit = $value ? StockProduit::find($value) : null;
            $this->lignes[$index]['stock_actuel']    = $produit ? ($produit->stock_actuel ?? 0) : 0;
            $this->lignes[$index]['produit_libelle'] = $produit ? $produit->libelle : '';
        }
    }

    public function getProduitOptionsProperty()
    {
        return cache()->remember('stock_produits_options', 300, function () {
            return StockProduit::with('categorie')
                ->orderBy('libelle')
                ->get()
                ->map(fn($p) => [
                    'value' => (string) $p->id,
                    'text'  => $p->libelle . ' [' . ($p->categorie->libelle ?? 'Sans catégorie') . '] — Stock: ' . $p->stock_actuel,
                ])
                ->toArray();
        });
    }

    public function getFournisseurOptionsProperty()
    {
        return cache()->remember('stock_fournisseurs_options', 300, function () {
            return StockFournisseur::orderBy('libelle')
                ->get()
                ->map(fn($f) => [
                    'value' => (string) $f->id,
                    'text'  => $f->libelle,
                ])
                ->toArray();
        });
    }

    public function save()
    {
        $this->validate([
            'date_entree'         => 'required|date',
            'reference_commande'  => 'nullable|string|max:255',
            'fournisseur_id'      => 'nullable|exists:stock_fournisseurs,id',
            'lignes'              => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:stock_produits,id',
            'lignes.*.quantite'   => 'required|integer|min:1',
        ], [
            'date_entree.required'         => 'La date d\'entrée est obligatoire.',
            'lignes.*.produit_id.required' => 'Sélectionnez un produit pour chaque ligne.',
            'lignes.*.quantite.required'   => 'La quantité est obligatoire.',
            'lignes.*.quantite.min'        => 'La quantité doit être au moins 1.',
        ]);

        // Vérification des doublons de produits
        $produitIds = array_column($this->lignes, 'produit_id');
        if (count($produitIds) !== count(array_unique($produitIds))) {
            $this->addError('lignes', 'Vous avez sélectionné le même produit plusieurs fois. Fusionnez les lignes.');
            return;
        }

        try {
            $groupeId = Str::uuid()->toString();

            DB::transaction(function () use ($groupeId) {
                $numeroEntree = StockEntree::genererNumeroEntree($this->date_entree);

                foreach ($this->lignes as $ligne) {
                    StockEntree::create([
                        'date_entree'        => $this->date_entree,
                        'reference_commande' => $this->reference_commande ?: null,
                        'produit_id'         => $ligne['produit_id'],
                        'fournisseur_id'     => $this->fournisseur_id ?: null,
                        'quantite'           => $ligne['quantite'],
                        'observations'       => $this->observations ?: null,
                        'created_by'         => auth()->user()->idUser,
                        'groupe_id'          => $groupeId,
                        'numero_entree'      => $numeroEntree,
                    ]);
                }
            });

            $nb = count($this->lignes);
            session()->flash('success', $nb . ' article(s) enregistré(s) avec succès.');
            return redirect()->route('stock.entrees.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('stock.entrees.index');
    }

    public function render()
    {
        return view('livewire.stock.entrees.form-entree');
    }
}