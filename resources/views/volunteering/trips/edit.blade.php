@extends('layouts.app')

@section('title', __('volunteering.edit_trip'))

@section('content')

    {!! Form::model($trip, ['route' => ['volunteering.trips.update', $trip], 'method' => 'put']) !!}

        @if(count($jobs) > 0)
            {!! Form::open(['route' => ['volunteering.trips.store'], 'method' => 'post']) !!}
                <p>@lang('volunteering.volunteer'):</p>
                <p>{{ $trip->volunteer->name }}, {{ $trip->volunteer->date_of_birth }}, {{ $trip->volunteer->nationality }}</p>
                <div class="form-row">
                    <div class="col-sm">
                        {{ Form::bsDate('arrival', null, [ 'id' => 'arrival', 'required' ], __('volunteering.arrival')) }}
                    </div>
                    <div class="col-sm">
                        {{ Form::bsDate('departure', null, [ 'id' => 'departure' ], __('volunteering.departure')) }}
                    </div>
                    <div class="col-sm">
                        {{ Form::bsSelect('job', $jobs, null, [ 'required' ], __('volunteering.job')) }}
                    </div>
                </div>
                <p>{{ Form::bsCheckbox('need_accommodation', 1, null, __('volunteering.needs_accommodation')) }}</p>
                {{ Form::bsTextarea('remarks', null, [ ], __('app.remarks')) }}
                <p>{{ Form::bsSubmitButton(__('app.update')) }}</p>
            {!! Form::close() !!}
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> @lang('volunteering.no_jobs_found')
            </div>
            @can('create', \App\VolunteerJob::class)
                <p><a href="{{ route('volunteering.jobs.create') }}" class="btn btn-primary">@icon(plus-circle) @lang('volunteering.create_job')</a></p>
            @endcan
        @endif

    {!! Form::close() !!}

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
