@extends('layouts.app')

@section('title', 'Volunteers')

@section('content')
    <div id="app" class="mb-3">
        <volunteer-app></volunteer-app>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/volunteers.js') }}?v={{ $app_version }}"></script>
@endsection