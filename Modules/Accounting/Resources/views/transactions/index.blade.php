@extends('accounting::layouts.accounting')

@section('title', __('accounting::accounting.accounting'))

@section('wrapped-content')

    <div id="accounting-app">
        <accounting-table
            api-url="{{ route('api.accounting.transactions.index') }}"
            :labels=@json($labels)
            loading-text="@lang('app.loading')"
            empty-text="@lang('app.no_records_to_show')"
            empty-filtered-text="@lang('app.no_records_matching_your_request')"
            reload-text="@lang('app.reload')"
        ></accounting-table>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection