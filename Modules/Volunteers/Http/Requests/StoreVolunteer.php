<?php

namespace Modules\Volunteers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVolunteer extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
            ],
            'nationality' => [
                'nullable',
            ],
            'gender' => [
                'nullable',
                Rule::in(['m', 'f']),
            ],
            'email' => [
                'nullable',
                'email',
            ],
            'phone' => [
                'nullable',
            ],
            'phone' => [
                'nullable',
            ],
            'skype' => [
                'nullable',
            ],
            'passport_id_number' => [
                'nullable',
            ],
            'govt_reg_number' => [
                'nullable',
            ],
            'govt_reg_expiry' => [
                'nullable',
                'date',
            ],
            'languages' => [
                'array',
                'date',
            ],
            'criminal_record_received' => [
                'boolean',
            ],
            'remarks' => [
                'nullable',
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
