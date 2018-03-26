<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Iatstuti\Database\Support\NullableFields;
use Carbon\Carbon;

class Volunteer extends Model
{
    use NullableFields;

    /**
     * Get the user account to which the volunteer entity belongs
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	public function trips()
    {
        return $this->hasMany('App\Trip');
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAddressAttribute()
    {
        $str = $this->street;
        $str .= ', ';
        $str .= $this->zip;
        $str .= ' ';
        $str .= $this->city;
        if (!empty($this->country)) {
            $str .= ', ';
            $str .= $this->country;
        }
        return $str;
    }

    public function getAgeAttribute() {
        return isset($this->date_of_birth) ? (new Carbon($this->date_of_birth))->age : null;
    }

}
