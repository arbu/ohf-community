<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MoneyTransaction extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $audit = $this->resource->audits()->first();
        return [
            'id' => $this->id,
            'receipt_no' => $this->receipt_no,
            'receipt_pictures' => $this->receipt_pictures != null
                ? collect($this->receipt_pictures)->map(fn ($p) => Storage::url($p))
                : null,
            'date' => $this->date,
            'type' => $this->type,
            'amount' => $this->amount,
            'category' => $this->category,
            'secondary_category' => $this->secondary_category,
            'project' => $this->project,
            'location' => $this->location,
            'cost_center' => $this->cost_center,
            'description' => $this->description,
            'beneficiary' => $this->beneficiary,
            'created_at' => $this->created_at,
            'audit_user_name' => isset($audit) ? $audit->getMetadata()['user_name'] : null,
            'can_update' => $request->user()->can('update', $this->resource),
        ];
    }
}
