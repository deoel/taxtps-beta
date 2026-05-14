<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomsOffice extends Model
{
    protected $fillable = [
        'province_id',
        'code_bureau',
        'name',
        'latitude',
        'longitude',
        'gps_required'
    ];

    protected $casts = [
        'gps_required' => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }
}