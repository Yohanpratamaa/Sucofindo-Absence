<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeSchedule extends Model
{
    protected $fillable = [
        'office_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];
}
