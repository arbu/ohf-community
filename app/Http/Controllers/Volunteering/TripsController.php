<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerTrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('list', VolunteerTrip::class);

        return view('volunteering.trips.index', [
            'current' => VolunteerTrip
                ::whereDate('arrival', '<=', Carbon::today())
                ->where(function($q){
                    $q->whereDate('departure', '>=', Carbon::today())
                        ->orWhereNull('departure');
                })
                ->orderBy('departure', 'asc')
                ->get(),
            'upcoming' => VolunteerTrip
                ::whereDate('arrival', '>', Carbon::today())
                ->orderBy('departure', 'asc')
                ->get(),
            'past' => VolunteerTrip
                ::whereDate('departure', '<', Carbon::today())
                ->orderBy('departure', 'asc')
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return \Illuminate\Http\Response
     */
    public function show(VolunteerTrip $volunteerTrip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return \Illuminate\Http\Response
     */
    public function edit(VolunteerTrip $volunteerTrip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VolunteerTrip $volunteerTrip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VolunteerTrip  $volunteerTrip
     * @return \Illuminate\Http\Response
     */
    public function destroy(VolunteerTrip $volunteerTrip)
    {
        //
    }
}
