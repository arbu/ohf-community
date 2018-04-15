@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')
    @include('volunteering.trips.table')
@endsection
