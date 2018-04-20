@extends('layouts.app')

@section('title', __('volunteering.job_categories'))

@section('content')

    @if( ! $categories->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.title')</th>
                    <th class="d-none d-sm-table-cell fit">@lang('app.order')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            <a href="{{ route('volunteering.jobs.categories.edit', $category) }}">{{ $category->title[App::getLocale()] ?? '-' }}</a><br>
                        </td>
                        <td class="d-none d-sm-table-cell fit">{{ $category->order }}</td>
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
