<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;
    Protected $table = 'vehicle_types';

    public function vehicleClass()
    {
    return $this->hasMany(VehicleClass::class,'vehicle_type_id','id');
    }
}
