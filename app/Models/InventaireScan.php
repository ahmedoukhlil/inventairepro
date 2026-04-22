<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InventaireScan extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'inventaire_id',
        'inventaire_localisation_id',
        'bien_id',
        'date_scan',
        'statut_scan',
        'localisation_reelle_id',
        'etat_constate',
        'commentaire',
        'photo_path',
        'user_id',
    ];

    protected $casts = [
        'date_scan'   => 'datetime',
        'commentaire' => 'encrypted', // Chiffré au repos (APP_KEY requis)
    ];

    /**
     * RELATIONS
     */

    /**
     * Relation avec l'inventaire
     */
    public function inventaire(): BelongsTo
    {
        return $this->belongsTo(Inventaire::class);
    }

    /**
     * Relation avec l'inventaire_localisation
     */
    public function inventaireLocalisation(): BelongsTo
    {
        return $this->belongsTo(InventaireLocalisation::class);
    }

    /**
     * Relation avec le bien (Gesimmo)
     * bien_id contient le NumOrdre de la table gesimmo
     */
    public function bien(): BelongsTo
    {
        return $this->belongsTo(Gesimmo::class, 'bien_id', 'NumOrdre');
    }

    /**
     * Alias de bien() pour compatibilité
     */
    public function gesimmo(): BelongsTo
    {
        return $this->bien();
    }

    /**
     * Relation avec la localisation réelle (où le bien a été trouvé)
     */
    public function localisationReelle(): BelongsTo
    {
        return $this->belongsTo(LocalisationImmo::class, 'localisation_reelle_id', 'idLocalisation');
    }

    /**
     * Relation avec l'agent qui a effectué le scan
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }

    /**
     * SCOPES
     */

    public function scopePresents(Builder $query): Builder
    {
        return $query->where('statut_scan', 'present');
    }

    public function scopeDeplaces(Builder $query): Builder
    {
        return $query->where('statut_scan', 'deplace');
    }

    public function scopeAbsents(Builder $query): Builder
    {
        return $query->where('statut_scan', 'absent');
    }

    public function scopeByLocalisation(Builder $query, int $localisationId): Builder
    {
        return $query->where('localisation_reelle_id', $localisationId);
    }

    /**
     * ACCESSORS
     */

    /**
     * Vérifie si le scan est conforme (localisation réelle = localisation prévue)
     */
    public function getIsConformeAttribute(): bool
    {
        if (!$this->bien || !$this->localisationReelle) {
            return false;
        }

        $locPrevue = $this->bien->emplacement?->idLocalisation;
        return $locPrevue === $this->localisation_reelle_id;
    }

    /**
     * Vérifie si le bien est déplacé ou absent
     */
    public function getIsDeformeAttribute(): bool
    {
        return in_array($this->statut_scan, ['deplace', 'absent']);
    }

    /**
     * Retourne le code inventaire depuis Gesimmo
     */
    public function getCodeInventaireAttribute(): string
    {
        if ($this->bien) {
            return $this->bien->code_formate ?? ('GS' . $this->bien->NumOrdre);
        }
        return 'GS' . $this->bien_id;
    }

    /**
     * Retourne la désignation depuis Gesimmo
     */
    public function getDesignationAttribute(): string
    {
        if ($this->bien && $this->bien->designation) {
            return $this->bien->designation->designation ?? 'N/A';
        }
        return 'N/A';
    }

    /**
     * Retourne le code localisation du bien
     */
    public function getLocalisationCodeAttribute(): ?string
    {
        $loc = $this->bien?->emplacement?->localisation;
        if ($loc) {
            return $loc->CodeLocalisation ?? $loc->Localisation;
        }
        return null;
    }

    /**
     * Retourne la catégorie du bien
     */
    public function getCategorieAttribute(): ?string
    {
        return $this->bien?->categorie?->Categorie;
    }

    /**
     * Libellé de l'état constaté (3 états: Neuf, Bon état, Défectueuse)
     */
    public function getEtatConstateLabelAttribute(): string
    {
        static $labels = null;
        if ($labels === null) {
            $codeMap = ['NF' => 'neuf', 'BE' => 'bon', 'DFCT' => 'mauvais'];
            $labelMap = [
                'neuf' => 'neuf', 'bon' => 'bon', 'bon etat' => 'bon', 'bon état' => 'bon',
                'mauvais' => 'mauvais', 'défectueux' => 'mauvais', 'defectueux' => 'mauvais',
                'défectueuse' => 'mauvais', 'defectueuse' => 'mauvais',
            ];
            $labels = ['bon' => 'Bon état', 'neuf' => 'Neuf', 'moyen' => 'Moyen', 'mauvais' => 'Défectueux'];
            foreach (\App\Models\Etat::all() as $etat) {
                $constate = $codeMap[$etat->CodeEtat] ?? ($labelMap[mb_strtolower(trim($etat->Etat))] ?? null);
                if ($constate) {
                    $labels[$constate] = $etat->Etat;
                }
            }
        }
        return $labels[$this->etat_constate ?? 'bon'] ?? 'Bon état';
    }

    /**
     * URL publique de la photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $this->photo_path), '/');
        return '/storage/' . $path;
    }
}

