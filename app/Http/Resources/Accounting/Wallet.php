<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Resources\Json\JsonResource;

class Wallet extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            // 'amount' => $this->whenLoaded('transactions', fn () => $this->amount)
            'is_default' => $this->is_default,
            'num_transactions' => $this->whenLoaded('transactions', fn () => $this->transactions()->count()),
            'latest_activity' => $this->whenLoaded('transactions', fn () => $this->latestActivity),
            'is_restricted' => $this->whenLoaded('roles', fn () => $this->roles()->exists()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
