<?php

namespace App\Services\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use Setting;

class TransactionRepository
{
    public function getNextFreeReceiptNo(?Wallet $wallet = null)
    {
        return optional(MoneyTransaction::selectRaw('MAX(receipt_no) as val')
            ->when($wallet !== null, fn ($qry) => $qry->forWallet($wallet))
            ->first())
            ->val + 1;
    }

    public function getCategories(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.categories')) {
            return collect(Setting::get('accounting.transactions.categories'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::categories();
    }

    public function useSecondaryCategories(): bool
    {
        return Setting::get('accounting.transactions.use_secondary_categories') ?? false;
    }

    public function getSecondaryCategories(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.secondary_categories')) {
            return collect(Setting::get('accounting.transactions.secondary_categories'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::secondaryCategories();
    }

    public function getProjects(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.projects')) {
            return collect(Setting::get('accounting.transactions.projects'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::projects();
    }

    public function useLocations(): bool
    {
        return Setting::get('accounting.transactions.use_locations') ?? false;
    }

    public function getLocations(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.locations')) {
            return collect(Setting::get('accounting.transactions.locations'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::locations();
    }

    public function useCostCenters(): bool
    {
        return Setting::get('accounting.transactions.use_cost_centers') ?? false;
    }

    public function getCostCenters(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.cost_centers')) {
            return collect(Setting::get('accounting.transactions.cost_centers'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::costCenters();
    }
}
