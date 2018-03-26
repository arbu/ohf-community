<?php

namespace App\Http\Controllers\Volunteering;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerProfile;
use App\Http\Requests\StoreTrip;
use App\Util\CountriesExtended;
use App\Volunteer;
use App\Trip;
use Illuminate\Support\Facades\Auth;

class VolunteersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $this->authorize('list', Volunteer::class);

        return view('volunteers.index', [
            'volunteers' => Volunteer::orderBy('created_at', 'desc')
                ->paginate()
        ]);
    }

    function create() {
        $this->authorize('create', Volunteer::class);

        return view('volunteers.create', [ ]);
    }

    function show(Volunteer $volunteer) {
        $this->authorize('view', $volunteer);

        return view('volunteers.show', [
            'volunteer' => $volunteer
        ]);
    }



    function showProfile() {
        $volunteer = Auth::user()->volunteer;
        if ($volunteer == null) {
            return redirect()->route('volunteers.editProfile');
        }
        return view('volunteers.showProfile', [
            'volunteer' => $volunteer
        ]);
    }

    function editProfile() {
        return view('volunteers.editProfile', [
            'countries' => CountriesExtended::getList('en')
        ]);
    }

    function updateProfile(StoreVolunteerProfile $request) {
        $volunteer = Auth::user()->volunteer;
        if ($volunteer == null) {
            $volunteer = new Volunteer();
            Auth::user()->volunteer()->save($volunteer);
        }
        $volunteer->address = $request->address;
        $volunteer->zip = $request->zip;
        $volunteer->city = $request->city;
        $volunteer->country = $request->country;
        $volunteer->nationality = $request->nationality;
        $volunteer->gender = $request->gender;
        $volunteer->birthdate = $request->birthdate;
        $volunteer->phone = $request->phone;
        $volunteer->skype = $request->skype;
        $volunteer->save();

        return redirect()->route('volunteers.showProfile')
            ->with('success', 'Your data has been updated');
    }
    
    function createTrip() {
        if (Auth::user()->volunteer == null) {
            return redirect()->route('volunteers.editProfile');
        }
        
        return view('volunteers.createTrip', [
            'type_of_work' => [
                'Activities',
                'Building',
                'Cooking',
                'Coordination',
                'Driving'
            ]
        ]);
    }
    
    function storeTrip(StoreTrip $request) {
        $trip = new Trip();
        $trip->arrival = $request->arrival;
        $trip->departure = $request->departure;
        Auth::user()->volunteer->trips()->save($trip);
        return redirect()->route('volunteers.showProfile')
            ->with('success', 'Your application has been registered');
    }
}
