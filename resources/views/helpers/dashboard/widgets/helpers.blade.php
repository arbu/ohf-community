@php
    $links = [
        [
            'url' => route('people.helpers.index'),
            'title' =>  Auth::user()->can('manage-helpers') ? __('app.manage') : __('app.view'),
            'icon' => Auth::user()->can('manage-helpers') ? 'edit' : 'search',
            'authorized' => Auth::user()->can('list', App\Models\Helpers\Helper::class),
        ],
    ];
@endphp

@extends('dashboard.widgets.base')

@section('widget-title', __('helpers.helpers'))

@section('widget-content')
    <div class="card-body pb-2">
        <p>
            @lang('people.we_have_n_helpers', [
                'active' => $active,
            ])<br>
            @lang('people.n_helpers_on_waiting_list', [
                'num' => $applicants,
            ])<br>
        </p>
    </div>
@endsection
