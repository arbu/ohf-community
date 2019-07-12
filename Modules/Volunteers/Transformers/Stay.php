<?php

namespace Modules\Volunteers\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class Stay extends Resource
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
        unset($data['volunteer_id']);
        $data['active'] = $this->active;
        $data['arrival'] = $this->arrival->toDateString();
        $data['departure'] = optional($this->departure)->toDateString();
        $data['num_days'] = $this->numberOfDays;
        return $data;
    }
}
