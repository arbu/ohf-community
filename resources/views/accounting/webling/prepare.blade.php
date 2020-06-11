@extends('layouts.app')

@section('title', __('accounting.book_to_webling'))

@section('content')
    <div id="accounting-app">
        <webling-prepare-page
            :wallet-id="{{ $wallet->id }}"
            period-id="{{ $period_id }}"
            from="{{ $from }}"
            to="{{ $to }}"
        >
            @lang('app.loading')
        </webling-prepare-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
