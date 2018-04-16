<?php

namespace App\Http\Requests\Volunteering;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreVolunteerTrip extends FormRequest
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
            'volunteer' => [
                'required',
                'exists:volunteers,id',
            ],
            'job' => [
                'required',
                'exists:volunteer_jobs,id',
            ],
            'arrival' => [
                'required',
                'date',
            ],
            'departure' => [
                'nullable',
                'date',
            ],
            'remarks' => [
                'nullable',
                'string'
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!isset($this->departure)) {
                return;
            }
            $arrival = new Carbon($this->arrival);
            $departure = new Carbon($this->departure);
            if ($arrival->gte($departure)) {
                $validator->errors()->add('arrival', '"arrival" date must be before "departure" date');
                $validator->errors()->add('departure', '"departure" date must be after "arrival" date');
            }
        });
    }
}
