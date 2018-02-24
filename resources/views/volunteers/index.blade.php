@extends('layouts.app')

@section('title', 'Volunteers')

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
                    <th>Name</th>
                    <th>Address</th>
                    <th>E-Mail</th>
                    <th>Phone</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($volunteers as $volunteer)
                    <tr>
                        <td>
                            <a href="{{ route('volunteers.show', $volunteer) }}" title="View volunteer">{{ $volunteer->user->name }}</a>
                        </td>
                        <td>{{ $volunteer->fullAddress() }}</td>
                        <td>
                            {{ $volunteer->user->email }}
                            &nbsp; <a href="mailto:{{ $volunteer->user->email }}"><i class="fa fa-envelope-o"></i></a>
                        </td>
                        <td>
                            @if ( isset( $volunteer->phone ) )
                                {{ $volunteer->phone }}
                                &nbsp; <a href="tel:{{ $volunteer->phone }}"><i class="fa fa-phone"></i></a>
                                &nbsp; <a href="whatsapp:{{ $volunteer->phone }}"><i class="fa fa-whatsapp"></i></a>
                            @endif
                        </td>
                        <td>{{ $volunteer->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $volunteers->links('vendor.pagination.bootstrap-4') }}
 
    @else
		<div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No volunteers found.
        </div>
	@endif
	
@endsection
