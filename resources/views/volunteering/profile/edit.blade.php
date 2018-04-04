@extends('layouts.app')

@section('title')
    @if ( Auth::user()->volunteer == null )
        @lang('volunteering.create_volunteer_profile')
    @else
        @lang('volunteering.update_volunteer_profile')
    @endif
@endsection

@section('content')

    @if ( Auth::user()->volunteer == null )
        <p>@lang('volunteering.fill_in_form_to_complete_volunteer_profile')</p>
    @endif

    {!! Form::model(Auth::user()->volunteer, ['route' => ['volunteering.profile.update']]) !!}
        @php
            $name_parts = explode(' ', Auth::user()->name);
            $first_name = isset($name_parts[0]) ? $name_parts[0] : null;
            $last_name = isset($name_parts[1]) ? $name_parts[1] : null;
            $email = Auth::user()->email;
        @endphp

        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('first_name', $first_name, [ 'required', 'autofocus' ], __('app.first_name')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('last_name', $last_name, [ 'required' ], __('app.last_name')) }}
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('street', null, [ 'required' ], __('app.street')) }}
            </div>
            <div class="col-md-1">
                {{ Form::bsText('zip', null, [ 'required' ], __('app.zip')) }}
            </div>
            <div class="col-md-4">
                {{ Form::bsText('city', null, [ 'required' ], __('app.city')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('country', null, [ 'required', 'id' => 'country', 'autocomplete' => 'off', 'rel' => 'autocomplete', 'data-autocomplete-source' => json_encode(array_values($countries))], __('app.country')) }}
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('nationality', null, [ 'required' ], __('app.nationality'), __('volunteering.according_to_your_passport_id_card')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('passport_no', null, [ ], __('volunteering.passport_no'), __('volunteering.according_to_your_passport_id_card')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('date_of_birth', null, [ 'required' ], __('app.date_of_birth'), __('volunteering.yyyy_mm_dd')) }}
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

        <hr>

        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('email', $email, [ 'required' ], __('app.email'), __('volunteering.used_for_login')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('phone', null, [ 'required' ], __('app.phone'), __('volunteering.mobile_phone_number_incl_country_code')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('whatsapp', null, [ ], __('volunteering.whatsapp'), __('volunteering.whatsapp_number_incl_country_code')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('skype', null, [ ], __('volunteering.skype'), __('volunteering.skype_username_if_available')) }}
            </div>
        </div>

        <p>
            @if ( Auth::user()->volunteer == null )
                {{ Form::bsSubmitButton(__('volunteering.create_profile')) }}
            @else
                {{ Form::bsSubmitButton(__('volunteering.update_profile')) }}
            @endif
        </p>

    {!! Form::close() !!}

@endsection

@section('script')
@endsection
