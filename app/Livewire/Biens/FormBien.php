<?php

namespace App\Livewire\Biens;

use App\Models\Bien;
use App\Models\Localisation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormBien extends Component
{
    /**
     * Instance du bien (null si création)
     */
    public $bien = null;

    /**
     * ID du bien pour l'édition
     */
    public $bienId = null;

    /**
     * Propriétés du formulaire
     */
    public $designation = '';
    public $nature = '';
    public $date_acquisition = '';
    public $service_usager = '';
    public $localisation_id = '';
    public $valeur_acquisition = '';
    public $etat = '';
    public $observation = '';
    public $genererQRCode = true;

    /**
     * Initialisation du composant
     * 
     * @param Bien|null $bien Instance du bien pour l'édition, null pour la création
     */
    public function mount($bien = null): void
    {
        if ($bien) {
            // Mode édition : charger les valeurs du bien
            $this->bien = $bien;
            $this->bienId = $bien->id;
            $this->designation = $bien->designation;
            $this->nature = $bien->nature;
            $this->date_acquisition = $bien->date_acquisition->format('Y-m-d');
            $this->service_usager = $bien->service_usager;
            $this->localisation_id = $bien->localisation_id;
            $this->valeur_acquisition = $bien->valeur_acquisition;
            $this->etat = $bien->etat;
            $this->observation = $bien->observation ?? '';
            $this->genererQRCode = false; // Par défaut, ne pas régénérer en édition
        } else {
            // Mode création : valeurs par défaut
            $this->date_acquisition = now()->format('Y-m-d');
            $this->genererQRCode = true;
        }
    }

    /**
     * Propriété calculée : Retourne toutes les localisations actives
     */
    public function getLocalisationsProperty()
    {
        return Localisation::actives()
            ->orderBy('code')
            ->get();
    }

    /**
     * Propriété calculée : Retourne les valeurs enum de nature
     */
    public function getNaturesProperty()
    {
        return [
            'mobilier' => 'Mobilier',
            'informatique' => 'Informatique',
            'vehicule' => 'Véhicule',
            'materiel' => 'Matériel',
        ];
    }

    /**
     * Propriété calculée : Retourne les valeurs enum d'état
     */
    public function getEtatsProperty()
    {
        return [
            'neuf' => 'Neuf',
            'bon' => 'Bon',
            'moyen' => 'Moyen',
            'mauvais' => 'Mauvais',
            'reforme' => 'Réformé',
        ];
    }

    /**
     * Propriété calculée : Vérifie si on est en mode édition
     */
    public function getIsEditProperty(): bool
    {
        return $this->bien !== null;
    }

    /**
     * Propriété calculée : Retourne la liste unique des services pour le datalist
     */
    public function getServicesProperty()
    {
        return Bien::query()
            ->distinct()
            ->whereNotNull('service_usager')
            ->where('service_usager', '!=', '')
            ->orderBy('service_usager')
            ->pluck('service_usager')
            ->unique()
            ->values();
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        return [
            'designation' => 'required|string|max:255',
            'nature' => 'required|in:mobilier,informatique,vehicule,materiel',
            'date_acquisition' => 'required|date|before_or_equal:today',
            'service_usager' => 'required|string|max:255',
            'localisation_id' => 'required|exists:localisations,id',
            'valeur_acquisition' => 'required|numeric|min:0',
            'etat' => 'required|in:neuf,bon,moyen,mauvais,reforme',
            'observation' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'designation.required' => 'La désignation est obligatoire.',
            'nature.required' => 'La nature du bien est obligatoire.',
            'date_acquisition.required' => 'La date d\'acquisition est obligatoire.',
            'date_acquisition.before_or_equal' => 'La date d\'acquisition ne peut pas être dans le futur.',
            'service_usager.required' => 'Le service usager est obligatoire.',
            'localisation_id.required' => 'La localisation est obligatoire.',
            'localisation_id.exists' => 'La localisation sélectionnée n\'existe pas.',
            'valeur_acquisition.required' => 'La valeur d\'acquisition est obligatoire.',
            'valeur_acquisition.numeric' => 'La valeur d\'acquisition doit être un nombre.',
            'valeur_acquisition.min' => 'La valeur d\'acquisition doit être positive.',
            'etat.required' => 'L\'état du bien est obligatoire.',
            'observation.max' => 'L\'observation ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Sauvegarde le bien (création ou édition)
     */
    public function save()
    {
        // Valider les données
        $validated = $this->validate();

        try {
            if ($this->isEdit) {
                // Mode édition : mettre à jour le bien existant
                $this->bien->update([
                    'designation' => $validated['designation'],
                    'nature' => $validated['nature'],
                    'date_acquisition' => $validated['date_acquisition'],
                    'service_usager' => $validated['service_usager'],
                    'localisation_id' => $validated['localisation_id'],
                    'valeur_acquisition' => $validated['valeur_acquisition'],
                    'etat' => $validated['etat'],
                    'observation' => $validated['observation'] ?? null,
                ]);

                $bien = $this->bien->fresh();
                $message = 'Bien modifié avec succès';
            } else {
                // Mode création : créer un nouveau bien
                $codeInventaire = Bien::generateCodeInventaire();

                $bien = Bien::create([
                    'code_inventaire' => $codeInventaire,
                    'designation' => $validated['designation'],
                    'nature' => $validated['nature'],
                    'date_acquisition' => $validated['date_acquisition'],
                    'service_usager' => $validated['service_usager'],
                    'localisation_id' => $validated['localisation_id'],
                    'valeur_acquisition' => $validated['valeur_acquisition'],
                    'etat' => $validated['etat'],
                    'observation' => $validated['observation'] ?? null,
                    'user_id' => Auth::id(),
                ]);

                $message = 'Bien créé avec succès';
            }

            // Générer le QR code si demandé ou s'il n'existe pas
            if ($this->genererQRCode || !$bien->qr_code_path) {
                try {
                    $bien->generateQRCode();
                } catch (\Exception $e) {
                    // Logger l'erreur mais ne pas bloquer la sauvegarde
                    \Illuminate\Support\Facades\Log::warning("Impossible de générer le QR code pour le bien {$bien->code_inventaire}: " . $e->getMessage());
                }
            }

            session()->flash('success', $message);

            // Rediriger vers la page de détail du bien
            return redirect()->route('biens.show', $bien);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Annule et redirige vers la liste
     */
    public function cancel()
    {
        return redirect()->route('biens.index');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.biens.form-bien');
    }
}

