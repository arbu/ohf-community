@extends('layouts.app')

@section('title', __('logistics::suppliers.edit_supplier'))

@section('content')

    {!! Form::model($supplier, ['route' => ['logistics.suppliers.update', $supplier], 'method' => 'put']) !!}

       <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('name', null, [ 'required' ], __('app.name')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('name_translit', null, [ ], __('app.name_translit')) }}
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('address', null, [ 'required' ], __('app.address')) }}
            </div>
            <div class="col-md">
                {{ Form::bsText('address_translit', null, [  ], __('app.address_translit')) }}
            </div>
            <div class="col-sm-1">
                {{ Form::bsText('latitude', null, [ 'pattern' => '-?\d+\.\d+', 'title' => __('app.decimal_number') ], __('app.latitude')) }}
            </div>
            <div class="col-sm-1">
                {{ Form::bsText('longitude', null, [ 'pattern' => '-?\d+\.\d+', 'title' => __('app.decimal_number') ], __('app.longitude')) }}
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('category', null, [ 'required', 'rel' => 'autocomplete', 'data-autocomplete-source' => json_encode($categories) ], __('app.category')) }}
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                {{ Form::bsText('phone', null, [ ], __('app.phone')) }}
            </div>
            <div class="col-md">
                {{ Form::bsEmail('email', null, [ ], __('app.email')) }}
            </div>
            <div class="col-md">
                {{ Form::bsUrl('website', null, [ ], __('app.website')) }}
            </div>
        </div>
        {{ Form::bsTextarea('remarks', null, [ 'rows' => 3 ], __('app.remarks')) }}

        <p>
            {{ Form::bsSubmitButton(__('app.update')) }}
        </p>

    {!! Form::close() !!}
	
@endsection