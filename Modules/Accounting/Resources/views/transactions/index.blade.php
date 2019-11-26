@extends('accounting::layouts.accounting')

@section('title', __('accounting::accounting.accounting'))

@section('wrapped-content')

    @php
        $labels = [
            'receipt_no' => __('accounting::accounting.receipt_no'),
            'date' => __('app.date'),
            'amount' => __('app.amount'),
            'income' => __('accounting::accounting.income'),
            'spending' => __('accounting::accounting.spending'),
            'category' => __('app.category'),
            'project' => __('app.project'),
            'description' => __('app.description'),
            'beneficiary' => __('accounting::accounting.beneficiary'),
            'registered' => __('app.registered'),
            'date_from' => __('app.start_date'),
            'date_to' => __('app.end_date'),
            'type' => __('app.type'),
            'registered_today' => __('accounting::accounting.registered_today'),
            'no_receipt' => __('accounting::accounting.no_receipt'),
            'amount_from' => __('app.amount_from'),
            'amount_to' => __('app.amount_to'),
        ];
        $types = [
            [ 'value' => null, 'text' => __('app.any') ],
            [ 'value' => 'income', 'text' => __('accounting::accounting.income') ],
            [ 'value' => 'spending', 'text' => __('accounting::accounting.spending') ],
        ];
    @endphp

    <div id="accounting-app">
        <accounting-table
            api-url="{{ route('api.accounting.transactions.index') }}"
            :labels='@json($labels)'
            loading-text="@lang('app.loading')"
            empty-text="@lang('app.no_records_to_show')"
            empty-filtered-text="@lang('app.no_records_matching_your_request')"
            reload-text="@lang('app.reload')"
            :types='@json($types)'
            :categories='@json(array_values($categories))'
            :projects='@json(array_values($projects))'
            :beneficiaries='@json(array_values($beneficiaries))'
            filter-text="@lang('app.filter')"
            hide-filter-text="@lang('app.hide_filter')"
            reset-filter-text="@lang('app.reset_filter')"
        ></accounting-table>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection