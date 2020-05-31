@extends('layouts.app')

@section('title', __('accounting.book_to_webling'))

@section('content')
    <div id="accounting-app">
        <webling-prepare-page
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

@section('script')
    function updateStatus(row) {
        var id = row.data('id');

        var message = row.find('input[name="posting_text['+id+']"]').val();
        var debit_side = row.find('select[name="debit_side['+id+']"]').val();
        var credit_side = row.find('select[name="credit_side['+id+']"]').val();
        var action = row.find('input[name="action['+id+']"]:checked').val();

        row.removeClass('table-success');
        row.removeClass('table-warning');
        row.removeClass('table-info');

        if (action == 'book') {
            if (message != '' && debit_side != '' && credit_side != '') {
                row.addClass('table-success');
            } else {
                row.addClass('table-warning');
            }
        } else {
            if (message != '' && debit_side != '' && credit_side != '') {
                row.addClass('table-secondary');
            }
        }
    }

    $('#bookings_table input, #bookings_table select').on('change propertychange keyup', function () {
        var row = $(this).parents('tr');
        updateStatus(row);
    });

    $('#bookings_table tbody tr').each(function () {
        var row = $(this);
        updateStatus(row);
    });

@endsection
