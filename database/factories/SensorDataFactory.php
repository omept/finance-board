<?php

namespace Database\Factories;

use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SensorData>
 */
class SensorDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sensor_id' => Sensor::inRandomOrder()->first(),
            'value' => random_int(0, 100),
            'source' => fake()->randomElement(['google', 'luno', 'yahoo']),
            'channel' => 'farm',
            'recorded_at' => now()->subMinutes(random_int(1, 3600))
        ];
    }
}
