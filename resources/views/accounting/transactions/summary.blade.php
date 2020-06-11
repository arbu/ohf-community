@extends('layouts.app')

@section('title', __('accounting.accounting'))

@section('content')
    <div id="accounting-app">
        <transaction-summary-page
            :wallet-id="{{ $wallet->id }}"
        >
            @lang('app.loading')
        </transaction-summary-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
