<?php

namespace Modules\Fundraising\Exports;

use App\Exports\BaseExport;

use Modules\Fundraising\Entities\Donor;
use Modules\Fundraising\Entities\Donation;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DonorsExport extends BaseExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    public function __construct()
    {
        $this->setOrientation('landscape');
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return Donor
            ::orderBy('first_name')
            ->orderBy('last_name')
            ->orderBy('company');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return __('fundraising::fundraising.donors');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            __('app.salutation'),
            __('app.first_name'),
            __('app.last_name'),
            __('app.company'),
            __('app.street'),
            __('app.zip'),
            __('app.city'),
            __('app.country'),
            __('app.email'),
            __('app.phone'),
            __('app.correspondence_language'),
            __('app.registered'),
            __('app.tags'),
            __('app.remarks'),
        ];
        if (Auth::user()->can('list', Donation::class)) {
            $headings[] = __('fundraising::fundraising.donations') . ' ' . Carbon::now()->subYear()->year; 
            $headings[] = __('fundraising::fundraising.donations') . ' ' . Carbon::now()->year; 
        }
        return $headings;
    }

    /**
    * @var Donor $donor
    */
    public function map($donor): array
    {
        $map = [
            $donor->salutation,
            $donor->first_name,
            $donor->last_name,
            $donor->company,
            $donor->street,
            $donor->zip,
            $donor->city,
            $donor->country_name,
            $donor->email,
            $donor->phone,
            $donor->language,
            $donor->created_at,
            $donor->tags->sortBy('name')->pluck('name')->implode(', '),
            $donor->remarks,
        ];
        if (Auth::user()->can('list', Donation::class)) {
            $map[] = $donor->amountPerYear(Carbon::now()->subYear()->year) ?? 0;
            $map[] = $donor->amountPerYear(Carbon::now()->year) ?? 0;
        }
        return $map;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        if (Auth::user()->can('list', Donation::class)) {
            return [
                'O' => Config::get('fundraising.base_currency_excel_format'),
                'P' => Config::get('fundraising.base_currency_excel_format'),
            ];
        }
        return [];
    }
}