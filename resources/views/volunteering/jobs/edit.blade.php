@extends('layouts.app')

@section('title', __('volunteering.edit_job'))

@section('content')

    {!! Form::model($job, ['route' => ['volunteering.jobs.update', $job], 'method' => 'put']) !!}

        <div class="card mb-4">
            <div class="card-header">@lang('app.general')</div>
            <div class="card-body pb-1">
                <div class="form-row">
                    <div class="col-md">
                        {{ Form::bsSelect('category', $categories, $job->category->id, [ 'required' ], __('app.category')) }}
                    </div>
                    <div class="col-md-2">
                        {{ Form::bsNumber('order', null, [ 'required', 'min' => 0 ], __('app.order')) }}
                    </div>
                </div>
            </div>
        </div>
        
        @foreach([
            __('app.title') => 'title',
            __('app.description') => 'description',
            __('volunteering.available_dates') => 'available_dates',
            __('volunteering.minimum_stay') => 'minimum_stay',
            __('app.requirements') => 'requirements',
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

        <p>{{ Form::bsSubmitButton(__('app.update')) }}</p>

    {!! Form::close() !!}

@endsection
