@extends('layouts.app')

@section('title', __('volunteering.job_categories'))

@section('content')

    @if( ! $categories->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.title')</th>
                    <th class="d-none d-sm-table-cell">@lang('app.order')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            @foreach (language()->allowed() as $code => $name)
                                <small class="text-muted d-block d-sm-inline">{{ $name }}:</small> 
                                <a href="{{ route('volunteering.jobs.categories.edit', $category) }}">{{ $category->title[$code] ?? '-' }}</a><br>
                            @endforeach
                        </td>
                        <td class="d-none d-sm-table-cell">{{ $category->order }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
     @else
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_categories_found')
        </div>
	@endif
	
@endsection
