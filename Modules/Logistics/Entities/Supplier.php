<?php

namespace Modules\Logistics\Entities;

use Illuminate\Database\Eloquent\Model;
use Iatstuti\Database\Support\NullableFields;

class Supplier extends Model
{
    protected $table = 'logistics_suppliers';

    protected $fillable = [
        'name',
        'name_translit',
        'address',
        'address_translit',
        'category',
        'latlong',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'remarks',
    ];

    protected $nullable = [
        'name_translit',
        'address_translit',
        'latlong',
        'phone',
        'email',
        'website',
        'remarks',
    ];

    public function getNameTrAttribute() {
        return $this->name_translit != null ? $this->name_translit : $this->name;
    }

    public function getAddressTrAttribute() {
        return $this->address_translit != null ? $this->address_translit : $this->address;
    }

    public function getMapsLocationAttribute() {
        return "$this->name, $this->address";
    }

    
    public function getLatitudeAttribute() {
        if ($this->latlong != null) {
            $arr = preg_split('/,/', $this->latlong);
            if (count($arr) == 2) {
                return $arr[0];
            }
        }
        return null;
    }

    public function setLatitudeAttribute($value) {
        if ($this->latlong != null) {
            $arr = preg_split('/,/', $this->latlong);
            if (count($arr) == 2) {
                $this->latlong = $value . ',' . $arr[1];
            }
        } else {
            $this->latlong = $value . ',0.000000';
        }
    }

    public function getLongitudeAttribute() {
        if ($this->latlong != null) {
            $arr = preg_split('/,/', $this->latlong);
            if (count($arr) == 2) {
                return $arr[1];
            }
        }
        return null;
    }
    
    public function setLongitudeAttribute($value) {
        if ($this->latlong != null) {
            $arr = preg_split('/,/', $this->latlong);
            if (count($arr) == 2) {
                $this->latlong = $arr[0] . ',' . $value;
            }
        } else {
            $this->latlong = '0.000000,' . $value;
        }
    }

    public function products()
    {
        return $this->belongsToMany('Modules\Logistics\Entities\Product', 'logistics_product_supplier');
    }

}
