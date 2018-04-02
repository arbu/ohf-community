@extends('layouts.app')

@section('title')
    @if ( Auth::user()->volunteer == null )
        Apply as a Volunteer
    @else
        Update Volunteer Profile
    @endif
@endsection

@section('content')

    @if ( Auth::user()->volunteer == null )
        <p>Please fill in the form below to complete your voulnteer profile:</p>
    @endif

    {!! Form::model(Auth::user()->volunteer, ['route' => ['volunteering.profile.update']]) !!}

        <div class="card mb-4">
            <div class="card-header">Personal Information</div>
            <div class="card-body">

                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsText('name', Auth::user()->name, [ 'required' ]) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsText('street', null, [ 'required' ], __('app.street')) }}
                    </div>
                    <div class="col-md-1">
                        {{ Form::bsText('zip', null, [ 'required' ], 'ZIP') }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::bsText('city', null, [ 'required' ]) }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('country', null, [ 'required', 'id' => 'country', 'autocomplete' => 'off', 'rel' => 'autocomplete', 'data-autocomplete-source' => json_encode(array_values($countries))]) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsText('nationality', null, [ 'required' ], null, 'According to your passport / ID card') }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('date_of_birth', null, [ 'required' ], __('app.date_of_birth'), 'YYYY-MM-DD') }}
                    </div>
                    <div class="col-md-auto pl-md-3">
                        <p>@lang('app.gender')</p>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                {{ Form::radio('gender', 'male', null, [ 'class' => 'form-check-input' ]) }} @lang('people.male')
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                {{ Form::radio('gender', 'female', null, [ 'class' => 'form-check-input' ]) }} @lang('people.female')
                            </label>
                        </div>
						@if ($errors->has('gender'))
							<div><small class="text-danger">{{ $errors->first('gender') }}</small></div>
						@endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Communication</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsText('phone', null, [ 'required' ], null, 'Mobile phone number, including country code') }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('email', Auth::user()->email, [ 'required' ], 'E-Mail', 'Used for login') }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('skype', null, [ ], null, 'Skype username, if available') }}
                    </div>
                </div>

            </div>

        </div>

        <p>
            @if ( Auth::user()->volunteer == null )
                {{ Form::bsSubmitButton('Create profile') }}
            @else
                {{ Form::bsSubmitButton('Update profile') }}
            @endif
        </p>

    {!! Form::close() !!}

@endsection

@section('script')
@endsection
