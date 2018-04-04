<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class VolunteerProfileWidget implements Widget
{
    function authorize(): bool
    {
        $user = Auth::user();
        return $user->volunteer == null 
            && $user->roles()->count() == 0
            && !$user->isSuperAdmin();
    }

    function view(): string
    {
        return 'dashboard.widgets.volunteer-profile';
    }

    function args(): array {
        return [ ];
    }
}