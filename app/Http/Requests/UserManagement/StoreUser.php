<?php

namespace App\Http\Requests\UserManagement;

use App\Role;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:191',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'pwned',
            ],
            'roles' => [
                'array',
                Rule::in(Role::select('id')->get()->pluck('id')),
            ],
        ];
    }
}