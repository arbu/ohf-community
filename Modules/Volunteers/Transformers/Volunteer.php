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
        return array_merge(parent::toArray($request) + [
            'age' => $this->age,
            'stays' => StayResource::collection($this->stays->sortBy('arrival')),
        ]);
    }
}
