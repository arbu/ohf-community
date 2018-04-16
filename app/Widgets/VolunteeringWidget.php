<?php

namespace App\Widgets;

use App\Volunteer;
use App\VolunteerTrip;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VolunteeringWidget implements Widget
{
    function authorize(): bool
    {
        return Auth::user()->can('list', Volunteer::class)
            && Auth::user()->can('list', VolunteerTrip::class);
    }

    function view(): string
    {
        return 'dashboard.widgets.volunteering';
    }

    function args(): array {
        return [
             'volunteers' => Volunteer
                ::count(),
             'applications' => VolunteerTrip
                ::where('status', 'applied')->count(),
             'active' => VolunteerTrip
                ::whereDate('arrival', '<=', Carbon::today())
                ->where('status', 'approved')
                ->where(function($q){
                    $q->whereDate('departure', '>=', Carbon::today())
                        ->orWhereNull('departure');
                }),
        ];
    }
}