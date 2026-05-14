<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'agent_id',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(CustomsOffice::class, 'customs_office_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}