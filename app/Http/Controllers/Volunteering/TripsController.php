<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerTrip;
use App\VolunteerJob;
use App\Volunteer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Requests\Volunteering\StoreVolunteerTrip;
use App\Http\Resources\VolunteerTripResource;
use App\Http\Resources\VolunteerJobResource;

class TripsController extends Controller
{
    public function __construct() {
        VolunteerTripResource::withoutWrapping();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('list', VolunteerTrip::class);

        return view('volunteering.trips.index', [
            'data' => [
                'applications' => VolunteerTrip
                    ::where('status', 'applied')
                    ->orderBy('arrival', 'asc')
                    ->get(),
                'ongoing_trips' => VolunteerTrip
                    ::whereDate('arrival', '<=', Carbon::today())
                    ->where('status', 'approved')
                    ->where(function($q){
                        $q->whereDate('departure', '>=', Carbon::today())
                            ->orWhereNull('departure');
                    })
                    ->orderBy('departure', 'asc')
                    ->get(),
                'upcoming_trips' => VolunteerTrip
                    ::whereDate('arrival', '>', Carbon::today())
                    ->where('status', 'approved')
                    ->orderBy('arrival', 'asc')
                    ->get(),
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive()
    {
        $this->authorize('list', VolunteerTrip::class);

        return view('volunteering.trips.archive', [
            'data' => [
                'completed_trips' => VolunteerTrip
                    ::whereDate('departure', '<', Carbon::today())
                    ->where('status', 'approved')
                    ->orderBy('departure', 'desc')
                    ->get(),
                'denied_applications' => VolunteerTrip
                    ::where('status', 'denied')
                    ->orderBy('arrival', 'asc')
                    ->get(),
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        $this->authorize('list', VolunteerTrip::class);

        return view('volunteering.trips.calendar');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarEvents()
    {
        $this->authorize('list', VolunteerTrip::class);
        
        $trips = VolunteerTrip
            ::where('status', 'approved')
            ->orderBy('arrival', 'asc')
            ->get();

        return VolunteerTripResource::collection($trips);
    }

    public function calendarResources()
    {
        $this->authorize('list', VolunteerJob::class);
        
        $jobs = VolunteerJob
            ::orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->get();

        return VolunteerJobResource::collection($jobs);
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
        $trip->need_accommodation = isset($request->need_accommodation);
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
     * @param  \App\VolunteerTrip  $trip
     * @return \Illuminate\Http\Response
     */
    public function show(VolunteerTrip $trip)
    {
        $this->authorize('view', $trip);

        return view('volunteering.trips.show', [
            'trip' => $trip,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VolunteerTrip  $trip
     * @return \Illuminate\Http\Response
     */
    public function edit(VolunteerTrip $trip)
    {
        $this->authorize('update', $trip);

        return view('volunteering.trips.edit', [
            'trip' => $trip,
            'jobs' => self::getJobs(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VolunteerTrip  $trip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VolunteerTrip $trip)
    {
        $this->authorize('update', VolunteerTrip::class);

        $trip->arrival = $request->arrival;
        $trip->departure = $request->departure;
        $trip->need_accommodation = isset($request->need_accommodation);
        $trip->remarks = $request->remarks;
        $trip->job()->dissociate();
        $trip->job()->associate(VolunteerJob::findOrFail($request->job));
        $trip->save();

        return redirect()->route('volunteering.trips.show', $trip)
            ->with('success', __('volunteering.trip_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VolunteerTrip  $trip
     * @return \Illuminate\Http\Response
     */
    public function destroy(VolunteerTrip $trip)
    {
        $this->authorize('delete', $trip);

        $trip->delete();

        return redirect()->route('volunteering.trips.index')
            ->with('success', __('volunteering.trip_deleted'));
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
