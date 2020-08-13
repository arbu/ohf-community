<?php

namespace App\Imports\Accounting;

use App\Imports\ImportWithMapping;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use Illuminate\Support\Collection;

class TransactionsImport extends ImportWithMapping
{
    public function __construct(Collection $fields, Wallet $wallet)
    {
        $this->wallet = $wallet;

        parent::__construct($fields);
    }

    protected static function getModelIdentifier() {
        return 'transaction';
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $transaction = new MoneyTransaction();

            $this->assignImportedValues($row, $transaction);

            if (isset($transaction->receipt_no)) {
                $existing_transaction = MoneyTransaction::query()
                    ->where('receipt_no', $transaction->receipt_no)
                    ->where('wallet_id', $this->wallet->id)
                    ->first();

                if ($existing_transaction !== null) {
                    $this->assignImportedValues($row, $existing_transaction,
                        fn ($object, $field, $value) => $f['key'] == 'app.responsibilities');
                    $transaction = $existing_transaction;
                }
            } else {
                $transaction->receipt_no = optional(MoneyTransaction::query()
                    ->selectRaw('max(receipt_no) as max_receipt_no')
                    ->forWallet($this->wallet)
                    ->first())->max_receipt_no + 1;
            }

            $transaction->wallet_id = $this->wallet->id;

            if (! isset($transaction->attendee)) {
                $transaction->attendee = 'N/A';
            }
            if (! isset($transaction->category)) {
                $transaction->category = 'N/A';
            }
            if (isset($transaction->date)
                && isset($transaction->type)
                && isset($transaction->amount)
                && isset($transaction->description)) {
                if ($transaction->id == null) {
                    $this->created();
                } else {
                    $this->updated();
                }
                $transaction->save();
            } else {
                $this->skipped();
            }
        }
    }
}
