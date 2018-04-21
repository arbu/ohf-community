@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')

    @if( ! $jobs->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.title')</th>
                    <th>@lang('app.category')</th>
                    <th class="d-none d-md-table-cell">@lang('volunteering.available_dates')</th>
                    <th class="d-none d-md-table-cell">@lang('volunteering.minimum_stay')</th>
                    <th class="d-none d-sm-table-cell fit">@lang('app.order')</th>
                    <th class="d-none d-sm-table-cell fit">@lang('app.enabled')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                    <tr @unless($job->enabled)class="text-muted" @endunless>
                        <td>
                            <a href="{{ route('volunteering.jobs.show', $job) }}">{{ $job->title[App::getLocale()] }}</a><br>
                        </td>
                        <td>
                            {{ $job->category->title[App::getLocale()] }}<br>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $job->available_dates[App::getLocale()] }}</td>
                        <td class="d-none d-md-table-cell">{{ $job->minimum_stay[App::getLocale()] }}</td>
                        <td class="d-none d-sm-table-cell fit">{{ $job->order }}</td>
                        <td class="d-none d-sm-table-cell fit">{{ $job->enabled ? __('app.yes') : __('app.no') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
     @else
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_jobs_found')
        </div>
	@endif
	
@endsection
