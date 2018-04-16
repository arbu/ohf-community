<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerTripResource extends JsonResource
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
            'title' => $this->volunteer->name,
            //'description' => $this->description,
            'start' => $this->arrival->toIso8601String(),
            'end' => optional($this->departure)->toIso8601String(),
            'allDay' => true,
            'url' => route('volunteering.trips.show', $this),
        ];
    }
}
