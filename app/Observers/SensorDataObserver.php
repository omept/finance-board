<?php

namespace App\Observers;

use App\Events\SensorDataAdded;
use App\Models\SensorData;
use Illuminate\Support\Facades\Log;

class SensorDataObserver
{
    /**
     * Handle the SensorData "created" event.
     *
     * @param  SensorData  $sensorData
     * @return void
     */
    public function created(SensorData $sensorData) {
        SensorDataAdded::dispatch($sensorData);
    }
}
