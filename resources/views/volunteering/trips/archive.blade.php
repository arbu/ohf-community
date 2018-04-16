@extends('layouts.app')

@section('title', __('app.archive'))

@section('content')
    @include('volunteering.trips.table')
@endsection
