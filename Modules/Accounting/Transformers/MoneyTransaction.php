<?php

namespace Modules\Accounting\Transformers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\Resource;

class MoneyTransaction extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $audit = $this->audits()->first();
        $user = isset($audit) ? $audit->getMetadata()['user_name'] : null;
        return [
            'id' => $this->id,
            'receipt_no' => $this->receipt_no,
            'date' => $this->date,
            'amount' => number_format($this->amount, 2),
            'type' => $this->type,
            'income' => $this->type == 'income' ? number_format($this->amount, 2) : null,
            'spending' => $this->type == 'spending' ? number_format($this->amount, 2) : null,
            'category' => $this->category,
            'project' => $this->project,
            'description' => $this->description,
            'beneficiary' => $this->beneficiary,
            'registered' => $this->created_at . ($user != null ? " ($user)" : ''),
            'has_receipt_pictures' => !empty($this->receipt_pictures),
            'detail_url' => Auth::user()->can('view', $this->resource) ? route('accounting.transactions.show', $this) : null,
        ];
    }
}
