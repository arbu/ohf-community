<?php

namespace App\Http\Controllers\Volunteering;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerProfile;
use App\Http\Requests\StoreTrip;
use App\Util\CountriesExtended;
use Illuminate\Support\Facades\Auth;
use App\Trip;
use App\Volunteer;

class ProfileController extends Controller
{
    function show() {
        $volunteer = Auth::user()->volunteer;
        if ($volunteer == null) {
            return redirect()->route('volunteering.profile.edit');
        }
        return view('volunteering.profile.show', [
            'volunteer' => $volunteer
        ]);
    }

    function edit() {
        return view('volunteering.profile.edit', [
            'countries' => CountriesExtended::getList('en')
        ]);
    }

    function update(StoreVolunteerProfile $request) {
        $volunteer = Auth::user()->volunteer;
        if ($volunteer == null) {
            $volunteer = new Volunteer();
            Auth::user()->volunteer()->save($volunteer);
        }
        $volunteer->street = $request->street;
        $volunteer->zip = $request->zip;
        $volunteer->city = $request->city;
        $volunteer->country = $request->country;
        $volunteer->nationality = $request->nationality;
        $volunteer->gender = $request->gender;
        $volunteer->date_of_birth = $request->date_of_birth;
        $volunteer->phone = $request->phone;
        $volunteer->skype = $request->skype;
        $volunteer->save();

        return redirect()->route('volunteering.profile.show')
            ->with('success', 'Your data has been updated');
    }
    
    function createTrip() {
        if (Auth::user()->volunteer == null) {
            return redirect()->route('volunteering.profile.edit');
        }
        
        return view('volunteering.profile.createTrip', [
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
        return redirect()->route('volunteering.profile.show')
            ->with('success', 'Your application has been registered');
    }
}
