@extends('layouts.app')

@section('title', __('volunteering.trip_details'))

@section('content')

    <a href="{{ route('volunteering.volunteers.show', $trip->volunteer) }}">{{ $trip->volunteer->name }}</a>, {{ $trip->volunteer->date_of_birth }}, {{ $trip->volunteer->nationality }}

    {{ $trip->arrival }} 
    @isset($trip->departure)
        - {{ $trip->departure }} ({{ $trip->duration }} @lang('app.days'))
    @else
        @lang('volunteering.departure_date_unspecified')
    @endif

    @isset($trip->job)
        <a href="{{ route('volunteering.jobs.show', $trip->job) }}">
            {{ $trip->job->title[App::getLocale()] }}
        </a>
    @else
        @lang('app.unspecified')
    @endisset

    {{ $trip->remarks }}
    
    @if($trip->need_accommodation)
        @lang('volunteering.needs_accommodation')
    @endif

@endsection
