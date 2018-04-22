<?php

namespace App\Http\Controllers\API\Volunteering;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VolunteerJob;
use App\Http\Resources\Volunteering\JobResource;

class JobsController extends Controller
{
    public function __construct() {
        JobResource::withoutWrapping();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VolunteerJob  $job
     * @return \Illuminate\Http\Response
     */
    public function show(VolunteerJob $job)
    {
        // authorization can only be done if there is a user context
        //$this->authorize('view', $job); 
        if (!$job->enabled) {
            return response(null, 401);
        }

        return new JobResource($job);      
    }
}
