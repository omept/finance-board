<?php

namespace App\Events;

use App\Models\SensorData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensorData;
    /**
     * Create a new event instance.
     */

    public function __construct(SensorData $sensorData)
    {
        $sd = $sensorData->toArray();
        $sd['sensor_name'] = $sensorData->sensor && $sensorData->sensor->name ?  $sensorData->sensor->name : 'Unknown';
        $this->sensorData = $sd;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('sensor-data'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'sensorData' => $this->sensorData,
        ];
    }
}
