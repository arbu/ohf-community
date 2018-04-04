@extends('volunteering.layout')

@section('title', __('volunteering.volunteer_coordination'))

@section('wrapped-content')

    @if( ! $volunteers->isEmpty() )
        <table class="table table-sm table-bordered table-striped table-hover table-responsive-md">
            <thead>
                <tr>
                    <th>@lang('app.name')</th>
                    <th>@lang('app.address')</th>
                    <th>@lang('app.date_of_birth')</th>
                    <th>@lang('app.age')</th>
                    <th>@lang('app.gender')</th>
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
                            <a href="{{ route('volunteering.volunteers.show', $volunteer) }}">{{ $volunteer->name }}</a>
                        </td>
                        <td>{{ $volunteer->address }}</td>
                        <td>{{ $volunteer->date_of_birth }}</td>
                        <td>{{ $volunteer->age }}</td>
                        <td>
                            @isset($volunteer->gender) 
                                @if($volunteer->gender == 'female')@icon(female)
                                @elseif($volunteer->gender == 'male')@icon(male)
                                @endif
                            @endisset
                        </td>
                        <td>
                            <a href="{{ email_url($volunteer->user->email) }}"><i class="fa fa-envelope-o"></i></a>
                            {{ $volunteer->user->email }}
                        </td>
                        <td>
                            @if ( isset( $volunteer->phone ) )
                                <a href="{{ phone_url($volunteer->phone) }}"><i class="fa fa-phone"></i></a>
                                {{ $volunteer->phone }} 
                            @endif
                        </td>
                        <td>
                            @if ( isset( $volunteer->whatsapp ) )
                                <a href="{{ whatsapp_url($volunteer->whatsapp) }}"><i class="fa fa-whatsapp"></i></a>
                                {{ $volunteer->whatsapp }}
                            @endif
                        </td>
                        <td>
                            @if ( isset( $volunteer->skype ) )
                                <a href="{{ skype_call_url($volunteer->skype) }}"><i class="fa fa-skype"></i></a>
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
