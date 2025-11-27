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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'service',
        'actif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    /**
     * RELATIONS
     */

    /**
     * Relation avec les biens créés par l'utilisateur
     */
    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class, 'user_id');
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
     * Scope pour filtrer les utilisateurs actifs
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
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
            'admin' => 'Administrateur',
            'agent' => 'Agent',
            default => 'Non défini',
        };
    }

    /**
     * METHODS
     */

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est agent
     */
    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    /**
     * Vérifie si l'utilisateur peut gérer les inventaires
     */
    public function canManageInventaire(): bool
    {
        return $this->role === 'admin' || $this->role === 'agent';
    }
}
