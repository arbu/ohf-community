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

    {!! Form::model(Auth::user()->volunteer, ['route' => ['volunteers.updateProfile']]) !!}

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
                        {{ Form::bsText('address', null, [ 'required' ]) }}
                    </div>
                    <div class="col-md-1">
                        {{ Form::bsText('zip', null, [ 'required' ], 'ZIP') }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::bsText('city', null, [ 'required' ]) }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('country', null, [ 'required', 'id' => 'country', 'autocomplete' => 'off']) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsText('nationality', null, [ 'required' ], null, 'According to your passport / ID card') }}
                    </div>
                    <div class="col-md">
                        {{ Form::bsText('birthdate', null, [ 'required', 'id' => 'birthdate' ], null, 'YYYY-MM-DD') }}
                    </div>
                    <div class="col-md-auto pl-md-3">
                        <p>Gender</p>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                {{ Form::radio('gender', 'male', null, [ 'class' => 'form-check-input' ]) }} Male
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                {{ Form::radio('gender', 'female', null, [ 'class' => 'form-check-input' ]) }} Female
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
    $(function(){
        $('#country').typeahead({
            source: [ @foreach($countries as $country) '{!! $country !!}', @endforeach ]
        });
        /*
        $('#birthdate').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        */
    });
@endsection
