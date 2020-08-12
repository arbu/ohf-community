<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Wallet;
use Illuminate\Foundation\Http\FormRequest;

class TransactionsImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->wallet == null
            || $this->user()->can('view', Wallet::find($this->wallet));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'wallet' => 'nullable|int',
            'file' => 'required|file',
            'map' => 'nullable|array',
            'map.*.from' => 'required_with:map.*.to|required_with:map.*.append',
            'map.*.to' => 'required_with:map.*.append',
        ];
    }
}
