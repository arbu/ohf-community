@extends('layouts.app')

@section('title', __('people.edit_helper'))

@section('content')

    {!! Form::model($helper, ['route' => ['people.helpers.update', $helper], 'method' => 'put', 'files' => true]) !!}

        <div class="columns-3 mb-4">
            @foreach($data as $section_label => $fields)
                @if(! empty($fields))
                    <div class="card mb-4 column-break-avoid">
                        <div class="card-header">{{ $section_label }}</div>
                        <div class="card-body">
                            @include('helpers.form')
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <p>
            {{ Form::bsSubmitButton(__('app.update')) }}
        </p>

    {!! Form::close() !!}

@endsection
