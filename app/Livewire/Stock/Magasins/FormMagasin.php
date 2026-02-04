<?php

namespace App\Livewire\Stock\Magasins;

use App\Models\StockMagasin;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class FormMagasin extends Component
{
    public $magasin = null;
    public $id = null;

    // Champs du formulaire
    public $magasinNom = '';
    public $localisation = '';
    public $observations = '';

    /**
     * Vérification des permissions et chargement des données
     */
    public function mount($id = null)
    {
        $user = auth()->user();
        if (!$user || !$user->canManageStock()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent gérer les magasins.');
        }

        if ($id) {
            $this->id = $id;
            $this->magasin = StockMagasin::findOrFail($id);
            $this->magasinNom = $this->magasin->magasin;
            $this->localisation = $this->magasin->localisation;
            $this->observations = $this->magasin->observations ?? '';
        }
    }

    /**
     * Règles de validation
     */
    protected function rules()
    {
        return [
            'magasinNom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'observations' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages()
    {
        return [
            'magasinNom.required' => 'Le nom du magasin est obligatoire.',
            'magasinNom.max' => 'Le nom du magasin ne peut pas dépasser 255 caractères.',
            'localisation.required' => 'La localisation est obligatoire.',
            'localisation.max' => 'La localisation ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Sauvegarder le magasin
     */
    public function save()
    {
        $validated = $this->validate();

        if ($this->magasin) {
            // Mise à jour
            $this->magasin->update([
                'magasin' => $validated['magasinNom'],
                'localisation' => $validated['localisation'],
                'observations' => $validated['observations'],
            ]);

            session()->flash('success', 'Magasin modifié avec succès.');
        } else {
            // Création
            StockMagasin::create([
                'magasin' => $validated['magasinNom'],
                'localisation' => $validated['localisation'],
                'observations' => $validated['observations'],
            ]);

            session()->flash('success', 'Magasin créé avec succès.');
        }

        Cache::forget('stock_magasins_options');

        return redirect()->route('stock.magasins.index');
    }

    /**
     * Annuler et retourner à la liste
     */
    public function cancel()
    {
        return redirect()->route('stock.magasins.index');
    }

    public function render()
    {
        return view('livewire.stock.magasins.form-magasin');
    }
}
