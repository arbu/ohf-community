<?php

namespace App\Http\ViewComposers;

use App\Person;
use App\Role;
use App\Task;
use App\User;
use App\Donor;
use App\Volunteer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NavigationComposer {

	/**
     * Create the composer.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $num_open_tasks = Auth::user()->tasks()->open()->count();
            $nav = [
                [
                    'route' => 'home',
                    'caption' => __('app.dashboard'),
                    'icon' => 'home',
                    'active' => '/',
                    'authorized' => true
                ],
                [
                    'route' => 'people.index',
                    'caption' => __('people.people'),
                    'icon' => 'users',
                    'active' => 'people*',
                    'authorized' => Auth::user()->can('list', Person::class)
                ],
                [
                    'route' => 'bank.index',
                    'caption' => __('people.bank'),
                    'icon' => 'bank',
                    'active' => 'bank*',
                    'authorized' => Gate::allows('view-bank-index')
                ],
                [
                    'route' => 'volunteering.volunteers.index',
                    'caption' => __('volunteering.volunteers'),
                    'icon' => 'heart',
                    'active' => 'volunteering*',
                    'authorized' => Auth::user()->can('list', Volunteer::class)
                ],                
                [
                    'route' => 'logistics.index',
                    'caption' => 'Logistics',
                    'icon' => 'spoon',
                    'active' => 'logistics*',
                    'authorized' => Gate::allows('use-logistics')
                ],
                [
                    'route' => 'donors.index',
                    'caption' => __('donations.donations'),
                    'icon' => 'handshake-o',
                    'active' => 'donations/donors*',
                    'authorized' => Auth::user()->can('list', Donor::class),
                ],
                [
                    'route' => 'calendar',
                    'caption' => 'Calendar',
                    'icon' => 'calendar',
                    'active' => 'calendar*',
                    'authorized' => Gate::allows('view-calendar'),
                ],                
                [
                    'route' => 'tasks',
                    'caption' => 'Tasks',
                    'icon' => 'tasks',
                    'active' => 'tasks*',
                    'authorized' => Auth::user()->can('list', Task::class),
                    'badge' => $num_open_tasks > 0 ? $num_open_tasks : null
                ],
                [
                    'route' => 'reporting.index',
                    'caption' => __('app.reporting'),
                    'icon' => 'bar-chart',
                    'active' => 'reporting*',
                    'authorized' => Gate::allows('view-reports'),
                ],
                [
                    'route' => 'users.index',
                    'caption' => __('app.users_and_roles'),
                    'icon' => 'users',
                    'active' => ['users*', 'roles*'],
                    'authorized' => Auth::user()->can('list', User::class) || Auth::user()->can('list', Role::class)
                ],
                [
                    'route' => 'logviewer.index',
                    'caption' => __('app.logviewer'),
                    'icon' => 'file-text-o',
                    'active' => 'logviewer*',
                    'authorized' => Gate::allows('view-logs'),
                ],
            ];
            $view->with('nav', $nav);
        }
    }
}
