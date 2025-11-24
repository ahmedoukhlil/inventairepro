<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class InventaireLocalisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventaire_id',
        'localisation_id',
        'date_debut_scan',
        'date_fin_scan',
        'statut',
        'nombre_biens_attendus',
        'nombre_biens_scannes',
        'user_id',
    ];

    protected $casts = [
        'date_debut_scan' => 'datetime',
        'date_fin_scan' => 'datetime',
        'nombre_biens_attendus' => 'integer',
        'nombre_biens_scannes' => 'integer',
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
     * Relation avec la localisation
     */
    public function localisation(): BelongsTo
    {
        return $this->belongsTo(Localisation::class);
    }

    /**
     * Relation avec l'agent assigné
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les scans d'inventaire
     */
    public function inventaireScans(): HasMany
    {
        return $this->hasMany(InventaireScan::class);
    }

    /**
     * SCOPES
     */

    /**
     * Scope pour filtrer les inventaire_localisations en cours
     */
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope pour filtrer les inventaire_localisations terminées
     */
    public function scopeTerminees(Builder $query): Builder
    {
        return $query->where('statut', 'termine');
    }

    /**
     * Scope pour filtrer par agent
     */
    public function scopeByAgent(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * ACCESSORS
     */

    /**
     * Calcule le pourcentage de progression
     */
    public function getProgressionAttribute(): float
    {
        if ($this->nombre_biens_attendus === 0) {
            return 0;
        }

        return round(($this->nombre_biens_scannes / $this->nombre_biens_attendus) * 100, 2);
    }

    /**
     * Calcule le taux de conformité (% de biens présents)
     */
    public function getTauxConformiteAttribute(): float
    {
        $totalScans = $this->inventaireScans()->count();
        
        if ($totalScans === 0) {
            return 0;
        }

        $scansPresents = $this->inventaireScans()
            ->where('statut_scan', 'present')
            ->count();

        return round(($scansPresents / $totalScans) * 100, 2);
    }

    /**
     * METHODS
     */

    /**
     * Démarre le scan de la localisation
     */
    public function demarrer(): bool
    {
        return $this->update([
            'date_debut_scan' => now(),
            'statut' => 'en_cours',
        ]);
    }

    /**
     * Termine le scan de la localisation
     */
    public function terminer(): bool
    {
        return $this->update([
            'date_fin_scan' => now(),
            'statut' => 'termine',
            'nombre_biens_scannes' => $this->inventaireScans()->count(),
        ]);
    }

    /**
     * Calcule et met à jour le nombre de biens attendus dans la localisation
     */
    public function calculerBiensAttendus(): int
    {
        $nombreBiens = $this->localisation->biens()->count();
        
        $this->update(['nombre_biens_attendus' => $nombreBiens]);
        
        return $nombreBiens;
    }
}

