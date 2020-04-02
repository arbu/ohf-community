<?php

namespace App\Http\Controllers\Fundraising;

use App\Exports\Fundraising\DonorsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fundraising\StoreDonor;
use App\Models\Fundraising\Donation;
use App\Models\Fundraising\Donor;
use App\Tag;
use Carbon\Carbon;
use Countries;
use Illuminate\Http\Request;
use JeroenDesloovere\VCard\VCard;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list', Donor::class);

        return view('fundraising.donors.index', [
            'tags' => Tag::has('donors')->orderBy('name')->get()->pluck('name', 'slug'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Donor::class);

        return view('fundraising.donors.create', [
            'salutations' => Donor::salutations(),
            'countries' => array_values(Countries::getList(\App::getLocale())),
            'languages' => Donor::languages(),
            'tag_suggestions' => self::getTagSuggestions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Fundraising\StoreDonor  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDonor $request)
    {
        $this->authorize('create', Donor::class);

        $donor = new Donor();
        $donor->salutation = $request->salutation;
        $donor->first_name = $request->first_name;
        $donor->last_name = $request->last_name;
        $donor->company = $request->company;
        $donor->street = $request->street;
        $donor->zip = $request->zip;
        $donor->city = $request->city;
        $donor->country_name = $request->country_name;
        $donor->email = $request->email;
        $donor->phone = $request->phone;
        $donor->language = $request->language;
        $donor->remarks = $request->remarks;
        $donor->save();

        // Tags
        $donor->syncTags(self::splitTags($request->tags));

        return redirect()
            ->route('fundraising.donors.show', $donor)
            ->with('success', __('fundraising.donor_added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fundraising\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function show(Donor $donor)
    {
        $this->authorize('view', $donor);

        return view('fundraising.donors.show', [
            'donor' => $donor,
            'donations' => $donor->donations()->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(),
            'currencies' => config('fundraising.currencies'),
            'channels' => Donation::channels(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fundraising\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function edit(Donor $donor)
    {
        $this->authorize('update', $donor);

        return view('fundraising.donors.edit', [
            'donor' => $donor,
            'salutations' => Donor::salutations(),
            'countries' => array_values(Countries::getList(\App::getLocale())),
            'languages' => Donor::languages(),
            'tag_suggestions' => self::getTagSuggestions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Fundraising\StoreDonor  $request
     * @param  \App\Models\Fundraising\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDonor $request, Donor $donor)
    {
        $this->authorize('update', $donor);

        $donor->salutation = $request->salutation;
        $donor->first_name = $request->first_name;
        $donor->last_name = $request->last_name;
        $donor->company = $request->company;
        $donor->street = $request->street;
        $donor->zip = $request->zip;
        $donor->city = $request->city;
        $donor->country_name = $request->country_name;
        $donor->email = $request->email;
        $donor->phone = $request->phone;
        $donor->language = $request->language;
        $donor->remarks = $request->remarks;
        $donor->save();

        // Tags
        $donor->syncTags(self::splitTags($request->tags));

        return redirect()->route('fundraising.donors.show', $donor)
            ->with('success', __('fundraising.donor_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fundraising\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donor $donor)
    {
        $this->authorize('delete', $donor);

        $donor->tags()->detach();

        $donor->delete();
        return redirect()->route('fundraising.donors.index')
            ->with('success', __('fundraising.donor_deleted'));
    }

    /**
     * Exports a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $this->authorize('list', Donor::class);

        $file_name = config('app.name') . ' - ' . __('fundraising.donors') . ' (' . Carbon::now()->toDateString() . ')';
        $extension = 'xlsx';

        return (new DonorsExport())->download($file_name . '.' . $extension);
    }

    /**
     * Download vcard
     *
     * @param  \App\Models\Fundraising\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function vcard(Donor $donor)
    {
        $this->authorize('view', $donor);

        // define vcard
        $vcard = new VCard();
        if ($donor->company != null) {
            $vcard->addCompany($donor->company);
        }
        if ($donor->last_name != null || $donor->first_name != null) {
            $vcard->addName($donor->last_name, $donor->first_name, '', '', '');
        }
        if ($donor->email != null) {
            $vcard->addEmail($donor->email);
        }
        if ($donor->phone != null) {
            $vcard->addPhoneNumber($donor->phone, $donor->company != null ? 'WORK' : 'HOME');
        }
        $vcard->addAddress(null, null, $donor->street, $donor->city, null, $donor->zip, $donor->country_name, ($donor->company != null ? 'WORK' : 'HOME') . ';POSTAL');

        // return vcard as a download
        return $vcard->download();
    }

    private static function splitTags($value): array
    {
        return collect(json_decode($value))
            ->pluck('value')
            ->unique()
            ->toArray();
    }

    private static function getTagSuggestions()
    {
        return Tag::has('donors')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();
    }
}
