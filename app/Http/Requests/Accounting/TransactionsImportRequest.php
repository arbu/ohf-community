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

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator The validator instance
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (isset($this->map)) {
                $mapped_fields = collect($this->map)->pluck('to')->all();

                if (! in_array('app.date', $mapped_fields)) {
                    $validator->errors()->add('file', __('accounting.missing_column', [ 'column' => __('app.date') ]));
                }
                if (! in_array('app.description', $mapped_fields)) {
                    $validator->errors()->add('file', __('accounting.missing_column', [ 'column' => __('app.description') ]));
                }
                if (! (in_array('accounting.income', $mapped_fields)
                    || in_array('accounting.spending', $mapped_fields)
                    || (in_array('app.amount', $mapped_fields) && in_array('app.type', $mapped_fields)))) {
                    $validator->errors()->add('file', __('accounting.missing_amount_column'));
                }
            }
        });
    }
}
