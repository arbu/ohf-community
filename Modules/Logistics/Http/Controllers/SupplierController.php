<?php

namespace Modules\Logistics\Http\Controllers;

use App\PointOfInterest;
use App\Http\Controllers\Controller;

use Modules\Logistics\Entities\Supplier;
use Modules\Logistics\Http\Requests\CreateSupplierRequest;
use Modules\Logistics\Http\Requests\UpdateSupplierRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Validator;

use JeroenDesloovere\VCard\VCard;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('list', Supplier::class);

        // Validate request
        Validator::make($request->all(), [
            'display' => [
                'nullable', 
                'in:list,map',
            ],
        ])->validate();

        // Handle display session persistence
        if (isset($request->display)) {
            $request->session()->put('suppliers_display', $request->display);
        }
        $display = $request->session()->get('suppliers_display', 'list');

        // Handle filter session persistence
        if ($request->has('reset_filter') || ($request->has('filter') && $request->filter == null)) {
            $request->session()->forget('suppliers_filter');
        }
        if (isset($request->filter)) {
            $request->session()->put('suppliers_filter', $request->filter);
        }

        // Init query
        $query = Supplier::join('points_of_interest', 'points_of_interest.id', '=', 'logistics_suppliers.poi_id');

        // Filter
        if ($request->session()->has('suppliers_filter')) {
            $filter = $request->session()->get('suppliers_filter');
            $query->where(function($wq) use($filter) {
                return $wq->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere('name_local', 'LIKE', '%' . $filter . '%')
                    ->orWhere('address', 'LIKE', '%' . $filter . '%')
                    ->orWhere('address_local', 'LIKE', '%' . $filter . '%')
                    ->orWhere('category', 'LIKE', '%' . $filter . '%');
            });
        } else {
            $filter = null;
        }

        return view('logistics::suppliers.index', [
            'suppliers' => $query
                ->orderBy('points_of_interest.name')
                ->orderBy('points_of_interest.name_local')
                ->paginate(),
            'filter' => $filter,
            'display' => $display,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return Response
     */
    public function create()
    {
        $this->authorize('create', Supplier::class);

        return view('logistics::suppliers.create', [
            'categories' => self::getCategories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  CreateSupplierRequest $request
     * @return Response
     */
    public function store(CreateSupplierRequest $request)
    {
        $this->authorize('create', Supplier::class);

        $poi = new PointOfInterest();
        $poi->fill($request->all());
        $poi->save();

        $supplier = new Supplier();
        $supplier->fill($request->all());
        $supplier->poi()->associate($poi);
        $supplier->save();

        return redirect()
            ->route('logistics.suppliers.show', $supplier)
            ->with('success', __('logistics::suppliers.supplier_created'));
    }

    /**
     * Show the specified resource.
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function show(Supplier $supplier, Request $request)
    {
        $this->authorize('view', $supplier);

        // Validate request
        Validator::make($request->all(), [
            'display' => [
                'nullable', 
                'in:info,map,products',
            ],
        ])->validate();

        // Handle display session persistence
        if (isset($request->display)) {
            $request->session()->put('supplier_display', $request->display);
        }
        $display = $request->session()->get('supplier_display', 'info');

        return view('logistics::suppliers.show', [
            'supplier' => $supplier,
            'display' => $display,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        return view('logistics::suppliers.edit', [
            'supplier' => $supplier,
            'categories' => self::getCategories(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Supplier $supplier
     * @param  Request $request
     * @return Response
     */
    public function update(Supplier $supplier, UpdateSupplierRequest $request)
    {
        $this->authorize('update', $supplier);

        $supplier->poi->fill($request->all());
        $supplier->poi->save();

        $supplier->fill($request->all());
        $supplier->save();

        return redirect()
            ->route('logistics.suppliers.show', $supplier)
            ->with('success', __('logistics::suppliers.supplier_updated'));
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        $supplier->delete();

        return redirect()
            ->route('logistics.suppliers.index')
            ->with('success', __('logistics::suppliers.supplier_deleted'));
    }

    private static function getCategories() {
        return Supplier::select('category')
            ->orderBy('category')
            ->distinct()
            ->get()
            ->pluck('category')
            ->toArray();
    }

    /**
     * Download vcard
     * 
     * @param  \Modules\Logistics\Entities\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    function vcard(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        // define vcard
        $vcard = new VCard();
        
        // Company
        $vcard->addCompany($supplier->poi->name);
        
        // E-Mail
        if ($supplier->email != null) {
            $vcard->addEmail($supplier->email);
        }

        // Phone
        if ($supplier->phone != null) {
            $vcard->addPhoneNumber($supplier->phone, 'WORK');
        }

        // Website
        if ($supplier->website != null) {
            $vcard->addURL($supplier->website, 'WORK');
        }

        // TODO parse address
        //$vcard->addAddress(null, null, $supplier->street, $supplier->city, null, $supplier->zip, $supplier->country_name, 'WORK;POSTAL');

        // return vcard as a download
        return $vcard->download();
    }
}
