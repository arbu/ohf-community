@extends('layouts.app')

@section('title', __('accounting::accounting.show_transaction'))

@section('content')

    <div class="row">
        <div class="col-md-8">
            @include('accounting::transactions.snippet')
        </div>
        <div class="col-md-4">
            <h3>@lang('accounting::accounting.receipt')</h3>
            <div id="accounting-app">
                <image-viewer
                    list-url="{{ route('api.accounting.transactions.listReceipts', $transaction) }}"
                    @can('update', $transaction)
                    upload-url="{{ route('api.accounting.transactions.updateReceipts', $transaction) }}"
                    delete-url="{{ route('api.accounting.transactions.deleteReceipt', $transaction) }}"
                    @endcan
                ></image-viewer>
            </div>
        </div>
    </div>

    <p class="mt-3">
        @isset($prev_id)
            <a href="{{ route('accounting.transactions.show', $prev_id) }}" class="btn btn-sm btn-secondary">
                &lsaquo; Previous
            </a>
        @endisset
        @isset($next_id)
            <a href="{{ route('accounting.transactions.show', $next_id) }}" class="btn btn-sm btn-secondary">
                Next &rsaquo;
            </a>
        @endisset
    </p>

@endsection

@section('footer')
    <script src="{{ asset('js/accounting.js') }}?v={{ $app_version }}"></script>
@endsection
