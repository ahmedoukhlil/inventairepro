<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollecteBienInitiale extends Model
{
    use HasFactory;

    protected $table = 'collecte_biens_initiale';

    protected $fillable = [
        'lot_uid',
        'line_index',
        'emplacement_label',
        'affectation_label',
        'localisation_label',
        'designation',
        'quantite',
        'etat',
        'date_acquisition',
        'observations',
        'transcription_brute',
        'confiance',
        'agent_label',
        'created_by_user_id',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'date_acquisition' => 'integer',
        'confiance' => 'decimal:2',
    ];
}
