@extends('layouts.tabbed_view', [ 
    'nav_elements' => [
        [
            'url' => route('volunteers.index'),
            'label' => __('volunteering.volunteers'),
            'icon' => 'user',
            'active' => function($currentRouteName) {
                return $currentRouteName == 'volunteers.index';
            },
            'authorized' => Auth::user()->can('list', \App\Volunteer::class),
        ],
        [
            'url' => route('volunteering.trips.index'),
            'label' => __('volunteering.trips'),
            'icon' => 'calendar',
            'active' => function($currentRouteName) {
                return $currentRouteName == 'volunteering.trips.index';
            },
            'authorized' => true, // TODO
        ],
        [
            'url' => route('volunteering.jobs.index'),
            'label' => __('volunteering.jobs'),
            'icon' => 'list',
            'active' => function($currentRouteName) {
                return $currentRouteName == 'volunteering.jobs.index';
            },
            'authorized' => true, // TODO
        ]
    ] 
])