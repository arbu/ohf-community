@extends('layouts.app')

@section('title', __('volunteering.edit_job'))

@section('content')

    {!! Form::model($job, ['route' => ['volunteering.jobs.update', $job], 'method' => 'put']) !!}

        <div class="form-row">
            <div class="col-md">
                {{ Form::bsSelect('category', $categories, null, [ 'required' ], __('app.category')) }}
            </div>
            <div class="col-md">
                {{ Form::bsNumber('order', null, [ 'required', 'min' => 0 ], __('app.order')) }}
            </div>
        </div>
        
        <table class="table table-sm">
            <tbody>
                @foreach([
                    __('app.title') => 'title',
                    __('app.description') => 'description',
                    __('volunteering.available_dates') => 'available_dates',
                    __('volunteering.minimum_stay') => 'minimum_stay',
                    __('app.requirements') => 'requirements',
                ] as $k => $v)
                    <tr>
                        <th class="fit">{{ $k }}</th>
                        <td>
                            <table class="table m-0">
                                @foreach (language()->allowed() as $code => $name)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td>{{ Form::bsText($v . '[' . $code . ']', null, [ 'required' ], '') }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>{{ Form::bsSubmitButton(__('app.update')) }}</p>

    {!! Form::close() !!}

@endsection
