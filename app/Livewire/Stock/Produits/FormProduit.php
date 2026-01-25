<?php

namespace App\Livewire\Stock\Produits;

use App\Models\StockProduit;
use App\Models\StockCategorie;
use App\Models\StockMagasin;
use App\Livewire\Traits\WithCachedOptions;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class FormProduit extends Component
{
    use WithCachedOptions;

    public $produit = null;
    public $id = null;

    // Champs du formulaire
    public $libelle = '';
    public $categorie_id = '';
    public $magasin_id = '';
    public $seuil_alerte = 10;
    public $descriptif = '';
    public $stockage = '';
    public $observations = '';

    public function mount($id = null)
    {
        $user = auth()->user();
        if (!$user || !$user->canManageStock()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent gérer les produits.');
        }

        if ($id) {
            $this->id = $id;
            $this->produit = StockProduit::findOrFail($id);
            $this->libelle = $this->produit->libelle;
            $this->categorie_id = $this->produit->categorie_id;
            $this->magasin_id = $this->produit->magasin_id;
            $this->seuil_alerte = $this->produit->seuil_alerte;
            $this->descriptif = $this->produit->descriptif ?? '';
            $this->stockage = $this->produit->stockage ?? '';
            $this->observations = $this->produit->observations ?? '';
        }
    }

    protected function rules()
    {
        return [
            'libelle' => 'required|string|max:255',
            'categorie_id' => 'required|exists:stock_categories,id',
            'magasin_id' => 'required|exists:stock_magasins,id',
            'seuil_alerte' => 'required|integer|min:0',
            'descriptif' => 'nullable|string',
            'stockage' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'categorie_id.required' => 'La catégorie est obligatoire.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'magasin_id.required' => 'Le magasin est obligatoire.',
            'magasin_id.exists' => 'Le magasin sélectionné n\'existe pas.',
            'seuil_alerte.required' => 'Le seuil d\'alerte est obligatoire.',
            'seuil_alerte.integer' => 'Le seuil d\'alerte doit être un nombre entier.',
            'seuil_alerte.min' => 'Le seuil d\'alerte ne peut pas être négatif.',
        ];
    }

    /**
     * Options pour le select Catégories
     */
    public function getCategorieOptionsProperty()
    {
        return cache()->remember('stock_categories_options', 300, function () {
            return StockCategorie::orderBy('libelle')
                ->get()
                ->map(function ($cat) {
                    return [
                        'value' => (string)$cat->id,
                        'text' => $cat->libelle,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Options pour le select Magasins
     */
    public function getMagasinOptionsProperty()
    {
        return cache()->remember('stock_magasins_options', 300, function () {
            return StockMagasin::orderBy('magasin')
                ->get()
                ->map(function ($mag) {
                    return [
                        'value' => (string)$mag->id,
                        'text' => $mag->magasin . ' (' . $mag->localisation . ')',
                    ];
                })
                ->toArray();
        });
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->produit) {
            // En mode édition, ne pas modifier stock_initial ni stock_actuel
            // Ils sont gérés uniquement via les entrées/sorties
            $this->produit->update($validated);
            session()->flash('success', 'Produit modifié avec succès.');
        } else {
            // En création, initialiser stock_initial et stock_actuel à 0
            // Le stock sera ajouté via les opérations d'entrée
            $validated['stock_initial'] = 0;
            $validated['stock_actuel'] = 0;
            
            StockProduit::create($validated);
            session()->flash('success', 'Produit créé avec succès. Vous pouvez maintenant ajouter le stock initial via une opération d\'entrée.');
        }

        return redirect()->route('stock.produits.index');
    }

    public function cancel()
    {
        return redirect()->route('stock.produits.index');
    }

    public function render()
    {
        return view('livewire.stock.produits.form-produit');
    }
}
