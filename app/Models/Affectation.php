<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affectation extends Model
{
    use HasFactory;

    protected $table = 'affectation';
    protected $primaryKey = 'idAffectation';
    public $timestamps = false;

    protected $fillable = ['Affectation', 'CodeAffectation'];

    /**
     * RELATIONS
     */

    /**
     * Relation avec les emplacements
     */
    public function emplacements(): HasMany
    {
        return $this->hasMany(Emplacement::class, 'idAffectation', 'idAffectation');
    }
}
