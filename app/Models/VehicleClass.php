<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleClass extends Model
{
    use HasFactory;
    Protected $table = 'vehicle_classes';

    public function vehicleType()
    {
    return $this->belongsTo(VehicleType::class);
    }
}
