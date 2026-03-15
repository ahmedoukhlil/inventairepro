<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorbeilleImmobilisation extends Model
{
    use HasFactory;

    protected $table = 'corbeille_immobilisations';

    protected $fillable = [
        'original_num_ordre',
        'idDesignation',
        'idCategorie',
        'idEtat',
        'idEmplacement',
        'idNatJur',
        'idSF',
        'DateAcquisition',
        'Observations',
        'barcode',
        'designation_label',
        'deleted_reason',
        'deleted_by_user_id',
        'deleted_at',
    ];

    protected $casts = [
        'DateAcquisition' => 'date',
        'deleted_at' => 'datetime',
    ];
}
