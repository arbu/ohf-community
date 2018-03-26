<?php

namespace App\Http\Controllers\Volunteering;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerProfile;
use App\Http\Requests\StoreTrip;
use App\Util\CountriesExtended;
use App\Volunteer;
use App\Trip;
use Illuminate\Support\Facades\Auth;
use JeroenDesloovere\VCard\VCard;
use Carbon\Carbon;

class VolunteersController extends Controller
{
    /**
     * List volunteers
     */
    function index() {
        $this->authorize('list', Volunteer::class);

        return view('volunteering.volunteers.index', [
            'volunteers' => Volunteer
                ::orderBy('first_name', 'asc')
                ->orderBy('last_name', 'asc')
                ->paginate(),
        ]);
    }

    /**
     * Export volunteers
     */
    function export() {
        $this->authorize('list', Volunteer::class);

        \Excel::create('OHF_Community_Volunteers_' . Carbon::now()->toDateString(), function($excel) {
            $excel->sheet(__('volunteering.volunteers'), function($sheet) {
                $sheet->setOrientation('landscape');
                $sheet->freezeFirstRow();
                $sheet->loadView('volunteering.volunteers.export',[
                    'volunteers' => Volunteer
                        ::orderBy('first_name', 'asc')
                        ->orderBy('last_name', 'asc')
                        ->get(),
                ]);
            });
            // $excel->getActiveSheet()->setAutoFilter(
            //     $excel->getActiveSheet()->calculateWorksheetDimension()
            // );
        })->export('xlsx');
    }

    /**
     * Show volunteer
     */
    function show(Volunteer $volunteer) {
        $this->authorize('view', $volunteer);

        return view('volunteering.volunteers.show', [
            'volunteer' => $volunteer
        ]);
    }

    /**
     * Download vcard
     */
    function vcard(Volunteer $volunteer) {
        $this->authorize('view', $volunteer);

        // define vcard
        $vcard = new VCard();
        $vcard->addName($volunteer->last_name, $volunteer->first_name, '', '', '');
        $vcard->addEmail($volunteer->user->email);
        $vcard->addPhoneNumber($volunteer->phone, 'HOME');
        $vcard->addBirthday($volunteer->date_of_birth);
        $vcard->addAddress(null, null, $volunteer->street, $volunteer->city, null, $volunteer->zip, $volunteer->country, 'HOME;POSTAL');

        // return vcard as a download
        return $vcard->download();
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
