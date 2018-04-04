@extends('layouts.app')

@section('title', __('volunteering.view_job'))

@section('content')

    <ul class="list-group list-group-flush mb-2">
        <li class="list-group-item d-none d-sm-block">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm">
                    <div class="row">
                        @foreach (language()->allowed() as $code => $name)
                            <div class="col-sm"><strong>{{ $name }}</strong></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2"><strong>@lang('app.category')</strong></div>
                <div class="col-sm">
                    <div class="row">
                        @foreach (language()->allowed() as $code => $name)
                        <div class="col-sm">
                            <small class="text-muted d-block d-sm-none mt-3">{{ $name }}:</small> 
                                {{ $job->category->title[$code] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </li>
        @foreach([
            __('app.title') => 'title',
            __('app.description') => 'description',
            __('volunteering.available_dates') => 'available_dates',
            __('volunteering.minimum_stay') => 'minimum_stay',
            __('app.requirements') => 'requirements',
        ] as $k => $v)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2"><strong>{{ $k }}</strong></div>
                    <div class="col-sm">
                        <div class="row">
                            @foreach (language()->allowed() as $code => $name)
                                <div class="col-sm">
                                    <small class="text-muted d-block d-sm-none mt-3">{{ $name }}:</small> 
                                    {{ $job->$v[$code] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-2"><strong>@lang('app.order')</strong></div>
                <div class="col-sm">
                    <div class="mt-3 mt-sm-0">{{ $job->order }}</div>
                </div>
            </div>
        </li>
    </ul>

@endsection
