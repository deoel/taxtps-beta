<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Declaration extends Model
{
    protected $fillable = [
        'numero_dcl',
        'customs_office_id',
        'importateur',
        'code_sh',
        'montant_cif',
        'taxe_due',
        'statut',
        'gps_validated',
        'latitude',
        'longitude',
        'agent_id'
    ];
}