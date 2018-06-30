<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StorageTransaction extends Model
{
    public static function boot()
    {
        static::creating(function ($model) {
            $model->user_id = Auth::id();
            $model->user_name = optional(Auth::user())->name;
        });

        parent::boot();
    }

    public function container() {
        return $this->belongsTo('App\StorageContainer', 'container_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
