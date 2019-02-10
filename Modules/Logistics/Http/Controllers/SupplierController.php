<?php

namespace Modules\Logistics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Modules\Logistics\Entities\Supplier;
use Modules\Logistics\Http\Requests\CreateSupplierRequest;
use Modules\Logistics\Http\Requests\UpdateSupplierRequest;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Supplier::class);

        return view('logistics::suppliers.index', [
            'suppliers' => Supplier::orderBy('name')->paginate(),            
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

        $supplier = new Supplier();
        $supplier->fill($request->all());
        $supplier->save();

        return redirect()
            ->route('logistics.suppliers.index')
            ->with('success', __('logistics::suppliers.supplier_created'));
    }

    /**
     * Show the specified resource.
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        return view('logistics::suppliers.show', [
            'supplier' => $supplier,
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

}