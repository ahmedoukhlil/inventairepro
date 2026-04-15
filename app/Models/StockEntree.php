<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StockEntree extends Model
{
    use HasFactory;

    protected $table = 'stock_entrees';

    protected $fillable = [
        'date_entree',
        'reference_commande',
        'produit_id',
        'fournisseur_id',
        'quantite',
        'observations',
        'created_by',
        'groupe_id',
        'numero_entree',
    ];

    /**
     * Génère le prochain numéro d'entrée pour l'année de la date donnée.
     * Format : E-001/2026
     * Doit être appelé à l'intérieur d'une transaction pour éviter les doublons.
     */
    public static function genererNumeroEntree(string $dateEntree): string
    {
        $annee = \Carbon\Carbon::parse($dateEntree)->year;

        $dernierNumero = static::whereYear('date_entree', $annee)
            ->whereNotNull('numero_entree')
            ->lockForUpdate()
            ->max(DB::raw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(numero_entree, \'-\', -1), \'/\', 1) AS UNSIGNED)'));

        $prochain = ($dernierNumero ?? 0) + 1;

        return 'E-' . str_pad($prochain, 3, '0', STR_PAD_LEFT) . '/' . $annee;
    }

    protected $casts = [
        'date_entree' => 'date',
        'quantite' => 'integer',
    ];

    /**
     * RELATIONS
     */

    /**
     * Relation avec le produit
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(StockProduit::class, 'produit_id');
    }

    /**
     * Relation avec le fournisseur
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(StockFournisseur::class, 'fournisseur_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé l'entrée
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'idUser');
    }

    /**
     * EVENTS
     */

    /**
     * Événement déclenché après la création d'une entrée
     * Met à jour automatiquement le stock du produit
     */
    protected static function booted(): void
    {
        static::created(function (StockEntree $entree) {
            // Mettre à jour le stock_actuel du produit
            $produit = $entree->produit;
            if ($produit) {
                $produit->ajouterStock($entree->quantite);
            }
        });

        static::deleting(function (StockEntree $entree) {
            // Retirer la quantité du stock si l'entrée est supprimée
            $produit = $entree->produit;
            if ($produit) {
                $produit->retirerStock($entree->quantite);
            }
        });
    }

    /**
     * ACCESSORS
     */

    /**
     * Nom du créateur
     */
    public function getNomCreateurAttribute(): string
    {
        return $this->createur ? ($this->createur->display_name ?? 'Inconnu') : 'Système';
    }
}
