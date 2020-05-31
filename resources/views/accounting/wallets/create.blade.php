@extends('layouts.app')

@section('title', __('accounting.create_wallet'))

@section('content')
    <div id="accounting-app">
        <wallet-create-page>
            @lang('app.loading')
        </wallet-create-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
