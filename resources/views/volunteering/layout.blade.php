@extends('layouts.tabbed_view', [ 
    'nav_elements' => [
        [
            'url' => route('volunteering.volunteers.index'),
            'label' => __('volunteering.volunteers'),
            'icon' => 'address-book-o',
            'active' => function($currentRouteName) {
                return $currentRouteName == 'volunteering.volunteers.index';
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
            'icon' => 'wrench',
            'active' => function($currentRouteName) {
                return $currentRouteName == 'volunteering.jobs.index';
            },
            'authorized' => true, // TODO
        ]
    ] 
])