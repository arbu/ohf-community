<?php

namespace App\Exports\Accounting;

use App\Exports\BaseExport;
use App\Http\Controllers\Accounting\MoneyTransactionsController;
use App\Models\Accounting\MoneyTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class WeblingMoneyTransactionsExport extends BaseExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
     * Filter conditions
     *
     * @var array<string>
     */
    private array $filter;

    /**
     * Constructor
     *
     * @param array<string>|null $filter
     */
    public function __construct(?array $filter = [])
    {
        $this->filter = $filter;
        $this->orientation = 'landscape';
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        $qry = MoneyTransaction::orderBy('date', 'ASC')
            ->orderBy('created_at', 'ASC');
        MoneyTransactionsController::applyFilterToQuery($this->filter, $qry);
        return $qry;
    }

    public function title(): string
    {
        return __('accounting.transactions');
    }

    public function headings(): array
    {
        return [
            'Datum',
            'Gutschrift',
            'Lastschrift',
            'Buchungstext',
        ];
    }

    /**
     * @param MoneyTransaction $transaction
     */
    public function map($transaction): array
    {
        return [
            (new Carbon($transaction->date))->format('d.m.Y'),
            $transaction->type == 'income' ? $transaction->amount : '',
            $transaction->type == 'spending' ? $transaction->amount : '',
            ($transaction->receipt_no > 0 ? ('#'. $transaction->receipt_no . ' ') : '') . '['. $transaction->category . ']  ' . ($transaction->project != null ? ('(' . $transaction->project . ') ') : '') . $transaction->description,
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'B' => NumberFormat::FORMAT_NUMBER_00,
            'C' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
