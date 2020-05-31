@extends('layouts.app')

@section('title', __('accounting.edit_wallet'))

@section('content')
    <div id="accounting-app">
        <wallet-edit-page :id="{{ $wallet->id }}">
            @lang('app.loading')
        </wallet-edit-page>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection

