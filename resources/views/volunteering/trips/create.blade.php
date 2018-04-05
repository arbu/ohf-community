@extends('layouts.app')

@section('title', __('volunteering.register_trip'))

@section('content')

    @if(count($jobs) > 0)
        {!! Form::open(['route' => ['volunteering.trips.store'], 'method' => 'post']) !!}
            {{ Form::bsText('volunteerSearch', null, [ 'required', 'placeholder' => __('volunteering.search_volunteer'), 'rel' => 'autocomplete', 'data-autocomplete-url' => route('volunteering.volunteers.filter'), 'data-autocomplete-update' => '#volunteer' ], __('volunteering.volunteer')) }}
            {{ Form::hidden('volunteer', null, [ 'id' => 'volunteer' ]) }}
            <div class="form-row">
                <div class="col-sm">
                    {{ Form::bsDate('arrival', Carbon\Carbon::today()->toDateString(), [ 'id' => 'arrival', 'required' ], __('volunteering.arrival')) }}
                </div>
                <div class="col-sm">
                    {{ Form::bsDate('departure', Carbon\Carbon::today()->addWeeks(2)->toDateString(), [ 'id' => 'departure' ], __('volunteering.departure')) }}
                </div>
                <div class="col-sm">
                    {{ Form::bsSelect('job', $jobs, null, [ 'required' ], __('volunteering.job')) }}
                </div>
            </div>
            <p>{{ Form::bsCheckbox('need_accommodation', null, [], __('volunteering.needs_accommodation')) }}</p>
            {{ Form::bsTextarea('remarks', null, [ ], __('app.remarks')) }}
            <p>{{ Form::bsSubmitButton(__('app.register')) }}</p>
        {!! Form::close() !!}
    @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_jobs_found')
        </div>
        @can('create', \App\VolunteerJob::class)
            <p><a href="{{ route('volunteering.jobs.create') }}" class="btn btn-primary">@icon(plus-circle) @lang('volunteering.create_job')</a></p>
        @endcan
    @endif

@endsection

@section('script')
    $(function(){
        var arrival = $('#arrival');
        var departure = $('#departure');

        arrival.on('change', function(){
            const arrivalDay = moment(arrival.val());
            const departureDay = moment(departure.val());

            departure.val(moment(arrivalDay).add(14, 'days').format('YYYY-MM-DD'));
            departure.attr('min', moment(arrivalDay).add(1, 'days').format('YYYY-MM-DD'));
        });

        departure.attr('min', moment(arrival.val()).add(1, 'days').format('YYYY-MM-DD'));
    });
@endsection

@section('footer')
    <script src='{{ asset('js/moment-with-locales.min.js') }}'></script>
@endsection