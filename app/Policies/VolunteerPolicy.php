<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can list volunteers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->hasPermission('volunteers.manage');
    }

    /**
     * Determine whether the user can view the volunteer.
     *
     * @param  \App\User  $user
     * @param  \App\Volunteer  $volunteer
     * @return mixed
     */
    public function view(User $user, Volunteer $volunteer)
    {
        return $user->hasPermission('volunteers.managee');
    }

    /**
     * Determine whether the user can create volunteers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('volunteers.managee');
    }

    /**
     * Determine whether the user can update the volunteer.
     *
     * @param  \App\User  $user
     * @param  \App\Volunteer  $volunteer
     * @return mixed
     */
    public function update(User $user, Volunteer $volunteer)
    {
        return $user->hasPermission('volunteers.manage');
    }

    /**
     * Determine whether the user can delete the volunteer.
     *
     * @param  \App\User  $user
     * @param  \App\Volunteer  $volunteer
     * @return mixed
     */
    public function delete(User $user, Volunteer $volunteer)
    {
        return $user->hasPermission('volunteers.manage');
    }
}
