<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => Device::inRandomOrder()->first(),
            'type' => '',
            'name' =>  fake()->randomElement(['BTC', 'USDT', 'ETH', 'XRP'])
        ];
    }
}
