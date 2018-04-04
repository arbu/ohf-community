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
            'categories' => VolunteerJobCategory
                ::orderBy('order', 'asc')
                ->orderBy('title', 'asc')
                ->get(),
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

        //
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
            'categories' => VolunteerJobCategory
                ::orderBy('order', 'asc')
                ->orderBy('title', 'asc')
                ->get(),
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

        //
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

        //
    }
}
