<?php

namespace App\Livewire\Localisations;

use App\Models\Localisation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]

class FormLocalisation extends Component
{
    /**
     * Instance de la localisation (null si création)
     */
    public $localisation = null;

    /**
     * ID de la localisation pour l'édition
     */
    public $localisationId = null;

    /**
     * Propriétés du formulaire
     */
    public $code = '';
    public $designation = '';
    public $batiment = '';
    public $etage = '';
    public $service_rattache = '';
    public $responsable = '';
    public $actif = true;
    public $genererQRCode = true;

    /**
     * Initialisation du composant
     * 
     * @param Localisation|int|string|null $localisation Instance de la localisation pour l'édition, ID, ou null pour la création
     */
    public function mount($localisation = null): void
    {
        if ($localisation) {
            // Si c'est un ID (string ou int) et pas une instance de Localisation, charger la localisation
            if (!($localisation instanceof Localisation)) {
                if (is_numeric($localisation) || (is_string($localisation) && ctype_digit($localisation))) {
                    try {
                        $localisation = Localisation::findOrFail($localisation);
                    } catch (\Exception $e) {
                        Log::warning('Localisation introuvable pour édition:', ['id' => $localisation]);
                        // Si la localisation n'existe pas, traiter comme création
                        $localisation = null;
                    }
                } else {
                    // Si ce n'est ni un ID ni une instance, traiter comme création
                    $localisation = null;
                }
            }
            
            // Si on a une instance valide de Localisation
            if ($localisation instanceof Localisation) {
                // Mode édition : charger les valeurs de la localisation
                $this->localisation = $localisation;
                $this->localisationId = $localisation->id;
                $this->code = $localisation->code;
                $this->designation = $localisation->designation;
                $this->batiment = $localisation->batiment ?? '';
                $this->etage = $localisation->etage !== null ? (string) $localisation->etage : '';
                $this->service_rattache = $localisation->service_rattache ?? '';
                $this->responsable = $localisation->responsable ?? '';
                $this->actif = $localisation->actif;
                $this->genererQRCode = false; // Par défaut, ne pas régénérer en édition
            } else {
                // Mode création : valeurs par défaut
                $this->actif = true;
                $this->genererQRCode = true;
            }
        } else {
            // Mode création : valeurs par défaut
            $this->actif = true;
            $this->genererQRCode = true;
        }
    }

    /**
     * Propriété calculée : Retourne la liste des bâtiments existants pour suggestions
     */
    public function getBatimentsExistantsProperty()
    {
        return Localisation::query()
            ->distinct()
            ->whereNotNull('batiment')
            ->where('batiment', '!=', '')
            ->orderBy('batiment')
            ->pluck('batiment')
            ->unique()
            ->values();
    }

    /**
     * Propriété calculée : Retourne la liste des services existants pour suggestions
     */
    public function getServicesExistantsProperty()
    {
        return Localisation::query()
            ->distinct()
            ->whereNotNull('service_rattache')
            ->where('service_rattache', '!=', '')
            ->orderBy('service_rattache')
            ->pluck('service_rattache')
            ->unique()
            ->values();
    }

    /**
     * Propriété calculée : Vérifie si on est en mode édition
     */
    public function getIsEditProperty(): bool
    {
        return $this->localisation !== null;
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        $rules = [
            'code' => 'required|string|max:50',
            'designation' => 'required|string|max:255',
            'batiment' => 'nullable|string|max:100',
            'etage' => 'nullable|integer|min:-2|max:20',
            'service_rattache' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ];
        
        // Règle unique pour le code (sauf en édition)
        if ($this->isEdit && $this->localisationId) {
            $rules['code'] .= '|unique:localisations,code,' . $this->localisationId;
        } else {
            $rules['code'] .= '|unique:localisations,code';
        }
        
        return $rules;
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'code.required' => 'Le code de localisation est obligatoire.',
            'code.unique' => 'Ce code de localisation est déjà utilisé.',
            'code.max' => 'Le code ne peut pas dépasser 50 caractères.',
            'designation.required' => 'La désignation est obligatoire.',
            'designation.max' => 'La désignation ne peut pas dépasser 255 caractères.',
            'batiment.max' => 'Le bâtiment ne peut pas dépasser 100 caractères.',
            'etage.integer' => 'L\'étage doit être un nombre entier.',
            'etage.min' => 'L\'étage ne peut pas être inférieur à -2.',
            'etage.max' => 'L\'étage ne peut pas être supérieur à 20.',
            'service_rattache.max' => 'Le service rattaché ne peut pas dépasser 255 caractères.',
            'responsable.max' => 'Le responsable ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Génère une suggestion de code automatique
     * 
     * Format : TYPE-BATIMENT-ETAGE-NUMERO
     * Exemple : BUR-A-1-001
     */
    public function generateCodeSuggestion(): void
    {
        // Déterminer le type selon la désignation
        $type = 'BUR'; // Par défaut Bureau
        if (!empty($this->designation)) {
            $designationUpper = strtoupper($this->designation);
            if (str_contains($designationUpper, 'ATELIER') || str_contains($designationUpper, 'WORKSHOP')) {
                $type = 'ATL';
            } elseif (str_contains($designationUpper, 'SALLE') || str_contains($designationUpper, 'ROOM')) {
                $type = 'SAL';
            } elseif (str_contains($designationUpper, 'ENTREPOT') || str_contains($designationUpper, 'WAREHOUSE') || str_contains($designationUpper, 'STOCK')) {
                $type = 'ENT';
            } elseif (str_contains($designationUpper, 'PARKING') || str_contains($designationUpper, 'GARAGE')) {
                $type = 'PKG';
            } elseif (str_contains($designationUpper, 'LABORATOIRE') || str_contains($designationUpper, 'LAB')) {
                $type = 'LAB';
            } elseif (str_contains($designationUpper, 'CAFETERIA') || str_contains($designationUpper, 'RESTAURANT')) {
                $type = 'CAF';
            }
        }
        
        // Nettoyer et formater le bâtiment
        $batiment = 'X';
        if (!empty($this->batiment)) {
            $batiment = strtoupper(trim($this->batiment));
            // Nettoyer le bâtiment (garder seulement lettres et chiffres)
            $batiment = preg_replace('/[^A-Z0-9]/', '', $batiment);
            if (empty($batiment)) {
                $batiment = 'X';
            }
            // Limiter à 3 caractères
            $batiment = substr($batiment, 0, 3);
        }
        
        // Formater l'étage
        $etage = 'X';
        if ($this->etage !== '' && $this->etage !== null && is_numeric($this->etage)) {
            $etage = (string) (int) $this->etage;
        }

        // Chercher le dernier numéro pour ce pattern
        $pattern = "{$type}-{$batiment}-{$etage}-%";
        $lastLocalisation = Localisation::where('code', 'like', $pattern)
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastLocalisation) {
            // Extraire le numéro et incrémenter
            $parts = explode('-', $lastLocalisation->code);
            if (isset($parts[3]) && is_numeric($parts[3])) {
                $lastNumber = (int) $parts[3];
                $nextNumber = $lastNumber + 1;
            }
        }

        // Générer le code avec numéro sur 3 chiffres
        $this->code = sprintf('%s-%s-%s-%03d', $type, $batiment, $etage, $nextNumber);
    }

    /**
     * Sauvegarde la localisation (création ou édition)
     */
    public function save()
    {
        // Générer automatiquement le code si vide (en mode création uniquement)
        if (!$this->isEdit && empty(trim($this->code ?? ''))) {
            $this->generateCodeSuggestion();
        }
        
        // Normaliser les valeurs vides avant validation
        $this->code = trim($this->code ?? '');
        $this->designation = trim($this->designation ?? '');
        $this->batiment = ($this->batiment === '' || $this->batiment === null) ? null : trim($this->batiment);
        $this->etage = ($this->etage === '' || $this->etage === null) ? null : (is_numeric($this->etage) ? (int) $this->etage : null);
        $this->service_rattache = ($this->service_rattache === '' || $this->service_rattache === null) ? null : trim($this->service_rattache);
        $this->responsable = ($this->responsable === '' || $this->responsable === null) ? null : trim($this->responsable);
        $this->actif = (bool) ($this->actif ?? true);

        // Valider les données
        try {
            $validated = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Les erreurs de validation sont automatiquement affichées par Livewire
            throw $e;
        }

        try {
            // Préparer les données pour l'insertion - utiliser directement les valeurs validées
            $data = [
                'code' => trim($validated['code']),
                'designation' => trim($validated['designation']),
                'batiment' => !empty($validated['batiment']) ? trim($validated['batiment']) : null,
                'etage' => isset($validated['etage']) && $validated['etage'] !== null && $validated['etage'] !== '' ? (int) $validated['etage'] : null,
                'service_rattache' => !empty($validated['service_rattache']) ? trim($validated['service_rattache']) : null,
                'responsable' => !empty($validated['responsable']) ? trim($validated['responsable']) : null,
                'actif' => (bool) ($validated['actif'] ?? true),
            ];

            if ($this->isEdit && $this->localisation) {
                // Mode édition : mettre à jour la localisation existante
                $this->localisation->update($data);
                $localisation = $this->localisation->fresh();
                $message = 'Localisation modifiée avec succès';
            } else {
                // Mode création : créer une nouvelle localisation
                // Vérifier que le code n'existe pas déjà (double vérification)
                $existing = Localisation::where('code', $data['code'])->first();
                if ($existing) {
                    $this->addError('code', 'Ce code de localisation existe déjà.');
                    return;
                }
                
                // Créer la localisation avec DB transaction pour sécurité
                try {
                    DB::beginTransaction();
                    $localisation = Localisation::create($data);
                    DB::commit();
                    
                    // Vérifier que la création a réussi
                    if (!$localisation || !$localisation->id) {
                        throw new \Exception('Échec de la création de la localisation - aucun ID retourné');
                    }
                    
                    $message = 'Localisation créée avec succès';
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    Log::error('Erreur SQL lors de la création de localisation', [
                        'message' => $e->getMessage(),
                        'code' => $data['code'] ?? 'N/A'
                    ]);
                    throw new \Exception('Erreur lors de l\'insertion en base de données : ' . $e->getMessage());
                }
            }

            // Générer le QR code si demandé
            if ($this->genererQRCode && $localisation) {
                try {
                    $localisation->generateQRCode();
                } catch (\Exception $qrError) {
                    // Ne pas bloquer la sauvegarde si le QR code échoue
                    Log::warning('Erreur génération QR code', ['error' => $qrError->getMessage()]);
                }
            }

            // Invalider le cache des statistiques
            \Illuminate\Support\Facades\Cache::forget('localisations_total_count');
            \Illuminate\Support\Facades\Cache::forget('localisations_batiments_count');
            \Illuminate\Support\Facades\Cache::forget('localisations_batiments');
            \Illuminate\Support\Facades\Cache::forget('localisations_etages');
            \Illuminate\Support\Facades\Cache::forget('localisations_services');

            session()->flash('success', $message);

            // Rediriger vers la page de détail de la localisation
            return $this->redirect(route('localisations.show', $localisation), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde localisation', [
                'message' => $e->getMessage(),
                'code' => $this->code ?? 'N/A'
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde : ' . $e->getMessage());
            return;
        }
    }

    /**
     * Annule et redirige vers la liste
     */
    public function cancel()
    {
        return redirect()->route('localisations.index');
    }

    /**
     * Vérifie si le code est unique (pour validation en temps réel)
     */
    public function updatedCode($value)
    {
        if (empty($value)) {
            return;
        }

        $exists = Localisation::where('code', $value)
            ->where('id', '!=', $this->localisationId)
            ->exists();

        if ($exists) {
            $this->addError('code', 'Ce code de localisation est déjà utilisé.');
        } else {
            $this->resetErrorBag('code');
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.localisations.form-localisation');
    }
}

