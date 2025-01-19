<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    
    public function locations()
    {
        return $this->hasMany(DeviceLocation::class);
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}
