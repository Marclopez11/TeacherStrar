<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherScheduleEntry extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'time_slot_id',
        'day',
        'subject'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
