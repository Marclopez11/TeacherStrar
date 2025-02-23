<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'start_time',
        'end_time',
        'is_break',
        'order'
    ];

    protected $casts = [
        'is_break' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'order' => 'integer'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
