<?php

namespace App\Policies\Volunteering;

use App\User;
use App\VolunteerJobCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerJobCategoryPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can list volunteerJobCategories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the volunteerJobCategory.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJobCategory  $volunteerJobCategory
     * @return mixed
     */
    public function view(User $user, VolunteerJobCategory $volunteerJobCategory)
    {
        return true;
    }

    /**
     * Determine whether the user can create volunteerJobCategories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }

    /**
     * Determine whether the user can update the volunteerJobCategory.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJobCategory  $volunteerJobCategory
     * @return mixed
     */
    public function update(User $user, VolunteerJobCategory $volunteerJobCategory)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }

    /**
     * Determine whether the user can delete the volunteerJobCategory.
     *
     * @param  \App\User  $user
     * @param  \App\VolunteerJobCategory  $volunteerJobCategory
     * @return mixed
     */
    public function delete(User $user, VolunteerJobCategory $volunteerJobCategory)
    {
        return $user->hasPermission('volunteering.jobs.manage');
    }
}
