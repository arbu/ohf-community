<?php

namespace App\Http\Resources\Volunteering;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locale = \App::getLocale();
        return [
            'id' => $this->id,
            'title' => $this->title[$locale],
            'description' => $this->description[$locale],
            'available_dates' => $this->available_dates[$locale],
            'minimum_stay' => $this->minimum_stay[$locale],
            'requirements' => $this->requirements[$locale],
        ];
    }
}
