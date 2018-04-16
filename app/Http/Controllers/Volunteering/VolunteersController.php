<?php

namespace App\Http\Controllers\Volunteering;

use App\Http\Controllers\Controller;
use App\Volunteer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JeroenDesloovere\VCard\VCard;
use Carbon\Carbon;
use App\Http\Requests\FilterQuery;

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
     * Filtered list of volunteers, JSON
     */
    public function filter(FilterQuery $request) {
        $qry = Volunteer::limit(10)
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc');
        if (isset($request->query()['query'])) {
            $qry->where('first_name', 'LIKE', '%' . $request->query()['query'] . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request->query()['query'] . '%')
                ->orWhere(DB::raw('CONCAT(first_name, \' \', last_name)'), 'LIKE', '%' . $request->query()['query'] . '%');
        }
        $persons = $qry->get()
            ->map(function($e){ 
                $val = $e->family_name . ' '. $e->name;
                if (!empty($e->date_of_birth)) {
                    $val.= ', ' . $e->date_of_birth;
                }
                if (!empty($e->nationality)) {
                    $val.= ', ' . $e->nationality;
                }
                return [
                    'value' => trim($val),
                    'data' => $e->id,
                ]; 
            });
        return response()->json(["suggestions" => $persons]);
    }

    /**
     * Export list of volunteers
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
            $excel->getActiveSheet()->setAutoFilter(
                $excel->getActiveSheet()->calculateWorksheetDimension()
            );
        })->export('xlsx');
    }

    /**
     * Show volunteer
     */
    function show(Volunteer $volunteer) {
        $this->authorize('view', $volunteer);

        return view('volunteering.volunteers.show', [
            'volunteer' => $volunteer,
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

}
