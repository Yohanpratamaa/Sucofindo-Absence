<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSchedule extends Model
{
    protected $fillable = [
        'office_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'office_working_hours_id');
    }

    // Method untuk mendapatkan jadwal berdasarkan hari
    public static function getScheduleForDay($officeId, $dayOfWeek)
    {
        return self::where('office_id', $officeId)
                   ->where('day_of_week', $dayOfWeek)
                   ->first();
    }
}
