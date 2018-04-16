@extends('layouts.app')

@section('title', __('volunteering.create_category'))

@section('content')

    {!! Form::open(['route' => ['volunteering.jobs.categories.store'], 'method' => 'post']) !!}

        <div class="card mb-4">
            <div class="card-header">@lang('app.general')</div>
            <div class="card-body pb-1">
                {{ Form::bsNumber('order', \App\VolunteerJobCategory::count(), [ 'required', 'min' => 0 ], __('app.order')) }}
            </div>
        </div>
        
        @foreach([
            __('app.title') => 'title',
        ] as $k => $v)
            <div class="card mb-4">
                <div class="card-header">{{ $k }}</div>
                <div class="card-body pb-1">
                    @foreach (language()->allowed() as $code => $name)
                        <div class="form-row">
                            <div class="col-sm-2 pb-2 pb-sm-0">
                                {{ $name }}
                            </div>
                            <div class="col-sm">
                                @if($v == 'description')
                                    {{ Form::bsTextarea($v . '[' . $code . ']', null, [ 'required' ], '') }}
                                @else
                                    {{ Form::bsText($v . '[' . $code . ']', null, [ 'required' ], '') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <p>{{ Form::bsSubmitButton(__('app.create')) }}</p>

    {!! Form::close() !!}

@endsection
