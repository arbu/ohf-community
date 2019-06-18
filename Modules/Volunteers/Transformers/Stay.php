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
        return array_merge(parent::toArray($request) + [
            'active' => $this->active,
        ]);
    }
}
