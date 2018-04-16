@extends('layouts.app')

@section('title', __('volunteering.trip_details'))

@section('content')

    <ul class="list-group list-group-flush mb-2">
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('app.status')</strong>
                </div>
                <div class="col-sm {{ status_text_color($trip->status) }}">
                    @lang('app.' . $trip->status)
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('volunteering.volunteer')</strong>
                </div>
                <div class="col-sm">
                    <a href="{{ route('volunteering.volunteers.show', $trip->volunteer) }}">
                        {{ $trip->volunteer->name }}, {{ $trip->volunteer->date_of_birth }}, {{ $trip->volunteer->nationality }}
                    </a>    
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('volunteering.arrival')</strong>
                </div>
                <div class="col-sm">
                    {{ $trip->arrival->toDateString() }} 
                    @unless($trip->hasArrived)
                        <small class="text-muted">{{ trans_choice('volunteering.in_n_days', $trip->arrivesIn, [ 'days' => $trip->arrivesIn ]) }}</small>
                    @endunless
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('volunteering.departure')</strong>
                </div>
                <div class="col-sm">
                    @isset($trip->departure)
                        {{ $trip->departure->toDateString() }}
                        @unless($trip->hasDeparted || !$trip->hasArrived)
                            <small class="text-muted">{{ trans_choice('volunteering.in_n_days', $trip->departsIn, [ 'days' => $trip->departsIn ]) }}</small>
                        @endunless
                    @else
                        @lang('volunteering.departure_date_unspecified')
                    @endisset
                </div>
            </div>
        </li>
        @isset($trip->duration)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm">
                        <strong>@lang('app.duration')</strong>
                    </div>
                    <div class="col-sm">
                        {{ $trip->duration }} {{ trans_choice('app.day_days', $trip->duration) }}
                    </div>
                </div>
            </li>
        @endisset
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('volunteering.job')</strong>
                </div>
                <div class="col-sm">
                    @isset($trip->job)
                        <a href="{{ route('volunteering.jobs.show', $trip->job) }}">
                            {{ $trip->job->title[App::getLocale()] }}
                        </a>
                    @else
                        @lang('app.unspecified')
                    @endisset
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm">
                    <strong>@lang('volunteering.needs_accommodation')</strong>
                </div>
                <div class="col-sm">
                    @if($trip->need_accommodation)
                        @lang('app.yes')
                    @else
                        @lang('app.no')
                    @endif
                </div>
            </div>
        </li>
        @isset($trip->remarks)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm">
                        <strong>@lang('app.remarks')</strong>
                    </div>
                    <div class="col-sm">
                        {{ $trip->remarks }}
                    </div>
                </div>
            </li>
        @endisset
    </ul>

@endsection
