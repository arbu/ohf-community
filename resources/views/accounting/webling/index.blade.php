@extends('layouts.app')

@section('title', __('accounting.book_to_webling'))

@section('content')
    <div id="accounting-app">
        <webling-index-page
            :wallet-id="{{ $wallet->id }}"
        >
            @lang('app.loading')
        </webling-index-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
