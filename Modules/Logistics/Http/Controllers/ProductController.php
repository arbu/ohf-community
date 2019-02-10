<?php

namespace Modules\Logistics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Modules\Logistics\Entities\Product;
use Modules\Logistics\Http\Requests\CreateProductRequest;
use Modules\Logistics\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return Response
     */
    public function index()
    {
        // TODO $this->authorize('list', Product::class);

        return view('logistics::products.index', [
            'products' => Product::orderBy('name')->paginate(),            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return Response
     */
    public function create()
    {
        // TODO $this->authorize('create', Product::class);

        return view('logistics::create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param CreateProductRequest $request
     * @return Response
     */
    public function store(CreateProductRequest $request)
    {
        // TODO $this->authorize('create', Product::class);


    }

    /**
     * Show the specified resource.
     * 
     * @param Product $product
     * @return Response
    */
    public function show(Product $product)    
    {
        // TODO $this->authorize('view', $product);

        return view('logistics::products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product)
    {
        // TODO $this->authorize('update', $product);

        return view('logistics::edit');
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Product $product
     * @param UpdateProductRequest $request
     * @return Response
     */
    public function update(Product $product, UpdateProductRequest $request)
    {
        // TODO $this->authorize('update', $product);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param Product $product
     * @return Response
     */
    public function destroy(Product $product)
    {
        // TODO $this->authorize('delete', $product);
    }

    private static function getCategories() {
        return Product::select('category')
            ->orderBy('category')
            ->distinct()
            ->get()
            ->pluck('category')
            ->toArray();
    }
}
