@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
    <h1 class="display-4">@lang('app.hello', [ 'name' => Auth::user()->name ])!</h1>
    <p class="lead">@lang('app.welcome_text', [ 'app_name' => Config::get('app.product_name') ]).</p>

    @if ( count($widgets) > 0 )
        <div class="card-columns">
            @foreach($widgets as $w)
                {!! $w !!}
            @endforeach
        </div>
    @else
        @component('components.alert.info')
            @lang('app.no_content_available_to_you')
        @endcomponent
    @endif

    @if ( Auth::user()->volunteer == null)
    <div class="card mb-4">
        <div class="card-header">
            Volunteering
        </div>
        <div class="card-body">
            <a href="{{ route('volunteers.updateProfile')  }}" class="btn btn-primary btn-block">Apply as a volunteer</a>
        </div>
    </div>
    @endif

@endsection
