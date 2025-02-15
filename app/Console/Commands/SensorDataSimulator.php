<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use App\Models\SensorData;
use Illuminate\Console\Command;

class SensorDataSimulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sensor-data-simulator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while (1) {
            $sensor = Sensor::inRandomOrder()->first();
            $data = random_int(1600, 1900);
            $sensorData = SensorData::create(
                [
                    'sensor_id' => $sensor->id,
                    'channel' => 'farm',
                    'source' =>  fake()->randomElement(['google', 'luno', 'yahoo']),
                    'value' => $data,
                ]
            );
            echo 'sensor: ' . $sensor->id . ' data: ' . $data . PHP_EOL;
            usleep(2000 * 1000); // 2 seconds
        }
    }
}
