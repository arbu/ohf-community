@extends('layouts.app')

@section('title', __('accounting.import_transaction_data'))

@section('content')

    {!! Form::open(['route' => 'accounting.transactions.doImport', 'files' => true]) !!}
        {{ Form::bsSelect('wallet', $wallets->mapWithKeys(fn ($w) => [ $w['id'] => $w['name'] ]), $current_wallet['id']) }}
        {{ Form::bsFile('file', [ 'accept' => '.xlsx,.xls,.csv', 'class' => 'import-form-file' ], __('app.choose_file')) }}
        <table class="import-form-header-mapping table d-none" data-query="{{ route('accounting.getHeaderMappings') }}">
            <thead>
                <th>@lang('app.field_to_import')</th>
                <th>@lang('app.field_in_database')</th>
                <th>@lang('app.add_to_existing_values')</th>
            </thead>
            <tbody></tbody>
        </table>
        <p>
            {{ Form::bsSubmitButton(__('app.import'), 'upload') }}
        </p>
    {!! Form::close() !!}

@endsection
