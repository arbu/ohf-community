<?php

namespace Modules\Volunteers\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Modules\Volunteers\Entities\Volunteer;
use Modules\Volunteers\Transformers\Volunteer as VolunteerResource;
use Modules\Volunteers\Transformers\VolunteerCollection;
use Modules\Volunteers\Http\Requests\StoreVolunteer;
use Modules\Volunteers\Http\Requests\UpdateVolunteer;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VolunteersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('list', Volunteer::class);

        if ($request->scope == 'active') {
            $qry = Volunteer::active();
        } else if ($request->scope == 'applied') {
            $qry = Volunteer::applied();
        } else if ($request->scope == 'future') {
            $qry = Volunteer::future();
        } else if ($request->scope == 'previous') {
            $qry = Volunteer::previous();
        } else {
            $qry = Volunteer::query();
        }
        return new VolunteerCollection($qry->orderBy('first_name')->orderBy('last_name')->get());
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreVolunteer $request
     * @return Response
     */
    public function store(StoreVolunteer $request)
    {
        $this->authorize('create', Volunteer::class);

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
        $this->authorize('view', $volunteer);

        return new VolunteerResource($volunteer->load([
            'stays' => function ($query) {
                $query->orderBy('arrival', 'asc');
            }
        ]));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateVolunteer $request
     * @param Volunteer $volunteer
     * @return Response
     */
    public function update(UpdateVolunteer $request, Volunteer $volunteer)
    {
        $this->authorize('update', $volunteer);

        $volunteer->fill($request->all());
        $volunteer->save();
        return (new VolunteerResource($volunteer->load([
            'stays' => function ($query) {
                $query->orderBy('arrival', 'asc');
            }
        ])))->response(200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Volunteer $volunteer)
    {
        $this->authorize('delete', $volunteer);

        $volunteer->delete();
        return response()->json([], 204);
    }
}
