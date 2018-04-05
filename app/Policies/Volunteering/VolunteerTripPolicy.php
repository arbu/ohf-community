<?php

namespace App\Policies\Volunteering;

use App\User;
use App\VolunteerTrip;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerTripPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can list volunteerTrips.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->hasPermission('volunteering.volunteers.manage');
    }

    /**
     * Determine whether the user can view the volunteerTrip.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return mixed
     */
    public function view(User $user, VolunteerTrip $volunteerTrip)
    {
        return $user->hasPermission('volunteering.volunteers.manage');
    }

    /**
     * Determine whether the user can create volunteerTrips.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('volunteering.volunteers.manage');
    }

    /**
     * Determine whether the user can update the volunteerTrip.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return mixed
     */
    public function update(User $user, VolunteerTrip $volunteerTrip)
    {
        return $user->hasPermission('volunteering.volunteers.manage');
    }

    /**
     * Determine whether the user can delete the volunteerTrip.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return mixed
     */
    public function delete(User $user, VolunteerTrip $volunteerTrip)
    {
        return $user->hasPermission('volunteering.volunteers.manage');
    }
}
