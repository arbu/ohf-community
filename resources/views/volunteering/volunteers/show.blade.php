@extends('layouts.app')

@section('title', __('volunteering.view_volunteer'))

@section('content')

    {{-- Personal information --}}
    <div class="card mb-4">
        <div class="card-header">Personal Information</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2 d-none d-md-block">Person</div>
                <div class="col-sm">{{ $volunteer->user->name }}, {{ $volunteer->nationality }}, {{ $volunteer->birthdate }}, {{ $volunteer->gender }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-2 d-none d-md-block">Address</div>
                <div class="col-sm">{{ $volunteer->address }}, {{ $volunteer->zip }} {{ $volunteer->city }}, {{ $volunteer->country }}</div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-2 d-none d-md-block">Communication</div>
                <div class="col-sm">
                    <div class="row">
                        <div class="col-md-auto mb-3 mb-md-0">
                            <a href="mailto:{{ $volunteer->user->email }}" class="btn btn-light btn-block">@icon(envelope) {{ $volunteer->user->email }}</a>
                        </div>
                        <div class="col-md-auto mb-3 mb-md-0">
                            <a href="tel:{{ $volunteer->phone }}" class="btn btn-light btn-block">@icon(phone) {{ $volunteer->phone }}</a>
                        </div>
                        <div class="col-md-auto mb-3 mb-md-0">
                            <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/^\+/', '', $volunteer->phone) }}" class="btn btn-light btn-block">@icon(whatsapp) {{ $volunteer->phone }}</a>
                        </div>
                        @if( !empty($volunteer->skype) )
                            <div class="col-md-auto">
                                <a href="skype:{{ $volunteer->skype }}" class="btn btn-light btn-block">@icon(skype) {{ $volunteer->skype }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection
