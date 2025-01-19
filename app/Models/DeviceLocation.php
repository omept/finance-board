<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLocation extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceLocationFactory> */
    use HasFactory;


    public function device(){
        return $this->belongsTo(Device::class);
    }
}
