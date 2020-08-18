<!doctype html>
<html>
    <head>
        <title>{{ $title }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
            @page {
                font-family: sans-serif;
            }

            .receipt-values {
                width: 100%;
            }
            .receipt-values td {
                padding: 0.4em 0em;
                border-top: 1px solid gray;
                vertical-align: text-top;
            }
            .receipt-values td:nth-child(1) {
                width: 1%;
                padding-right: 1em;
                white-space: nowrap;
            }
            .page-break {
                page-break-after: always;
            }
            .receipt-img {
                text-align: center;
            }
            .receipt-img img {
                display: block;
            }
            .scale-width {
                width: 527pt;
            }
            .scale-height {
                height: 706pt;
            }
        </style>
    </head>
    <body>
        @foreach($receipts as $receipt)
            @php extract($receipt) @endphp
            <h2>{{ $title }}</h2>
            <table class="receipt-values">
                <tbody>
                    <tr>
                        @isset($transaction->receipt_no)
                            <td><strong>@lang('accounting.receipt') #</strong></td>
                            <td>{{ $transaction->receipt_no }}</td>
                        @endisset
                    </tr>
                    <tr>
                        <td><strong>@lang('app.date')</strong></td>
                        <td>{{ $transaction->date }}</td>
                    </tr>
                    <tr>
                        <td><strong>@lang('app.amount')
                            @if($transaction->type == 'income') (@lang('accounting.income')) @endif
                            @if($transaction->type == 'spending') (@lang('accounting.spending')) @endif
                        </strong></td>
                        <td>{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>@lang('app.category')</strong></td>
                        <td>{{ $transaction->category }}</td>
                    </tr>
                    @isset($transaction->secondary_category)
                        <tr>
                            <td><strong>@lang('app.secondary_category')</strong></td>
                            <td>{{ $transaction->secondary_category }}</td>
                        </tr>
                    @endisset
                    @isset($transaction->project)
                        <tr>
                            <td><strong>@lang('app.project')</strong></td>
                            <td>{{ $transaction->project }}</td>
                        </tr>
                    @endisset
                    @isset($transaction->location)
                        <tr>
                            <td><strong>@lang('app.location')</strong></td>
                            <td>{{ $transaction->location }}</td>
                        </tr>
                    @endisset
                    @isset($transaction->cost_center)
                        <tr>
                            <td><strong>@lang('accounting.cost_center')</strong></td>
                            <td>{{ $transaction->cost_center }}</td>
                        </tr>
                    @endisset
                    <tr>
                        <td><strong>@lang('app.description')</strong></td>
                        <td>{{ $transaction->description }}</td>
                    </tr>
                    <tr>
                        <td><strong>@lang('accounting.attendee')</strong></td>
                        <td>{{ $transaction->attendee }}</td>
                    </tr>
                    @isset($transaction->remarks)
                        <tr>
                            <td><strong>@lang('app.remarks')</strong></td>
                            <td>{{ $transaction->remarks }}</td>
                        </tr>
                    @endisset
                    <tr>
                        <td><strong>@lang('app.registered')</strong></td>
                        <td>
                            @php
                                $audit = $transaction->audits()->first();
                            @endphp
                            {{ $transaction->created_at }} @isset($audit)({{ $audit->getMetadata()['user_name'] }})@endisset
                        </td>
                    </tr>
                </tbody>
            </table>
            @foreach($receipt_pictures as $picture)
                <div class="page-break"></div>
                <h2>{{ $title }}</h2>
                <div class="receipt-img">
                    <img class="{{ $picture['scale'] }}" src="{{ $picture['path'] }}" />
                </div>
            @endforeach
            @if(! $loop->last)
                <div class="page-break"></div>
                @if($duplex_align && count($receipt_pictures) % 2 == 0)
                    <div class="page-break"></div>
                @endif
            @endif
        @endforeach
        <script type="text/javascript"> try { this.print(); } catch (e) { window.onload = window.print; } </script>
    </body>
</html>
