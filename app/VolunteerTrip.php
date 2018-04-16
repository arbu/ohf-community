<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Iatstuti\Database\Support\NullableFields;

class VolunteerTrip extends Model
{
    use NullableFields;

    protected $nullable = [
        'remarks',
        'departure',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'arrival',
        'departure',
    ];

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
        return $this->arrival->diffInDays($this->departure);
    }

    public function getArrivesInAttribute() {
        return $this->hasArrived ? null : Carbon::today()->diffInDays($this->arrival);
    }

    public function getHasArrivedAttribute() {
        return Carbon::today()->gte($this->arrival);
    }

    public function getDepartsInAttribute() {
        if ($this->departure == null) {
            return null;
        }
        return $this->hasDeparted ? null : Carbon::today()->diffInDays($this->departure);
    }

    public function getHasDepartedAttribute() {
        if ($this->departure == null) {
            return false;
        }
        return Carbon::today()->gte($this->departure);
    }

    public function getStatusAttribute() {
        if ($this->attributes['status'] == 'approved') {
            if ($this->hasArrived && !$this->hasDeparted) {
                return 'ongoing';
            } elseif ($this->hasDeparted) {
                return 'completed';
            }
        }
        return $this->attributes['status'];
    }


}
