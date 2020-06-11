@extends('layouts.app')

@section('title', __('accounting.register_new_transaction'))

@section('content')
    <div id="accounting-app">
        <transaction-create-page
            :wallet-id="{{ $wallet->id }}"
        >
            @lang('app.loading')
        </transaction-create-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
