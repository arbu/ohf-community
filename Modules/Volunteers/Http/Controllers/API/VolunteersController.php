<?php

namespace Modules\Volunteers\Http\Controllers\API;

use Modules\Volunteers\Entities\Volunteer;
use Modules\Volunteers\Transformers\Volunteer as VolunteerResource;
use Modules\Volunteers\Transformers\VolunteerCollection;
use Modules\Volunteers\Http\Requests\StoreVolunteer;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class VolunteersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        // TODO authorization
        if ($request->scope == 'active') {
            $data = Volunteer::active()->get();
        } else if ($request->scope == 'applied') {
            $data = Volunteer::applied()->get();
        } else if ($request->scope == 'future') {
            $data = Volunteer::future()->get();
        } else {
            $data = Volunteer::all();
        }
        return new VolunteerCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreVolunteer $request
     * @return Response
     */
    public function store(StoreVolunteer $request)
    {
        // TODO authorization
        $volunteer = new Volunteer();
        $volunteer->fill($request->all());
        $volunteer->save();
        return (new VolunteerResource($volunteer))->response(201);
    }

    /**
     * Show the specified resource.
     * @param Volunteer $volunteer
     * @return Response
     */
    public function show(Volunteer $volunteer)
    {
        // TODO authorization
        return new VolunteerResource($volunteer);
    }

    /**
     * Update the specified resource in storage.
     * @param StoreVolunteer $request
     * @param Volunteer $volunteer
     * @return Response
     */
    public function update(StoreVolunteer $request, Volunteer $volunteer)
    {
        // TODO authorization
        $volunteer->fill($request->all());
        $volunteer->save();
        return (new VolunteerResource($volunteer))->response(200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Volunteer $volunteer)
    {
        // TODO authorization
        $volunteer->delete();
        return response()->json([], 204);
    }
}
