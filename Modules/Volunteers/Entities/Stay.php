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
        'feedback_sheet_received',
        'fundraising_infos_received',
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
        'financial_contribution_paid' => 'boolean',
        'feedback_sheet_received' => 'boolean',
        'fundraising_infos_received' => 'boolean',
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

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }
}
