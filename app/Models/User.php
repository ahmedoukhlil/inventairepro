<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Nom de la table
     */
    protected $table = 'users';

    /**
     * Clé primaire personnalisée
     */
    protected $primaryKey = 'idUser';

    /**
     * Désactiver les timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'users',  // Nom d'utilisateur (selon immos.md)
        'mdp',    // Mot de passe (selon immos.md)
        'role',
    ];

    /**
     * Nom de la colonne pour l'authentification (utilisé par Laravel)
     * Note: getAuthIdentifierName() doit retourner le nom de la colonne utilisée pour identifier l'utilisateur
     * mais Auth::id() retourne toujours la valeur de la clé primaire
     */
    public function getAuthIdentifierName()
    {
        return $this->primaryKey; // Retourner 'idUser' pour que Auth::id() fonctionne correctement
    }

    /**
     * Récupérer l'identifiant pour l'authentification (retourne la clé primaire)
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->primaryKey); // Retourner idUser
    }

    /**
     * Récupérer le mot de passe pour l'authentification
     */
    public function getAuthPassword()
    {
        return $this->getAttribute('mdp');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mdp',  // Mot de passe (selon immos.md)
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Pas de cast 'hashed' car les mots de passe peuvent être en clair dans la base existante
        ];
    }

    /**
     * RELATIONS
     */

    /**
     * Relation avec les immobilisations (Gesimmo) créées par l'utilisateur
     * Note: Cette relation peut nécessiter une colonne user_id dans la table gesimmo
     */
    public function immobilisations(): HasMany
    {
        return $this->hasMany(Gesimmo::class, 'user_id', 'idUser');
    }

    /**
     * Relation avec les inventaires créés par l'utilisateur
     */
    public function inventairesCreated(): HasMany
    {
        return $this->hasMany(Inventaire::class, 'created_by');
    }

    /**
     * Relation avec les inventaires clôturés par l'utilisateur
     */
    public function inventairesClosed(): HasMany
    {
        return $this->hasMany(Inventaire::class, 'closed_by');
    }

    /**
     * Relation avec les scans d'inventaire effectués par l'utilisateur
     */
    public function inventaireScans(): HasMany
    {
        return $this->hasMany(InventaireScan::class, 'user_id');
    }

    /**
     * Relation avec les inventaire_localisations assignées à l'utilisateur
     */
    public function inventaireLocalisations(): HasMany
    {
        return $this->hasMany(InventaireLocalisation::class, 'user_id');
    }

    /**
     * SCOPES
     */

    /**
     * Scope pour filtrer les administrateurs
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope pour filtrer les agents
     */
    public function scopeAgents(Builder $query): Builder
    {
        return $query->where('role', 'agent');
    }

    /**
     * Scope pour filtrer par rôle
     */
    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * ACCESSORS
     */

    /**
     * Retourne le nom du rôle en français
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin', 'superuser' => 'Administrateur',
            'agent', 'immobilisation' => 'Agent',
            'stock' => 'Stock',
            default => 'Non défini',
        };
    }

    /**
     * METHODS
     */

    /**
     * Vérifie si l'utilisateur est administrateur
     * Accepte 'admin' et 'superuser' comme administrateurs
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superuser']);
    }

    /**
     * Vérifie si l'utilisateur est agent
     * Accepte 'agent' et 'immobilisation' comme agents
     */
    public function isAgent(): bool
    {
        return in_array($this->role, ['agent', 'immobilisation']);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les inventaires
     */
    public function canManageInventaire(): bool
    {
        return in_array($this->role, ['admin', 'superuser', 'agent', 'immobilisation']);
    }
}
