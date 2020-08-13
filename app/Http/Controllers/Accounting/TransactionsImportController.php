<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\TransactionsImportRequest;
use App\Imports\Accounting\TransactionsImport;
use App\Imports\HeadingRowImport;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use Illuminate\Http\Request;

class TransactionsImportController extends Controller
{
    public function import(CurrentWalletService $currentWallet)
    {
        $this->authorize('import', MoneyTransaction::class);

        return view('accounting.transactions.import', [
            'wallets' => Wallet::orderBy('name')
                ->get()
                ->filter(fn ($wallet) => request()->user()->can('view', $wallet)),
            'current_wallet' => $currentWallet->get(),
        ]);
    }

    public function doImport(TransactionsImportRequest $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('import', MoneyTransaction::class);

        $request->validated();

        $fields = TransactionsImport::getImportFields($this->getFields());

        if ($request->map != null) {
            $fields = TransactionsImport::applyHeaderMappings($fields, $request->map);
        }

        if ($request->wallet != null) {
            $wallet = Wallet::find($request->wallet);
        } else {
            $wallet = $currentWallet->get();
        }

        $importer = new TransactionsImport($fields, $wallet);
        $importer->import($request->file('file'));

        return redirect()
            ->route('accounting.transactions.index')
            ->with('success', __('app.import_successful', $importer->stats));
    }

    public function getHeaderMappings(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $table_headers = collect((new HeadingRowImport)->toArray($request->file('file'))[0][0]);

        $fields = TransactionsImport::getImportFields($this->getFields());

        return TransactionsImport::getHeaderMappings($fields, $table_headers);
    }

    private static function getFields()
    {
        return [
            [
                'label_key' => 'accounting.receipt_no',
                'value' => 'receipt_no',
                'assign' => fn ($transaction, $value) => $transaction->receipt_no = $value,
            ],
            [
                'label_key' => 'app.date',
                'value' => 'date',
                'assign' => fn ($transaction, $value) => $transaction->date = $value,
                'format' => 'date',
            ],
            [
                'label_key' => 'app.type',
                'value' => 'type',
                'assign' => fn ($transaction, $value) => $transaction->type = $value,
            ],
            [
                'label_key' => 'app.amount',
                'value' => 'amount',
                'assign' => fn ($transaction, $value) => $transaction->amount = $value,
            ],
            [
                'label_key' => 'accounting.income',
                'value' => fn ($transaction) => $transaction->type == 'income' ? $transaction->amount : null,
                'assign' => function ($transaction, $value) {
                    $transaction->amount = $value;
                    $transaction->type = 'income';
                },
            ],
            [
                'label_key' => 'accounting.spending',
                'value' => fn ($transaction) => $transaction->type == 'spending' ? $transaction->amount : null,
                'assign' => function ($transaction, $value) {
                    $transaction->amount = $value;
                    $transaction->type = 'spending';
                },
            ],
            [
                'label_key' => 'app.receipt_pictures',
                'value' => 'receipt_pictures',
            ],
            [
                'label_key' => 'accounting.attendee',
                'value' => 'attendee',
                'assign' => fn ($transaction, $value) => $transaction->attendee = $value,
            ],
            [
                'label_key' => 'app.category',
                'value' => 'category',
                'assign' => fn ($transaction, $value) => $transaction->category = $value,
            ],
            [
                'label_key' => 'app.secondary_category',
                'value' => 'secondary_category',
                'assign' => fn ($transaction, $value) => $transaction->secondary_category = $value,
            ],
            [
                'label_key' => 'app.project',
                'value' => 'project',
                'assign' => fn ($transaction, $value) => $transaction->project = $value,
            ],
            [
                'label_key' => 'app.location',
                'value' => 'location',
                'assign' => fn ($transaction, $value) => $transaction->location = $value,
            ],
            [
                'label_key' => 'accounting.cost_center',
                'value' => 'cost_center',
                'assign' => fn ($transaction, $value) => $transaction->cost_center = $value,
            ],
            [
                'label_key' => 'app.description',
                'value' => 'description',
                'assign' => fn ($transaction, $value) => $transaction->description = $value,
            ],
            [
                'label_key' => 'app.remarks',
                'value' => 'remarks',
                'assign' => fn ($transaction, $value) => $transaction->remarks = $value,
            ],
            [
                'label_key' => 'accounting.booked',
                'value' => 'booked',
                'assign' => fn ($transaction, $value) => $transaction->booked = filter_var($value, FILTER_VALIDATE_BOOLEAN),
            ],
        ];
    }
}
