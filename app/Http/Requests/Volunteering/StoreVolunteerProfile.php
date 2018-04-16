<?php

namespace App\Http\Requests\Volunteering;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Util\CountriesExtended;

class StoreVolunteerProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'street' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => [
                'required',
                'max:255',
                Rule::in(CountriesExtended::getList('en')), // TODO localize
            ],
            'nationality' => 'required|string|max:255',
            'passport_no' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:255',
            'skype' => 'nullable|max:255',
        ];
    }
}
