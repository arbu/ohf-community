<?php

namespace Modules\Volunteers\Transformers;

use Modules\Volunteers\Transformers\Stay as StayResource;

use Illuminate\Http\Resources\Json\Resource;

class Volunteer extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['date_of_birth'] = optional($this->date_of_birth)->toDateString();
        $data['age'] = $this->age;
        if ($request->scope == 'active') {
            $data['stay'] = new StayResource($this->stays()->active()->first());
        } else if ($request->scope == 'applied') {
            $data['stay'] = new StayResource($this->stays()->applied()->orderBy('arrival')->first());
        } else if ($request->scope == 'future') {
            $data['stay'] = new StayResource($this->stays()->future()->orderBy('arrival')->first());
        } else if ($request->scope == 'previous') {
            $data['stay'] = new StayResource($this->stays()->previous()->orderBy('departure', 'desc')->first());
        }
        $data['stays'] = StayResource::collection($this->whenLoaded('stays'));
        return $data;
    }
}
