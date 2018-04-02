<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Iatstuti\Database\Support\NullableFields;
use App\Util\ListEnumValues;

class VolunteerDocument extends Model
{
    use NullableFields;
    use ListEnumValues;

    protected $nullable = [
        'remarks',
    ];

    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }

    public static function types() {
        $types = self::getPossibleEnumValues('type');
        return collect($types)
            ->mapWithKeys(function($t){
                return [ $t => __('volunteering.' . $t)];
            })
            ->toArray();
    }
}
