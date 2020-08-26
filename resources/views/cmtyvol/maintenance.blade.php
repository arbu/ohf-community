@extends('layouts.app')

@section('title', __('app.maintenance'))

@section('content')

    {!! Form::open(['route' => ['cmtyvol.doMaintenance']]) !!}

        <div class="card mb-4">
            <div class="card-header">@lang('people.cleanup_database')</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md">
                        <p>@lang('people.there_are_n_people_registered', [ 'num' => $num_all ]).</p>
                        {{ Form::bsCheckbox('remove_alumni_since', null, null, __('cmtyvol.remove_alumni_since_n_months', [
                            'months' => $months_alumni_since,
                            'num' => $num_alumni_since_n_months,
                        ])) }}
                        {{ Form::bsCheckbox('remove_all_alumni', null, null, __('cmtyvol.remove_all_alumni', [
                            'num' => $num_alumni,
                        ])) }}
                        {{ Form::bsCheckbox('remove_future', null, null, __('cmtyvol.remove_future', [
                            'num' => $num_future,
                        ])) }}
                        {{ Form::bsCheckbox('remove_all', null, null, __('cmtyvol.remove_all', [
                            'num' => $num_all,
                        ])) }}
                        <br>
                        {{ Form::bsSubmitButton(__('people.cleanup')) }}
                    </div>
                </div>
            </div>
        </div>

@endsection
