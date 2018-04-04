<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerJob;
use App\VolunteerJobCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('list', VolunteerJob::class);

        return view('volunteering.jobs.index', [
            'jobs' => VolunteerJob
                ::orderBy('order', 'asc')
                ->orderBy('title', 'asc')
                ->get(),
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
        $this->authorize('create', VolunteerJob::class);

        return view('volunteering.jobs.create', [
            'categories' => self::getCategories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', VolunteerJob::class);

        $job = new VolunteerJob();
        $job->title = $request->title;
        $job->description = $request->description;
        $job->available_dates = $request->available_dates;
        $job->minimum_stay = $request->minimum_stay;
        $job->requirements = $request->requirements;
        $job->order = $request->order;
        $job->category()->associate(VolunteerJobCategory::findOrFail($request->category));
        $job->save();

        return redirect()->route('volunteering.jobs.index')
            ->with('success', __('volunteering.job_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VolunteerJob  $volunteerJob
     * @return \Illuminate\Http\Response
     */
    public function show(VolunteerJob $job)
    {
        $this->authorize('view', $job);

        return view('volunteering.jobs.show', [
            'job' => $job,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VolunteerJob  $volunteerJob
     * @return \Illuminate\Http\Response
     */
    public function edit(VolunteerJob $job)
    {
        $this->authorize('update', $job);

        return view('volunteering.jobs.edit', [
            'job' => $job,
            'categories' => self::getCategories(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VolunteerJob  $volunteerJob
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VolunteerJob $job)
    {
        $this->authorize('update', $job);

        $job->title = $request->title;
        $job->description = $request->description;
        $job->available_dates = $request->available_dates;
        $job->minimum_stay = $request->minimum_stay;
        $job->requirements = $request->requirements;
        $job->order = $request->order;
        $job->category()->dissociate();
        $job->category()->associate(VolunteerJobCategory::findOrFail($request->category));
        $job->save();

        return redirect()->route('volunteering.jobs.show', $job)
            ->with('success', __('volunteering.job_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VolunteerJob  $volunteerJob
     * @return \Illuminate\Http\Response
     */
    public function destroy(VolunteerJob $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('volunteering.jobs.index')
            ->with('success', __('volunteering.job_deleted'));
    }

    private static function getCategories() {
        return VolunteerJobCategory
            ::orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->mapWithKeys(function($e) {
                return [ $e->id => implode(' / ', $e->title) ];
            })
            ->toArray();        
    }
}
