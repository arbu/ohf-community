@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')
    @php
        $types = [
            'current_trips' => $current,
            'upcoming_trips' => $upcoming,
            'past_trips' => $past,
        ];
    @endphp
    {{-- <ul class="nav nav-tabs tab-remember" id="volunteerTripsTabNav" role="tablist"> --}}
        {{-- @foreach($types as $type => $trips)
            @if(count($trips) > 0) --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" id="{{ $type }}-tab" data-toggle="tab" href="#{{ $type }}" role="tab" aria-controls="{{ $type }}" aria-selected="true"> --}}
                        {{-- @lang('volunteering.' . $type)
                        @if($type == 'current_trips')
                            <span class="badge badge-secondary">{{ count($trips) }}</span>
                        @elseif($type == 'upcoming_trips')
                            <span class="badge badge-secondary">{{ count($trips) }}</span>
                        @endif --}}
                    {{-- </a>
                </li> --}}
            {{-- @endif
        @endforeach --}}
    {{-- </ul> --}}
    {{-- <div class="tab-content" id="volunteerTripsTabContent"> --}}
        @foreach($types as $type => $trips)
            @if(count($trips) > 0)
                <h4 class="my-2">
                    @lang('volunteering.' . $type)
                </h4>
    {{-- <div class="tab-pane fade" id="{{ $type }}" role="tabpanel" aria-labelledby="{{ $type }}-tab"> --}}
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
            {{-- </div> --}}
        @endforeach
    {{-- </div> --}}

    @if($current->isEmpty() && $upcoming->isEmpty() && $future->isEmpty())
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_trips_found')
        </div>
	@endif
	
@endsection
