@extends('layouts.app')

@section('title', __('app.print'))

@section('content')

    {!! Form::open(['route' => 'accounting.transactions.doPrintMultiple']) !!}
        <div class="mb-3">
            {{ Form::bsRadioList('format', [
                'table' => __('accounting.print_table'),
                'receipts' => __('accounting.print_receipts'),
            ], 'receipts', '') }}
        </div>
        <div class="mb-3">
            {{ Form::bsRadioList('range_type', [
                'all' => __('accounting.range_all'),
                'receipt' => __('accounting.range_receipt'),
                'date' => __('accounting.range_date'),
            ], 'all', __('accounting.limit_range')) }}
        </div>
        <div class="row input-date">
            <div class="col-xs m-3">
                {{ Form::bsDate('date_from', $ranges->date_min, [], __('accounting.date_from')) }}
            </div>
            <div class="col-xs m-3">
                {{ Form::bsDate('date_to', $ranges->date_max, [], __('accounting.date_to')) }}
            </div>
        </div>
        <div class="row input-receipt">
            <div class="col-xs m-3">
                {{ Form::bsNumber('receipt_from', $ranges->receipt_min, [], __('accounting.receipt_from')) }}
            </div>
            <div class="col-xs m-3">
                {{ Form::bsNumber('receipt_to', $ranges->receipt_max, [], __('accounting.receipt_to')) }}
            </div>
        </div>
        <div class="mb-3">
            {{ Form::bsCheckbox('duplex_align', 'true', false, __('accounting.duplex_align')) }}
        </div>
        <p>
            {{ Form::bsSubmitButton(__('app.print'), 'print') }}
        </p>
    {!! Form::close() !!}

@endsection

@section('script')

    $(function () {
        $('[name="range_type"]').change(function () {
            console.log(this);
            $('.input-date').toggle($(this).val() === 'date');
            $('.input-receipt').toggle($(this).val() === 'receipt');
        });
        $('[name="range_type"]:checked').change()
    });

@endsection
