@extends('layouts.app')

@section('title', __('app.export'))

@section('content')
    <div id="accounting-app">
        <export-page
            :wallet-id="{{ $wallet->id }}"
        >
            @lang('app.loading')
        </v-page>
    </div>
<hr>
@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
