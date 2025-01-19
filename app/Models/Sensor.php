<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sensor extends Model
{
    use HasFactory;
    //

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function data()
    {
        return $this->hasMany(SensorData::class);
    }

    
}
