<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(OfficeSchedule::class);
    }
}
