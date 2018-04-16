<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerJobCategory;
use App\Http\Requests\Volunteering\StoreVolunteerJobCategory;
use App\Http\Controllers\Controller;

class JobCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('list', VolunteerJobCategory::class);

        return view('volunteering.jobs.categories.index', [
            'categories' => VolunteerJobCategory
                ::orderBy('order', 'asc')
                ->orderBy('title', 'asc')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', VolunteerJobCategory::class);

        return view('volunteering.jobs.categories.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Volunteering\StoreVolunteerJobCategory  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVolunteerJobCategory $request)
    {
        $this->authorize('create', VolunteerJobCategory::class);

        $category = new VolunteerJobCategory();
        $category->title = $request->title;
        $category->order = $request->order;
        $category->save();

        return redirect()->route('volunteering.jobs.categories.index')
            ->with('success', __('volunteering.category_created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VolunteerJobCategory  $volunteerJobCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(VolunteerJobCategory $category)
    {
        $this->authorize('update', $category);

        return view('volunteering.jobs.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Volunteering\StoreVolunteerJobCategory  $request
     * @param  \App\VolunteerJobCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreVolunteerJobCategory $request, VolunteerJobCategory $category)
    {
        $this->authorize('update', $category);

        $category->title = $request->title;
        $category->order = $request->order;
        $category->save();

        return redirect()->route('volunteering.jobs.categories.index')
            ->with('success', __('volunteering.category_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VolunteerJobCategory  $volunteerJobCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(VolunteerJobCategory $category)
    {
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()->route('volunteering.jobs.categories.index')
            ->with('success', __('volunteering.category_deleted'));
    }
}
