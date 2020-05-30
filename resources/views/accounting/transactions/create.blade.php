@extends('layouts.app')

@section('title', __('accounting.register_new_transaction'))

@section('content')
    <div id="accounting-app">
        <transaction-create-page
            :wallet='@json($wallet)'
            :wallets='@json($wallets)'
        >
            @lang('app.loading')
        </transaction-create-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
