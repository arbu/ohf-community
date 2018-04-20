@extends('layouts.app')

@section('title', __('app.calendar'))

@section('head-meta')
    <link href="{{ asset('css/fullcalendar.min.css') }}?v={{ $app_version }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/scheduler.min.css') }}?v={{ $app_version }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div id="calendar" class="mb-4"></div>
@endsection

@section('script')
    var locale = '{{ App::getLocale() }}';
    var listEventsUrl = '{{ route('volunteering.trips.calendar.events') }}';
    var listResourcesUrl = '{{ route('volunteering.trips.calendar.resources') }}';
    var resourceLabel = '@lang('volunteering.jobs')';
    var todayLabel = '@lang('app.today')';
    var weekLabel = '@lang('app.week')';
    var monthLabel = '@lang('app.month')';
@endsection

@section('footer')
    <script src='{{ asset('js/moment-with-locales.min.js') }}'></script>
    <script src='{{ asset('js/fullcalendar.min.js') }}'></script>
    <script src='{{ asset('js/scheduler.min.js') }}'></script>
    <script src="{{ asset('js/volunteering.js') }}?v={{ $app_version }}"></script>
@endsection
