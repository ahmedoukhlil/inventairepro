<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gesimmo extends Model
{
    use HasFactory;

    protected $table = 'gesimmo';
    protected $primaryKey = 'NumOrdre';
    public $timestamps = false;

    protected $fillable = [
        'idDesignation', 'idCategorie', 'idEtat', 
        'idEmplacement', 'idNatJur', 'idSF', 
        'DateAcquisition', 'Observations'
    ];

    protected $casts = [
        'DateAcquisition' => 'integer', // Stocke uniquement l'année (ex: 2019)
    ];

    /**
     * Get the route key for the model.
     * Permet d'utiliser NumOrdre dans les routes au lieu de 'id'
     */
    public function getRouteKeyName()
    {
        return 'NumOrdre';
    }

    /**
     * RELATIONS
     */

    /**
     * Relation avec la désignation
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'idDesignation', 'id');
    }

    /**
     * Relation avec la catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');
    }

    /**
     * Relation avec l'état
     */
    public function etat(): BelongsTo
    {
        return $this->belongsTo(Etat::class, 'idEtat', 'idEtat');
    }

    /**
     * Relation avec l'emplacement
     */
    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'idEmplacement', 'idEmplacement');
    }

    /**
     * Relation avec la nature juridique
     */
    public function natureJuridique(): BelongsTo
    {
        return $this->belongsTo(NatureJuridique::class, 'idNatJur', 'idNatJur');
    }

    /**
     * Relation avec la source de financement
     */
    public function sourceFinancement(): BelongsTo
    {
        return $this->belongsTo(SourceFinancement::class, 'idSF', 'idSF');
    }

    /**
     * Relation avec le code-barres
     */
    public function code(): HasOne
    {
        return $this->hasOne(Code::class, 'idGesimmo', 'NumOrdre');
    }

    /**
     * ACCESSORS
     */

    /**
     * Génère le code d'immobilisation au format: CodeNatJur/CodeDesignation/CodeCategorie/Année/CodeSourceFin/NumOrdre
     */
    public function getCodeFormateAttribute(): string
    {
        // DateAcquisition contient l'année (ex: 2019)
        $annee = ($this->DateAcquisition && $this->DateAcquisition > 1970) ? $this->DateAcquisition : '';
        
        return sprintf(
            '%s/%s/%s/%s/%s/%s',
            $this->natureJuridique->CodeNatJur ?? '',
            $this->designation->CodeDesignation ?? '',
            $this->categorie->CodeCategorie ?? '',
            $annee,
            $this->sourceFinancement->CodeSourceFin ?? '',
            $this->NumOrdre
        );
    }

    /**
     * Génère et sauvegarde le code-barres Code 128 pour cette immobilisation
     * 
     * @return string Le code-barres en SVG (base64 ou SVG direct)
     */
    public function generateBarcode(): string
    {
        $barcodeService = app(\App\Services\BarcodeService::class);
        return $barcodeService->generateForGesimmo($this);
    }
}
