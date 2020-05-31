@extends('layouts.app')

@section('title', __('accounting.wallets'))

@section('content')
    <div id="accounting-app">
        <wallet-index-page>
            @lang('app.loading')
        </wallet-index-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
