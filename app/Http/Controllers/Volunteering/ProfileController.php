<?php

namespace App\Http\Controllers\Volunteering;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Volunteering\StoreVolunteerProfile;
use App\Http\Requests\StoreTrip;
use Illuminate\Support\Facades\Auth;
use App\VolunteerTrip;
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
        return view('volunteering.profile.edit', []);
    }

    function update(StoreVolunteerProfile $request) {
        $volunteer = Auth::user()->volunteer;
        if ($volunteer == null) {
            $volunteer = new Volunteer();
            Auth::user()->volunteer()->save($volunteer);
        }
        $volunteer->first_name = $request->first_name;
        $volunteer->last_name = $request->last_name;
        $volunteer->street = $request->street;
        $volunteer->zip = $request->zip;
        $volunteer->city = $request->city;
        $volunteer->country_name = $request->country_name;
        $volunteer->nationality = $request->nationality;
        $volunteer->passport_no = $request->passport_no;
        $volunteer->date_of_birth = $request->date_of_birth;
        $volunteer->gender = $request->gender;
        $volunteer->phone = $request->phone;
        $volunteer->whatsapp = $request->whatsapp;
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
        $trip = new VolunteerTrip();
        $trip->arrival = $request->arrival;
        $trip->departure = $request->departure;
        Auth::user()->volunteer->trips()->save($trip);
        return redirect()->route('volunteering.profile.show')
            ->with('success', 'Your application has been registered');
    }
}
