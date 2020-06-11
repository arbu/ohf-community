@extends('layouts.app')

@section('title', __('accounting.accounting'))

@section('content')
    <div id="accounting-app">
        <transactions-index-page
            :wallet-id="{{ $wallet->id }}"
        >
            @lang('app.loading')
        </transactions-index-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
