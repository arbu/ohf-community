<?php

namespace App\Policies\Volunteering;

use App\User;
use App\VolunteerJob;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerJobPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can list volunteerJobs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the volunteerJob.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJob  $volunteerJob
     * @return mixed
     */
    public function view(User $user, VolunteerJob $volunteerJob)
    {
        return $volunteerJob->enabled;
    }

    /**
     * Determine whether the user can create volunteerJobs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }

    /**
     * Determine whether the user can update the volunteerJob.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJob  $volunteerJob
     * @return mixed
     */
    public function update(User $user, VolunteerJob $volunteerJob)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }

    /**
     * Determine whether the user can delete the volunteerJob.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJob  $volunteerJob
     * @return mixed
     */
    public function delete(User $user, VolunteerJob $volunteerJob)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }
}
