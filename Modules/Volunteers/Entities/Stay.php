<?php

namespace Modules\Volunteers\Entities;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Stay extends Model
{
    protected $table = 'volunteer_stays';

    protected $fillable = [
        'status',
        'arrival',
        'departure',
        'govt_reg_status',
        'financial_contribution',
        'financial_contribution_paid',
        'debriefing_info_received',
        'remarks',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'arrival',
        'departure',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'code_of_conduct_signed' => 'boolean',
        'financial_contribution_paid' => 'boolean',
        'debriefing_info_received' => 'boolean',
        'responsibilities' => 'array',
    ];    

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function getNumberOfDaysAttribute()
    {
        return optional($this->departure)->diffInDays($this->arrival);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'confirmed')
            ->whereDate('arrival', '<=', Carbon::today())->where(function($q){
                $q->where('departure', null)->orWhereDate('departure', '>=', Carbon::today());
            });
    }

    public function getActiveAttribute()
    {
        return $this->arrival <= Carbon::today() && ($this->departure == null || $this->departure >= Carbon::today());
    }

    public function scopeFuture($query)
    {
        return $query->where('status', 'confirmed')
            ->whereDate('arrival', '>', Carbon::today());
    }

    public function scopePrevious($query)
    {
        return $query->where('status', 'confirmed')
            ->whereDate('departure', '<', Carbon::today());
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }
}
