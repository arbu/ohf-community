<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class VolunteerProfileWidget implements Widget
{
    function authorize(): bool
    {
        return Auth::user()->volunteer == null;
    }

    function view(): string
    {
        return 'dashboard.widgets.volunteer-profile';
    }

    function args(): array {
        return [ ];
    }
}