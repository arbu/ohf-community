@extends('layouts.app')

@section('title', __('accounting.edit_transaction_number', ['number' => $transaction->receipt_no]))

@section('content')
    <div id="accounting-app">
        <transaction-edit-page id="{{ $transaction->id }}">
            @lang('app.loading')
        </transaction-edit-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection

