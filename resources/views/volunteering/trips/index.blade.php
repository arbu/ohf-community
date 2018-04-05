@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')

    @foreach([
        __('volunteering.current_trips') => $current,
        __('volunteering.upcoming_trips') => $upcoming,
        __('volunteering.past_trips') => $past,
    ] as $title => $trips)
        @if( ! $trips->isEmpty() )
            <h4>{{ $title }}</h4>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>@lang('volunteering.volunteer')</th>
                            <th>@lang('volunteering.arrival')</th>
                            <th>@lang('volunteering.departure')</th>
                            <th>@lang('volunteering.duration_days')</th>
                            <th>@lang('volunteering.job')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trips as $trip)
                            <tr>
                                <td>
                                    <a href="{{ route('volunteering.volunteers.show', $trip->volunteer) }}">{{ $trip->volunteer->name }}</a>
                                </td>
                                <td>{{ $trip->arrival }}</td>
                                <td>{{ $trip->departure ?? __('app.unspecified') }}</td>
                                <td>{{ $trip->duration ?? __('app.unspecified') }}</td>
                                <td>
                                    @isset($trip->job)
                                        <a href="{{ route('volunteering.jobs.show', $trip->job) }}">{{ $trip->job->title[App::getLocale()] }}</a>
                                    @else
                                        @lang('app.unspecified')
                                    @endisset
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    @if($current->isEmpty() && $upcoming->isEmpty() && $future->isEmpty())
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_trips_found')
        </div>
	@endif
	
@endsection
