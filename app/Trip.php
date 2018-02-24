<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Trip extends Model
{
    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }

    public function duration() {
        $arrival = new Carbon($this->arrival);
        $departure = new Carbon($this->departure);
        return $arrival->diffInDays($departure);
    }

    /*
    public function arrivesIn() {
        $arrival = new \Carbon\Carbon($this->arrival);
        return \Carbon\Carbon::now()->diffForHumans($arrival);
    }
    */
}
