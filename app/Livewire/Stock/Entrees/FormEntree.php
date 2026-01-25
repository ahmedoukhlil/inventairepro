<?php

namespace App\Livewire\Stock\Entrees;

use App\Models\StockEntree;
use App\Models\StockProduit;
use App\Models\StockFournisseur;
use App\Livewire\Traits\WithCachedOptions;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class FormEntree extends Component
{
    use WithCachedOptions;

    // Champs du formulaire
    public $date_entree;
    public $reference_commande = '';
    public $produit_id = '';
    public $fournisseur_id = '';
    public $quantite = 1;
    public $observations = '';

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->canCreateEntree()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent créer des entrées.');
        }

        // Date du jour par défaut
        $this->date_entree = now()->format('Y-m-d');
    }

    protected function rules()
    {
        return [
            'date_entree' => 'required|date',
            'reference_commande' => 'nullable|string|max:255',
            'produit_id' => 'required|exists:stock_produits,id',
            'fournisseur_id' => 'required|exists:stock_fournisseurs,id',
            'quantite' => 'required|integer|min:1',
            'observations' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'date_entree.required' => 'La date d\'entrée est obligatoire.',
            'produit_id.required' => 'Le produit est obligatoire.',
            'produit_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'fournisseur_id.required' => 'Le fournisseur est obligatoire.',
            'fournisseur_id.exists' => 'Le fournisseur sélectionné n\'existe pas.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être au moins 1.',
        ];
    }

    /**
     * Propriété calculée : Produit sélectionné pour affichage
     */
    public function getProduitSelectionneProperty()
    {
        if (empty($this->produit_id)) {
            return null;
        }

        return StockProduit::with(['categorie', 'magasin'])->find($this->produit_id);
    }

    /**
     * Options pour le select Produits
     */
    public function getProduitOptionsProperty()
    {
        return cache()->remember('stock_produits_options', 300, function () {
            return StockProduit::with('categorie')
                ->orderBy('libelle')
                ->get()
                ->map(function ($produit) {
                    return [
                        'value' => (string)$produit->id,
                        'text' => $produit->libelle . ' [' . ($produit->categorie->libelle ?? 'Sans catégorie') . ']',
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Options pour le select Fournisseurs
     */
    public function getFournisseurOptionsProperty()
    {
        return cache()->remember('stock_fournisseurs_options', 300, function () {
            return StockFournisseur::orderBy('libelle')
                ->get()
                ->map(function ($fournisseur) {
                    return [
                        'value' => (string)$fournisseur->id,
                        'text' => $fournisseur->libelle,
                    ];
                })
                ->toArray();
        });
    }

    public function save()
    {
        $validated = $this->validate();
        
        // Ajouter l'utilisateur qui crée l'entrée
        $validated['created_by'] = auth()->user()->idUser;

        try {
            // Créer l'entrée (le stock sera mis à jour automatiquement via l'event)
            StockEntree::create($validated);

            session()->flash('success', 'Entrée de stock enregistrée avec succès. Le stock a été mis à jour.');
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
