<?php

namespace App\Livewire\Stock\Categories;

use App\Models\StockCategorie;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class FormCategorie extends Component
{
    public $categorie = null;
    public $id = null;
    public $libelle = '';
    public $observations = '';

    public function mount($id = null)
    {
        $user = auth()->user();
        if (!$user || !$user->canManageStock()) {
            abort(403, 'Accès non autorisé.');
        }

        if ($id) {
            $this->id = $id;
            $this->categorie = StockCategorie::findOrFail($id);
            $this->libelle = $this->categorie->libelle;
            $this->observations = $this->categorie->observations ?? '';
        }
    }

    protected function rules()
    {
        return [
            'libelle' => 'required|string|max:255',
            'observations' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->categorie) {
            $this->categorie->update($validated);
            session()->flash('success', 'Catégorie modifiée avec succès.');
        } else {
            StockCategorie::create($validated);
            session()->flash('success', 'Catégorie créée avec succès.');
        }

        Cache::forget('stock_categories_options');

        return redirect()->route('stock.categories.index');
    }

    public function cancel()
    {
        return redirect()->route('stock.categories.index');
    }

    public function render()
    {
        return view('livewire.stock.categories.form-categorie');
    }
}
