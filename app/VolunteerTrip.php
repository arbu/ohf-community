<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VolunteerTrip extends Model
{
    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }

    public function job()
    {
        return $this->belongsTo('App\VolunteerJob', 'job_id');
    }

    public function getDurationAttribute() {
        if ($this->departure == null) {
            return null;
        }
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
