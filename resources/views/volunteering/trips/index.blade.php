@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')
    @php
        $types = [
            'applications' => $applied,
            'current_trips' => $current,
            'upcoming_trips' => $upcoming,
            'past_trips' => $past,
            'denied_applications' => $denied,
        ];
    @endphp
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
            @foreach($types as $type => $trips)
                @if(count($trips) > 0)
                    <thead>
                        <tr>
                            <td colspan="5" class="pt-4">
                                <h5>@lang('volunteering.' . $type) 
                                @if($type == 'applications')
                                    <span class="badge badge-warning">{{ count($trips) }}</span></h5>
                                @elseif($type == 'current_trips')
                                    <span class="badge badge-success">{{ count($trips) }}</span></h5>
                                @elseif($type == 'upcoming_trips')
                                    <span class="badge badge-info">{{ count($trips) }}</span></h5>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('volunteering.arrival')</th>
                            <th>@lang('volunteering.departure')</th>
                            <th class="d-none d-sm-table-cell">@lang('volunteering.duration_days')</th>
                            <th>@lang('volunteering.volunteer')</th>
                            <th>@lang('volunteering.job')</th>
                        </tr>                
                    </thead>
                    <tbody>
                        @foreach ($trips as $trip)
                            <tr>
                                <td>
                                    <a href="{{ route('volunteering.trips.show', $trip) }}">
                                        {{ $trip->arrival->toDateString() }}
                                    </a>
                                </td>
                                <td>{{ optional($trip->departure)->toDateString() ?? __('app.unspecified') }}</td>
                                <td class="d-none d-sm-table-cell">{{ $trip->duration ?? __('app.unspecified') }}</td>
                                <td>
                                    <a href="{{ route('volunteering.volunteers.show', $trip->volunteer) }}">
                                        {{ $trip->volunteer->name }}, {{ $trip->volunteer->date_of_birth }}, {{ $trip->volunteer->nationality }}
                                    </a>
                                </td>
                                <td>
                                    @isset($trip->job)
                                        <a href="{{ route('volunteering.jobs.show', $trip->job) }}">
                                            {{ $trip->job->title[App::getLocale()] }}
                                        </a>
                                    @else
                                        @lang('app.unspecified')
                                    @endisset
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            @endforeach
        </table>
    </div>

    @if($current->isEmpty() && $upcoming->isEmpty() && $upcoming->isEmpty())
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_trips_found')
        </div>
	@endif
	
@endsection
