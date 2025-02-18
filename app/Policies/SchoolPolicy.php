<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    public function view(User $user, School $school)
    {
        return $user->schools()->where('school_id', $school->id)->exists();
    }

    public function update(User $user, School $school)
    {
        return $user->schools()
            ->where('school_id', $school->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }
}
