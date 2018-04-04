<?php

namespace App\Http\Requests\Volunteering;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerJobCategory extends FormRequest
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
            'title.*' => 'required',
            'order' => 'required|numeric|min:0',
        ];
    }
}
