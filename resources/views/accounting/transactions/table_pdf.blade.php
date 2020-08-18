@php
    function lines($text, $width) {
        return substr_count(wordwrap($text, $width, ':newline:', true), ':newline:') + 1;
    }
@endphp
<!doctype html>
<html>
    <head>
        <title>{{ $title }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
            @page {
                font-family: sans-serif;
                font-size: 8pt;
            }

            .nobreak {
                page-break-inside: avoid;
            }

            .transactions {
                width: 100%;
                border-collapse: collapse;
            }
            .transactions td,
            .transactions th {
                padding: 0.2em;
                border: 1px solid gray;
                vertical-align: text-top;
                white-space: nowrap;
            }
            .transactions thead th {
                border-bottom: 3px solid gray;
            }
            .transactions tfoot th {
                border-top: 3px solid gray;
            }

            .num {
                text-align: right;
            }
            .center {
                text-align: center;
            }
            td.long {
                white-space: normal;
            }

            .summary {
                padding-top: 1.5em;
                font-size: 10pt;
            }
        </style>
    </head>
    <body>
        @php $height = 0; @endphp
        @foreach ($transactions as $transaction)
            @php
                $audit = $transaction->audits()->first();
                // dompdf does not work well with tables spanning across multiple pages
                // therefore guess the height of each row and create a new table when a limit is reached
                $lines = 1;
                $lines = max($lines, lines($transaction->category, 15));
                $lines = max($lines, lines($transaction->secondary_category, 20));
                $lines = max($lines, lines($transaction->project, 20));
                $lines = max($lines, lines($transaction->location, 15));
                $lines = max($lines, lines($transaction->cost_center, 20));
                $lines = max($lines, lines($transaction->description, 20));
                $lines = max($lines, lines($transaction->attendee, 20));
                $height += $lines * 2 + 1;
            @endphp
            @if($height > 92 && ! $loop->first)
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('accounting.receipt') #</th>
                                <th>@lang('app.date')</th>
                                <th>@lang('accounting.income')</th>
                                <th>@lang('accounting.spending')</th>
                                <th>@lang('accounting.intermediate_balance_short')</th>
                                <th>@lang('app.category')</th>
                                @if($use_secondary_categories !== null)<th>@lang('app.secondary_category')</th>@endif
                                <th>@lang('app.project')</th>
                                @if($use_locations !== null)<th>@lang('app.location')</th>@endif
                                @if($use_cost_centers !== null)<th>@lang('accounting.cost_center')</th>@endif
                                <th>@lang('app.description')</th>
                                <th>@lang('accounting.attendee')</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
            @if($height > 92 || $loop->first)
                @php $height = $lines * 2 + 1; @endphp
                <div class="nobreak">
                    <table class="transactions">
                        <thead>
                            <tr>
                                <th>@lang('accounting.receipt') #</th>
                                <th>@lang('app.date')</th>
                                <th>@lang('accounting.income')</th>
                                <th>@lang('accounting.spending')</th>
                                <th>@lang('accounting.intermediate_balance_short')</th>
                                <th>@lang('app.category')</th>
                                @if($use_secondary_categories !== null)<th>@lang('app.secondary_category')</th>@endif
                                <th>@lang('app.project')</th>
                                @if($use_locations !== null)<th>@lang('app.location')</th>@endif
                                @if($use_cost_centers !== null)<th>@lang('accounting.cost_center')</th>@endif
                                <th>@lang('app.description')</th>
                                <th>@lang('accounting.attendee')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="center"></td>
                                <td class="center"></td>
                                <td class="num"></td>
                                <td class="num"></td>
                                <td class="num">
                                    @if($loop->first)
                                        {{ number_format($start_balance, 2) }}</td>
                                    @else
                                        {{ number_format($intermediate_balances[$transactions[$loop->index - 1]->id], 2) }}</td>
                                    @endif
                                <td></td>
                                @if($use_secondary_categories !== null)<td></td>@endif
                                <td></td>
                                @if($use_locations !== null)<td></td>@endif
                                @if($use_cost_centers !== null)<td></td>@endif
                                <td></td>
                                <td></td>
                            </tr>
            @endif
            <tr>
                <td class="center">{{ $transaction->receipt_no }}</td>
                <td class="center">{{ $transaction->date }}</td>
                <td class="num">@if($transaction->type == 'income') {{ number_format($transaction->amount, 2) }}@endif</td>
                <td class="num">@if($transaction->type == 'spending') {{ number_format($transaction->amount, 2) }}@endif</td>
                <td class="num">{{ number_format($intermediate_balances[$transaction->id], 2) }}</td>
                <td class="long">{{ $transaction->category }}</td>
                @if($use_secondary_categories !== null)<td class="long">{{ $transaction->secondary_category }}</td>@endif
                <td class="long">{{ $transaction->project }}</td>
                @if($use_locations !== null)<td class="long">{{ $transaction->location }}</td>@endif
                @if($use_cost_centers !== null)<td class="long">{{ $transaction->cost_center }}</td>@endif
                <td class="long">{{ $transaction->description }}</td>
                <td class="long">{{ $transaction->attendee }}</td>
            </tr>
        @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>@lang('accounting.receipt') #</th>
                        <th>@lang('app.date')</th>
                        <th>@lang('accounting.income')</th>
                        <th>@lang('accounting.spending')</th>
                        <th>@lang('accounting.intermediate_balance_short')</th>
                        <th>@lang('app.category')</th>
                        @if($use_secondary_categories !== null)<th>@lang('app.secondary_category')</th>@endif
                        <th>@lang('app.project')</th>
                        @if($use_locations !== null)<th>@lang('app.location')</th>@endif
                        @if($use_cost_centers !== null)<th>@lang('accounting.cost_center')</th>@endif
                        <th>@lang('app.description')</th>
                        <th>@lang('accounting.attendee')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="nobreak summary">
            <table>
                <tbody>
                    <tr>
                        <td>@lang('accounting.start_balance'):</td>
                        <td class="num"><b>{{ number_format($start_balance, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td>@lang('accounting.end_balance'):</td>
                        <td class="num"><b>{{ number_format($end_balance, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td>@lang('accounting.difference'):</td>
                        <td class="num"><b>@if($end_balance > $start_balance)+@endif{{ number_format($end_balance - $start_balance, 2) }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script type="text/javascript">
            try {
                this.print();
            } catch (e) {
                window.onload = window.print;
            }
        </script>
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('
                    $pdf->text(35, $pdf->get_height() - 35, "{{ $title }}", $fontMetrics->get_font("sans-serif"), 9);
                    $text = "@lang('app.page') " . $PAGE_NUM . "/" . $PAGE_COUNT;
                    $text_width = $pdf->get_text_width($text, $fontMetrics->get_font("sans-serif"), 9);
                    $pdf->text($pdf->get_width() - $text_width - 35, $pdf->get_height() - 35, $text, $fontMetrics->get_font("sans-serif"), 9, );
                ');
            }
        </script>
    </body>
</html>
