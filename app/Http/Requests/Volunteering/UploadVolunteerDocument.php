<?php

namespace App\Http\Requests\Volunteering;

use Illuminate\Foundation\Http\FormRequest;
use App\VolunteerDocument;

class UploadVolunteerDocument extends FormRequest
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
            'file' => 'required|file|mimes:jpeg,bmp,png,pdf|max:' . (16 * 1024),
            'type' => 'required|in:' . implode(',', array_keys(VolunteerDocument::types())),
            'remarkts' => 'nullable|string',
        ];
    }

}
