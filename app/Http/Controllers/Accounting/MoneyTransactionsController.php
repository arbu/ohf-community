<?php

namespace App\Http\Controllers\Accounting;

use App\Exports\Accounting\MoneyTransactionsExport;
use App\Exports\Accounting\MoneyTransactionsMonthsExport;
use App\Exports\Accounting\WeblingMoneyTransactionsExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Export\ExportableActions;
use App\Http\Requests\Accounting\StoreTransaction;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use App\Support\Accounting\Webling\Entities\Entrygroup;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Setting;

class MoneyTransactionsController extends Controller
{
    use ExportableActions;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        $request->validate([
            'date_start' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'date_end' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'type' => [
                'nullable',
                Rule::in(['income', 'spending']),
            ],
            'month' => 'nullable|regex:/[0-1]?[1-9]/',
            'year' => 'nullable|integer|min:2017|max:' . Carbon::today()->year,
            'sortColumn' => 'nullable|in:date,created_at,category,secondary_category,project,location,cost_center,attendee,receipt_no',
            'sortOrder' => 'nullable|in:asc,desc',
            'wallet_id' => [
                'nullable',
                'exists:accounting_wallets,id',
            ],
        ]);

        if ($request->has('wallet_id')) {
            $wallet = Wallet::find($request->input('wallet_id'));
            $this->authorize('view', $wallet);
            $currentWallet->set($wallet);
        }

        $wallet = $currentWallet->get();
        if ($wallet === null) {
            return redirect()->route('accounting.wallets.change');
        }

        $sortColumns = [
            'date' => __('app.date'),
            'category' => __('app.category'),
            'secondary_category' => __('accounting.secondary_category'),
            'project' => __('app.project'),
            'location' => __('app.location'),
            'cost_center' => __('accounting.cost_center'),
            'attendee' => __('accounting.attendee'),
            'receipt_no' => __('accounting.receipt'),
            'created_at' => __('app.registered'),
        ];
        $sortColumn = session('accounting.sortColumn', self::showIntermediateBalances() ? 'receipt_no' : 'created_at');
        $sortOrder = session('accounting.sortOrder', 'desc');
        if (isset($request->sortColumn)) {
            $sortColumn = $request->sortColumn;
            session(['accounting.sortColumn' => $sortColumn]);
        }
        if (isset($request->sortOrder)) {
            $sortOrder = $request->sortOrder;
            session(['accounting.sortOrder' => $sortOrder]);
        }

        if ($request->query('reset_filter') != null) {
            session(['accounting.filter' => []]);
        }
        $filter = session('accounting.filter', []);
        foreach (config('accounting.filter_columns') as $col) {
            if (! empty($request->filter[$col])) {
                $filter[$col] = $request->filter[$col];
            } elseif (isset($request->filter)) {
                unset($filter[$col]);
            }
        }
        if (! empty($request->filter['date_start'])) {
            $filter['date_start'] = $request->filter['date_start'];
        } elseif (isset($request->filter)) {
            unset($filter['date_start']);
        }
        if (! empty($request->filter['date_end'])) {
            $filter['date_end'] = $request->filter['date_end'];
        } elseif (isset($request->filter)) {
            unset($filter['date_end']);
        }
        session(['accounting.filter' => $filter]);

        $query = self::createIndexQuery($filter, $sortColumn, $sortOrder);

        // Get results
        $transactions = $query->paginate(250);

        // Single receipt no. query
        if ($transactions->count() == 1 && ! empty($filter['receipt_no'])) {
            session(['accounting.filter' => []]);
            return redirect()->route('accounting.transactions.show', $transactions->first());
        }

        return view('accounting.transactions.index', [
            'transactions' => $transactions,
            'filter' => $filter,
            'sortColumns' => $sortColumns,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
            'attendees' => MoneyTransaction::attendees(),
            'categories' => self::getCategories(true),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => self::useSecondaryCategories() ? self::getSecondaryCategories(true) : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => self::getProjects(true),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => self::useLocations() ? self::getLocations(true) : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => self::useCostCenters() ? self::getCostCenters(true) : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
            'wallet' => $wallet,
            'has_multiple_wallets' => Wallet::count() > 1,
            'intermediate_balances' => ($sortColumn == 'receipt_no' && self::showIntermediateBalances()) ? self::getIntermediateBalances($wallet) : null,
        ]);
    }

    private static function createIndexQuery(array $filter, string $sortColumn, string $sortOrder)
    {
        return MoneyTransaction::query()
            ->forWallet(resolve(CurrentWalletService::class)->get())
            ->forFilter($filter)
            ->orderBy($sortColumn, $sortOrder)
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CurrentWalletService $currentWallet)
    {
        $this->authorize('create', MoneyTransaction::class);

        $wallets = Wallet::orderBy('name')
            ->get()
            ->filter(fn ($wallet) => request()->user()->can('view', $wallet))
            ->map(fn ($wallet) => [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'new_receipt_no' => self::getNextFreeReceiptNo($wallet),
            ]);
        if ($wallets->isEmpty()) {
            return redirect()->route('accounting.wallets.change');
        }
        $wallet = $currentWallet->get();

        return view('accounting.transactions.create', [
            'attendees' => MoneyTransaction::attendees(),
            'categories' => self::getCategories(),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => self::useSecondaryCategories() ? self::getSecondaryCategories() : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => self::getProjects(),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => self::useLocations() ? self::getLocations() : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => self::useCostCenters() ? self::getCostCenters() : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
            'wallet' => $wallet,
            'wallets' => $wallets,
        ]);
    }

    private static function getNextFreeReceiptNo(?Wallet $wallet = null)
    {
        return optional(MoneyTransaction::selectRaw('MAX(receipt_no) as val')
            ->when($wallet !== null, fn ($qry) => $qry->forWallet($wallet))
            ->first())
            ->val + 1;
    }

    private static function getCategories(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.categories')) {
            return collect(Setting::get('accounting.transactions.categories'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::categories();
    }

    private static function useSecondaryCategories(): bool
    {
        return Setting::get('accounting.transactions.use_secondary_categories') ?? false;
    }

    private static function getSecondaryCategories(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.secondary_categories')) {
            return collect(Setting::get('accounting.transactions.secondary_categories'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::secondaryCategories();
    }

    private static function getProjects(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.projects')) {
            return collect(Setting::get('accounting.transactions.projects'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::projects();
    }

    private static function useLocations(): bool
    {
        return Setting::get('accounting.transactions.use_locations') ?? false;
    }

    private static function getLocations(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.locations')) {
            return collect(Setting::get('accounting.transactions.locations'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::locations();
    }

    private static function useCostCenters(): bool
    {
        return Setting::get('accounting.transactions.use_cost_centers') ?? false;
    }

    private static function getCostCenters(?bool $onlyExisting = false): array
    {
        if (! $onlyExisting && Setting::has('accounting.transactions.cost_centers')) {
            return collect(Setting::get('accounting.transactions.cost_centers'))
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::costCenters();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Accounting\StoreTransaction  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransaction $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('create', MoneyTransaction::class);

        $transaction = new MoneyTransaction();
        $transaction->date = $request->date;
        $transaction->receipt_no = $request->receipt_no;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->attendee = $request->attendee;
        $transaction->category = $request->category;
        if (self::useSecondaryCategories()) {
            $transaction->secondary_category = $request->secondary_category;
        }
        $transaction->project = $request->project;
        if (self::useLocations()) {
            $transaction->location = $request->location;
        }
        if (self::useCostCenters()) {
            $transaction->cost_center = $request->cost_center;
        }
        $transaction->description = $request->description;
        $transaction->remarks = $request->remarks;

        $transaction->wallet()->associate($request->input('wallet_id'));
        $this->authorize('view', $transaction->wallet);

        if (isset($request->receipt_picture) && is_array($request->receipt_picture)) {
            for ($i = 0; $i < count($request->receipt_picture); $i++) {
                $transaction->addReceiptPicture($request->file('receipt_picture')[$i]);
            }
        }

        $transaction->save();

        if ($transaction->wallet->id !== optional($currentWallet->get())->id) {
            $currentWallet->set($transaction->wallet);
        }

        return redirect()
            ->route($request->submit == 'save_and_continue' ? 'accounting.transactions.create' : 'accounting.transactions.index')
            ->with('info', __('accounting.transactions_registered'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        $sortColumn = session('accounting.sortColumn', 'created_at');
        $sortOrder = session('accounting.sortOrder', 'desc');
        $filter = session('accounting.filter', []);
        $query = self::createIndexQuery($filter, $sortColumn, $sortOrder);
        // TODO: can this be optimized, e.g. with a cursor??
        $res = $query->select('id')->get()->pluck('id')->toArray();
        $prev_id = null;
        $next_id = null;
        $cnt = count($res);
        for ($i = 0; $i < $cnt; $i++) {
            $prev_id = $i > 0 ? $res[$i - 1] : null;
            $next_id = $i < $cnt - 1 ? $res[$i + 1] : null;
            if ($res[$i] == $transaction->id) {
                break;
            }
        }

        return view('accounting.transactions.show', [
            'transaction' => $transaction,
            'prev_id' => $prev_id,
            'next_id' => $next_id,
        ]);
    }

    public function snippet(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        return view('accounting.transactions.snippet', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        return view('accounting.transactions.edit', [
            'transaction' => $transaction,
            'attendees' => MoneyTransaction::attendees(),
            'categories' => self::getCategories(),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => self::useSecondaryCategories() ? self::getSecondaryCategories() : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => self::getProjects(),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => self::useLocations() ? self::getLocations() : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => self::useCostCenters() ? self::getCostCenters() : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTransaction $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $transaction->date = $request->date;
        $transaction->receipt_no = $request->receipt_no;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->attendee = $request->attendee;
        $transaction->category = $request->category;
        if (self::useSecondaryCategories()) {
            $transaction->secondary_category = $request->secondary_category;
        }
        $transaction->project = $request->project;
        if (self::useLocations()) {
            $transaction->location = $request->location;
        }
        if (self::useCostCenters()) {
            $transaction->cost_center = $request->cost_center;
        }
        $transaction->description = $request->description;
        $transaction->remarks = $request->remarks;

        if (isset($request->remove_receipt_picture) && is_array($request->remove_receipt_picture)) {
            foreach ($request->remove_receipt_picture as $picture) {
                $transaction->deleteReceiptPicture($picture);
            }
        }
        elseif (isset($request->receipt_picture) && is_array($request->receipt_picture)) {
            for ($i = 0; $i < count($request->receipt_picture); $i++) {
                $transaction->addReceiptPicture($request->file('receipt_picture')[$i]);
            }
        }

        $transaction->save();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting.transactions_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(MoneyTransaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting.transactions_deleted'));
    }

    protected function exportAuthorize()
    {
        $this->authorize('viewAny', MoneyTransaction::class);
    }

    protected function exportView(): string
    {
        return 'accounting.transactions.export';
    }

    protected function exportViewArgs(): array
    {
        $filter = session('accounting.filter', []);
        return [
            'columnsSelection' => [
                'all' => __('app.all'),
                'webling' => __('accounting.selection_for_webling'),
            ],
            'columns' => 'all',
            'groupings' => [
                'none' => __('app.none'),
                'monthly' => __('app.monthly'),
            ],
            'grouping' => 'none',
            'selections' => ! empty($filter) ? [
                'all' => __('app.all_records'),
                'filtered' => __('app.selected_records_according_to_current_filter'),
            ] : null,
            'selection' => 'all',
        ];
    }

    protected function exportValidateArgs(): array
    {
        return [
            'columns' => [
                'required',
                Rule::in(['all', 'webling']),
            ],
            'grouping' => [
                'required',
                Rule::in(['none', 'monthly']),
            ],
            'selection' => [
                'nullable',
                Rule::in(['all', 'filtered']),
            ],
        ];
    }

    protected function exportFilename(Request $request): string
    {
        $wallet = resolve(CurrentWalletService::class)->get();
        return config('app.name') . ' ' . __('accounting.accounting') . ' [' . $wallet->name . '] (' . Carbon::now()->toDateString() . ')';
    }

    protected function exportExportable(Request $request)
    {
        $filter = $request->selection == 'filtered' ? session('accounting.filter', []) : [];
        if ($request->grouping == 'monthly') {
            return new MoneyTransactionsMonthsExport($filter);
        }
        if ($request->columns == 'webling') {
            return new WeblingMoneyTransactionsExport($filter);
        }
        return new MoneyTransactionsExport($filter);
    }

    public function undoBooking(MoneyTransaction $transaction)
    {
        $this->authorize('undoBooking', $transaction);

        if ($transaction->external_id != null && Entrygroup::find($transaction->external_id) != null) {
            return redirect()
                ->route('accounting.transactions.show', $transaction)
                ->with('error', __('accounting.transaction_not_updated_external_record_still_exists'));
        }

        $transaction->booked = false;
        $transaction->external_id = null;
        $transaction->save();

        return redirect()
            ->route('accounting.transactions.show', $transaction)
            ->with('info', __('accounting.transactions_updated'));
    }

    private static function showIntermediateBalances(): bool
    {
        return Setting::get('accounting.transactions.show_intermediate_balances') ?? false;
    }

    private static function getIntermediateBalances(Wallet $wallet): array
    {
        $transactions = MoneyTransaction::query()
            ->forWallet($wallet)
            ->orderBy('receipt_no', 'ASC')
            ->get();

        $intermediate_balances = [];
        $intermediate_balance = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->type == 'income') {
                $intermediate_balance += $transaction->amount;
            } else {
                $intermediate_balance -= $transaction->amount;
            }
            $intermediate_balances[$transaction->id] = $intermediate_balance;
        }

        return $intermediate_balances;
    }

    public function print(MoneyTransaction $transaction)
    {
        $title = __('accounting.pdf_title') . ' ' . $transaction->receipt_no;
        $html = $this->renderPrintReceipts(collect([$transaction]), $title);

        $filename = mb_ereg_replace("([^\w\d\-_~,\[\]\(\).])", '_', $title);
        return $this->toPdf($html, $filename);
    }

    public function printMultiple()
    {
        return view('accounting.transactions.print_multiple', [
            'ranges' => MoneyTransaction::query()
                ->selectRaw('MIN(date) as date_min, MAX(date) as date_max, MIN(receipt_no) as receipt_min, MAX(receipt_no) as receipt_max')
                ->forWallet(resolve(CurrentWalletService::class)->get())
                ->first(),
        ]);
    }

    public function doPrintMultiple(Request $request)
    {
        $request->validate([
            'format' => 'required|in:table,receipts',
            'range_type' => 'required|in:receipt,date,all',
            'receipt_from' => 'required_if:range_type,receipt|int',
            'receipt_to' => 'required_if:range_type,receipt|int',
            'date_from' => 'required_if:range_type,date|date',
            'date_to' => 'required_if:range_type,date|date',
        ]);

        // This query meight exceed the default excecution time limit of 30 seconds
        set_time_limit(300);

        $wallet = resolve(CurrentWalletService::class)->get();

        $transactions = MoneyTransaction::query()
            ->forWallet($wallet)
            ->when($request->range_type == 'receipt',
                fn ($q) => $q->whereBetween('receipt_no', [ $request->receipt_from, $request->receipt_to ]))
            ->when($request->range_type == 'date',
                fn ($q) => $q->whereBetween('date', [ $request->date_from, $request->date_to ]))
            ->orderBy('receipt_no')
            ->get();

        if (count($transactions) === 0) {
            throw ValidationException::withMessages([
                'range_type' => __('accounting.no_transactions_found_for_range')
            ]);
        }

        $title = __('accounting.pdf_title_multiple');
        if ($request->range_type == 'receipt') {
            $title .= ' ' . $request->receipt_from . ' - ' . $request->receipt_to;
        } else if ($request->range_type == 'date') {
            $title .= ' ' . $request->date_from . ' - ' . $request->date_to;
        }
        $title .= ' ' . $wallet->name;

        if ($request->format == 'table') {
            $html = $this->renderPrintTable($transactions, $title, $wallet);
        } else {
            $html = $this->renderPrintReceipts($transactions, $title, isset($request->duplex_align));
        }

        $filename = mb_ereg_replace("([^\w\d\-_~,\[\]\(\).])", '_', $title);
        return $this->toPdf($html, $filename, $request->format == 'table' ? 'landscape' : 'portrait');
    }

    public function toPdf($html, $filename, $orientation = 'portrait')
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', $orientation);
        $dompdf->set_option('dpi', 300);
        $dompdf->set_option('isPhpEnabled', true);

        $dompdf->render();

        return new Response($dompdf->output(), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="'.$filename.'.pdf"',
        ));
    }

    private function renderPrintReceipts($transactions, $title, $duplex_align = false) {
        $receipts = $transactions->map(fn ($transaction) => [
            'transaction' => $transaction,
            'title' => __('accounting.receipt')
                    . ' ' . $transaction->receipt_no
                    . ' - ' . $transaction->wallet()->first()->name,
            'receipt_pictures' => collect($transaction->receipt_pictures)->map(function ($picture) {
                if (Storage::mimeType($picture) == "application/pdf") {
                    return self::pdfToImage($picture);
                } else {
                    return [ Storage::path($picture) ];
                }
            })
            ->collapse()
            ->map(function ($path) {
                // check wheter image dimensions are taller or wider then the page dimensions
                $size = getimagesize($path);
                if ($size[0] * 706 / 527 < $size[1]) {
                    $scale = 'scale-height';
                } else {
                    $scale = 'scale-width';
                }

                return [
                    'path' => $path,
                    'scale' => $scale,
                ];
            }),
        ]);

        return view('accounting.transactions.receipt_pdf', [
            'receipts' => $receipts,
            'title' => $title,
            'duplex_align' => $duplex_align,
        ])->render();
    }

    private function renderPrintTable($transactions, $title, $wallet)
    {
        $intermediate_balances = self::getIntermediateBalances($wallet);

        $preceding_transaction = MoneyTransaction::forWallet($wallet)
            ->where('receipt_no', '<', $transactions->pluck('receipt_no')->min())
            ->orderBy('receipt_no', 'DESC')
            ->first();
        if ($preceding_transaction !== null) {
            $start_balance = $intermediate_balances[$preceding_transaction->id];
        } else {
            $start_balance = 0;
        }

        return view('accounting.transactions.table_pdf', [
            'transactions' => $transactions,
            'intermediate_balances' => $intermediate_balances,
            'start_balance' => $start_balance,
            'end_balance' => count($transactions) > 0 ? $intermediate_balances[$transactions->last()->id] : 0,
            'title' => $title,
            'use_secondary_categories' => self::useSecondaryCategories(),
            'use_locations' => self::useLocations(),
            'use_cost_centers' => self::useCostCenters(),
        ])->render();
    }

    private function pdfToImage(string $pdf)
    {
        $dir = Storage::path(pathinfo($pdf)['dirname']);
        $prefix = pathinfo($pdf)['filename'] . '_page';
        $extension = 'jpeg';

        $cached_images = glob("{$dir}/{$prefix}*.{$extension}");

        if (is_array($cached_images) && count($cached_images) > 0) {
            return $cached_images;
        }

        $pdf = new \Spatie\PdfToImage\Pdf(Storage::path($pdf));
        $pdf->setOutputFormat($extension);

        return $pdf->saveAllPagesAsImages($dir, $prefix);
    }
}
