@extends('layouts.app')

@section('title', __('accounting.show_transaction'))

@section('content')
    <div id="accounting-app">
        <transaction-show-page id="{{ $transaction->id }}">
            @lang('app.loading')
        </transaction-show-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
