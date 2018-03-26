@extends('layouts.app')

@section('title', __('volunteering.volunteers'))

@section('buttons')
    @can('create', App\Volunteer::class)
        <a href="{{ route('volunteers.create') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Register</a>
    @endcan
@endsection

@section('content')

    @if( ! $volunteers->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.name')</th>
                    <th>@lang('app.address')</th>
                    <th>@lang('people.date_of_birth')</th>
                    <th>@lang('app.email')</th>
                    <th>@lang('app.phone')</th>
                    <th>@lang('volunteering.whatsapp')</th>
                    <th>@lang('volunteering.skype')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($volunteers as $volunteer)
                    <tr>
                        <td>
                            <a href="{{ route('volunteers.show', $volunteer) }}" title="View volunteer">{{ $volunteer->user->name }}</a>
                        </td>
                        <td>{{ $volunteer->address }}</td>
                        <td>{{ $volunteer->date_of_birth }} ({{ $volunteer->age }})</td>
                        <td>
                            <a href="mailto:{{ $volunteer->user->email }}"><i class="fa fa-envelope-o"></i></a>
                            {{ $volunteer->user->email }}
                        </td>
                        <td>
                            @if ( isset( $volunteer->phone ) )
                                <a href="tel:{{ $volunteer->phone }}"><i class="fa fa-phone"></i></a>
                                {{ $volunteer->phone }} 
                            @endif
                        </td>
                        <td>
                            @if ( isset( $volunteer->whatsapp ) )
                                <a href="whatsapp:{{ $volunteer->whatsapp }}"><i class="fa fa-whatsapp"></i></a>
                                {{ $volunteer->whatsapp }}
                            @endif
                        </td>
                        <td>
                            @if ( isset( $volunteer->skype ) )
                                <a href="skype:{{ $volunteer->skype }}?call"><i class="fa fa-skype"></i></a>
                                {{ $volunteer->skype }} 
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $volunteers->links() }}
 
    @else
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> @lang('volunteering.no_volunteers_found')
        </div>
	@endif
	
@endsection