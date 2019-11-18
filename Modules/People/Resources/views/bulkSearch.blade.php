@extends('layouts.app')

@section('title', __('app.bulk_search'))

@section('content')

    {!! Form::open(['route' => 'people.doBulkSearch']) !!}
        {{ Form::bsTextarea('data', null, ['autofocus'], __('app.data')) }}
        <p>
            {{ Form::bsSubmitButton(__('app.search'), 'search') }}
        </p>
    {!! Form::close() !!}
    
@endsection

