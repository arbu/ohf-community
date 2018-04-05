<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerTrip;
use App\VolunteerJob;
use App\Volunteer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Requests\Volunteering\StoreVolunteerTrip;

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
        $this->authorize('create', VolunteerTrip::class);

        return view('volunteering.trips.create', [
            'jobs' => self::getJobs(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Volunteering\StoreVolunteerTrip  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVolunteerTrip $request)
    {
        $this->authorize('create', VolunteerTrip::class);

        $trip = new VolunteerTrip();
        $trip->arrival = $request->arrival;
        $trip->departure = $request->departure;
        $trip->remarks = $request->remarks;
        $trip->volunteer()->associate(Volunteer::findOrFail($request->volunteer));
        $trip->job()->associate(VolunteerJob::findOrFail($request->job));
        $trip->save();

        return redirect()->route('volunteering.trips.index')
            ->with('success', __('volunteering.trip_registered'));
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

    private static function getJobs() {
        return VolunteerJob
            ::orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->mapWithKeys(function($e) {
                $lang = \App::getLocale();
                return [ $e->id => $e->title[$lang] ?? implode(' / ', $e->title) ];
            })
            ->toArray();        
    }
}
