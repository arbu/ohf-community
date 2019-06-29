<?php

namespace Modules\Volunteers\Entities;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Volunteer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'gender',
        'email',
        'phone',
        'whatsapp',
        'skype',
        'passport_id_number',
        'govt_reg_number',
        'govt_reg_expiry',
        'languages',
        'criminal_record_received',
        'remarks',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'languages' => 'array',
        'criminal_record_received' => 'boolean',
        'has_driving_license' => 'boolean',
    ];    

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_of_birth',
        'govt_reg_expiry',
    ];

    public function getAgeAttribute() {
        try {
            return isset($this->date_of_birth) ? (new Carbon($this->date_of_birth))->age : null;
        } catch (\Exception $e) {
            Log::error('Error calculating age of ' . $this->first_name . ' ' . $this->last_name . ' ('. $this->date_of_birth . '): ' . $e->getMessage());
            return null;
        }
    }

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('stays', function($q) {
            $q->active();
        });
    }

    public function scopeFuture($query)
    {
        return $query->whereHas('stays', function($q) {
            $q->future();
        });
    }

    public function scopePrevious($query)
    {
        return $query->whereHas('stays', function($q) {
            $q->previous();
        });
    }

    public function scopeApplied($query)
    {
        return $query->whereHas('stays', function($q) {
            $q->applied();
        });
    }
}
