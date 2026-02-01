<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Gesimmo;

class InventaireScan extends Model
{
    use HasFactory;

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
        'date_scan' => 'datetime',
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
     * Relation avec le bien
     * Note: Dans le nouveau workflow, bien_id peut contenir NumOrdre (Gesimmo)
     * ou l'ID d'un Bien selon le contexte
     */
    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    /**
     * Relation avec Gesimmo (pour le nouveau workflow)
     */
    public function gesimmo(): BelongsTo
    {
        return $this->belongsTo(Gesimmo::class, 'bien_id', 'NumOrdre');
    }

    /**
     * Relation avec la localisation réelle (où le bien a été trouvé)
     * Utilise LocalisationImmo car c'est la table utilisée pour les inventaires
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

    /**
     * Scope pour filtrer les scans avec biens présents
     */
    public function scopePresents(Builder $query): Builder
    {
        return $query->where('statut_scan', 'present');
    }

    /**
     * Scope pour filtrer les scans avec biens déplacés
     */
    public function scopeDeplaces(Builder $query): Builder
    {
        return $query->where('statut_scan', 'deplace');
    }

    /**
     * Scope pour filtrer les scans avec biens absents
     */
    public function scopeAbsents(Builder $query): Builder
    {
        return $query->where('statut_scan', 'absent');
    }

    /**
     * Scope pour filtrer par localisation réelle
     */
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

        return $this->bien->localisation_id === $this->localisation_reelle_id;
    }

    /**
     * Vérifie si le bien est déformé (déplacé ou absent)
     */
    public function getIsDeformeAttribute(): bool
    {
        return in_array($this->statut_scan, ['deplace', 'absent']);
    }

    /**
     * Retourne le code inventaire (Gesimmo ou Bien)
     * Compatible PWA (bien_id = NumOrdre) et workflow classique
     */
    public function getCodeInventaireAttribute(): string
    {
        if ($this->gesimmo) {
            return 'GS' . $this->gesimmo->NumOrdre;
        }
        return $this->bien?->code_inventaire ?? 'N/A';
    }

    /**
     * Retourne la désignation (Gesimmo ou Bien)
     */
    public function getDesignationAttribute(): string
    {
        if ($this->gesimmo && $this->gesimmo->designation) {
            return $this->gesimmo->designation->designation ?? 'N/A';
        }
        return $this->bien?->designation ?? 'N/A';
    }

    /**
     * Retourne la localisation du bien (pour affichage)
     */
    public function getLocalisationCodeAttribute(): ?string
    {
        if ($this->gesimmo && $this->gesimmo->emplacement && $this->gesimmo->emplacement->localisation) {
            return $this->gesimmo->emplacement->localisation->CodeLocalisation ?? $this->gesimmo->emplacement->localisation->Localisation;
        }
        return $this->bien?->localisation?->code ?? null;
    }

    /**
     * Retourne la valeur d'acquisition (Gesimmo n'a pas de valeur)
     */
    public function getValeurAcquisitionAttribute(): float
    {
        return (float) ($this->bien?->valeur_acquisition ?? 0);
    }
}

