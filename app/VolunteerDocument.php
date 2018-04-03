<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Iatstuti\Database\Support\NullableFields;
use App\Util\ListEnumValues;
use Illuminate\Support\Facades\Storage;

class VolunteerDocument extends Model
{
    use NullableFields;
    use ListEnumValues;

    protected $nullable = [
        'remarks',
    ];

    public static function boot () {
        parent::boot();
        
        self::deleting(function ($document) {
            Storage::delete($document->file);
        });
    }

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
