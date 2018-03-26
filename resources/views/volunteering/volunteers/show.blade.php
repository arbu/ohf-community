@extends('layouts.app')

@section('title', __('volunteering.view_volunteer'))

@section('content')

    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('people.person')</div>
                <div class="col-sm">
                    @isset($volunteer->gender)
                        @if($volunteer->gender == 'female')@icon(female) 
                        @elseif($volunteer->gender == 'male')@icon(male) 
                        @endif
                    @endisset                    
                    {{ $volunteer->name }}, 
                    {{ $volunteer->nationality }}, 
                    {{ $volunteer->date_of_birth }} ({{ $volunteer->age }})
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2 d-none d-md-block">@lang('app.address')</div>
                <div class="col-sm">{{ $volunteer->address }}</div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row align-items-center">
                <div class="col-sm-2 d-none d-md-block">@lang('app.communication')</div>
                <div class="col-sm">
                    <div class="row">
                        <div class="col-md-auto mb-3 mb-md-0">
                            <a href="{{ email_url($volunteer->user->email) }}" class="btn btn-light btn-block">@icon(envelope) {{ $volunteer->user->email }}</a>
                        </div>
                        @isset($volunteer->phone)
                            <div class="col-md-auto mb-3 mb-md-0">
                                <a href="{{ phone_url($volunteer->phone) }}" class="btn btn-light btn-block">@icon(phone) {{ $volunteer->phone }}</a>
                            </div>
                        @endisset
                        @isset($volunteer->whatsapp)
                            <div class="col-md-auto mb-3 mb-md-0">
                                <a href="{{ whatsapp_url($volunteer->whatsapp) }}" class="btn btn-light btn-block">@icon(whatsapp) {{ $volunteer->whatsapp }}</a>
                            </div>
                        @endisset
                        @isset($volunteer->skype)
                            <div class="col-md-auto">
                                <a href="{{ skype_call_url($volunteer->skype) }}" class="btn btn-light btn-block">@icon(skype) {{ $volunteer->skype }}</a>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </li>
    </ul>

@endsection

@section('script')

@endsection
