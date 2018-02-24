<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
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

    public function fullAddress()
    {
        $str = $this->address;
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
}
