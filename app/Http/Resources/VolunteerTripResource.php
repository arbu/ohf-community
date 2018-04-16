<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
        if ($request->end != null && $this->departure == null) {
            $this->departure = new Carbon($request->end, $request->timezone);
        }

        return [
            'id' => $this->id,
            'title' => $this->volunteer->name,
            'start' => $this->arrival->toDateString(),
            'end' => optional(optional($this->departure)->addDay())->toDateString(),
            'allDay' => true,
            'url' => route('volunteering.trips.show', $this),
            'resourceId' => optional($this->job)->id
            //'color' => random_color(),
        ];
    }
}
