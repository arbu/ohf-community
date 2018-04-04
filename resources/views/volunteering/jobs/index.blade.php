@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')

    @if( ! $jobs->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.title')</th>
                    <th>@lang('app.category')</th>
                    <th class="d-none d-sm-table-cell">@lang('app.order')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                    <tr>
                        <td>
                            @foreach (language()->allowed() as $code => $name)
                                <small class="text-muted d-block d-sm-inline">{{ $name }}:</small> 
                                <a href="{{ route('volunteering.jobs.show', $job) }}">{{ $job->title[$code] ?? '-' }}</a><br>
                            @endforeach
                        </td>
                        <td>
                            @foreach (language()->allowed() as $code => $name)
                                <small class="text-muted d-block d-sm-inline">{{ $name }}:</small> {{ $job->category->title[$code] ?? '-' }}<br>
                            @endforeach
                        </td>
                        <td class="d-none d-sm-table-cell">{{ $job->order }}</td>
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
