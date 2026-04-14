<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockSortie extends Model
{
    use HasFactory, Auditable;

    protected $table = 'stock_sorties';

    protected $fillable = [
        'date_sortie',
        'produit_id',
        'demandeur_id',
        'quantite',
        'observations',
        'created_by',
        'groupe_id',
        'numero_commande',
    ];

    /**
     * Génère le prochain numéro de commande pour l'année de la date donnée.
     * Format : 001/2026
     * Doit être appelé à l'intérieur d'une transaction pour éviter les doublons.
     */
    public static function genererNumeroCommande(string $dateSortie): string
    {
        $annee = \Carbon\Carbon::parse($dateSortie)->year;

        $dernierNumero = static::whereYear('date_sortie', $annee)
            ->whereNotNull('numero_commande')
            ->lockForUpdate()
            ->max(DB::raw('CAST(SUBSTRING_INDEX(numero_commande, \'/\', 1) AS UNSIGNED)'));

        $prochain = ($dernierNumero ?? 0) + 1;

        return str_pad($prochain, 3, '0', STR_PAD_LEFT) . '/' . $annee;
    }

    protected $casts = [
        'date_sortie'  => 'date',
        'quantite'     => 'integer',
        'observations' => 'encrypted', // Chiffré au repos (APP_KEY requis)
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
     * Relation avec le demandeur
     */
    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(StockDemandeur::class, 'demandeur_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé la sortie
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'idUser');
    }

    /**
     * EVENTS
     */

    /**
     * Événement déclenché après la création d'une sortie
     * Met à jour automatiquement le stock du produit
     */
    protected static function booted(): void
    {
        static::creating(function (StockSortie $sortie) {
            // Décrément ATOMIQUE : on empêche toute divergence entre `stock_sorties`
            // et `stock_produits.stock_actuel` (cas concurrence).
            $affected = DB::table('stock_produits')
                ->where('id', $sortie->produit_id)
                ->where('stock_actuel', '>=', $sortie->quantite)
                ->decrement('stock_actuel', $sortie->quantite);

            if ($affected === 0) {
                $stock = DB::table('stock_produits')
                    ->where('id', $sortie->produit_id)
                    ->value('stock_actuel');

                throw new \Exception(
                    'Stock insuffisant. Stock disponible : ' . ($stock ?? 0) . ', demandé : ' . $sortie->quantite
                );
            }
        });

        // Ne PAS utiliser `created` pour modifier le stock : le décrément atomique
        // est déjà fait dans `creating`.

        static::deleting(function (StockSortie $sortie) {
            // Réajouter la quantité au stock si la sortie est supprimée
            DB::table('stock_produits')
                ->where('id', $sortie->produit_id)
                ->increment('stock_actuel', $sortie->quantite);
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
