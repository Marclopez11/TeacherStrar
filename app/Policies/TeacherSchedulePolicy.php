<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherSchedulePolicy
{
    use HandlesAuthorization;

    public function view(User $user, School $school)
    {
        return $school->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function manage(User $user, School $school)
    {
        return $school->users()
            ->where('user_id', $user->id)
            ->exists();
    }
}
