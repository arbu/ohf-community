<?php

namespace Modules\Bank\Http\Controllers;

use Modules\People\Entities\Person;

use App\Http\Controllers\Controller;

use Modules\People\Http\Requests\StorePerson;

use Countries;

class PeopleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Person::class);
    }

    public function create()
    {
        $countries = Countries::getList('en');
        return view('bank::people.create', [
            'countries' => $countries,
        ]);
    }

    public function store(StorePerson $request)
    {
        $person = new Person();
        $person->fill($request->all());
        $person->card_no = $request->card_no;
		$person->save();

        $request->session()->put('filter', $person->search);

        return redirect()->route('bank.withdrawal')
            ->with('success', __('people::people.person_added'));
    }

    public function show(Person $person)
    {
        return view('bank::people.show', [
            'person' => $person,
        ]);
    }

    public function edit(Person $person)
    {
        return view('bank::people.edit', [
            'person' => $person,
            'countries' => Countries::getList('en')
		]);
	}

    public function update(StorePerson $request, Person $person)
    {
        $person->fill($request->all());
        $person->save();

        return redirect()->route('bank.people.show', $person)
                ->with('success', __('people::people.person_updated'));
    }

    public function destroy(Person $person)
    {
        $person->delete();

        return redirect()->route('bank.withdrawal')
            ->with('success', __('people::people.person_deleted'));
    }
}
