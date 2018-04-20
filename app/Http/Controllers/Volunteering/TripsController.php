<?php

namespace App\Http\Controllers\Volunteering;

use App\VolunteerTrip;
use App\VolunteerJob;
use App\Volunteer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Requests\Volunteering\StoreVolunteerTrip;
use App\Http\Requests\GetCalendarEvents;

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
    public function calendarEvents(GetCalendarEvents $request)
    {
        $this->authorize('list', VolunteerTrip::class);
        
        $qry = VolunteerTrip
            ::whereIn('status', ['applied', 'approved'])
            ->orderBy('arrival', 'asc');
        
        if ($request->start != null) {
            $qry->where(function($q) use($request) {
                $q->whereDate('departure', '>=', new Carbon($request->start, $request->timezone))
                    ->orWhereNull('departure');
            });
        }
        if ($request->end != null) {
            $qry->whereDate('arrival', '<=', new Carbon($request->end, $request->timezone));
        }

        return response()->json(
            $qry->get()
                ->map(function($e) use($request) {
                    if ($request->end != null && $e->departure == null) {
                        $end = (new Carbon($request->end, $request->timezone));
                    } else {
                        $end = optional($e->departure)->addDay();
                    }
            
                    $color = null;
                    if ($e->status == 'ongoing') {
                        $color = '#28a745';
                    } else if ($e->status == 'completed') {
                        $color = '#6c757d';
                    } else if ($e->status == 'approved') {
                        $color = '#17a2b8';
                    } else if ($e->status == 'applied') {
                        $color = '#ffc107';
                    }
                    $title = $e->volunteer->name;
                    if ($e->duration != null) {
                        $title.= ' (' . $e->duration . ' ' . trans_choice('app.day_days', $e->duration) . ')';
                    }
            
                    return [
                        'id' => $e->id,
                        'title' => $title,
                        'start' => $e->arrival->toDateString(),
                        'end' => optional($end)->toDateString(),
                        'allDay' => true,
                        'url' => route('volunteering.trips.show', $e),
                        'resourceId' => optional($e->job)->id,
                        'color' => $color,
                    ];
                })
        );
    }

    public function calendarResources()
    {
        $this->authorize('list', VolunteerJob::class);

        return response()->json(
            VolunteerJob
            ::orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->map(function($e){
                return [
                    'id' => $e->id,
                    'title' => $e->title[\App::getLocale()],
                    'parentId' => null,
                ];
            })
        );
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
