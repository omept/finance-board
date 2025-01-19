<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceLocation>
 */
class DeviceLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lat' => random_int(4,14) + random_int(1,100)/13,
            'long' => random_int(3, 15) + random_int(1, 100) / 13,
            'device_id' => Device::inRandomOrder()->first(),
        ];
    }
}
