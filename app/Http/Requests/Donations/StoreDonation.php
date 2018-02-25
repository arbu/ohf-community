<?php

namespace App\Http\Requests\Donations;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonation extends FormRequest
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
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
            'origin' => 'required|string',
        ];
    }
}