<?php

namespace Modules\Volunteers\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VolunteerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if (in_array($request->scope, ['applied', 'future'])) {
            return collect($data)->sortBy(function($e) {
                return $e['stay']['arrival'];
            });
        }
        if ($request->scope == 'previous') {
            return collect($data)->sortByDesc(function($e) {
                return $e['stay']['departure'];
            });            
        }
        return $data;
    }
}
