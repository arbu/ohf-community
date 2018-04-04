<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VolunteerJob extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'available_dates' => 'array',
        'minimum_stay' => 'array',
        'requirements' => 'array',
    ];
}
