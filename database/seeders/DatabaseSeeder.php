<?php

namespace Database\Seeders;

use App\Models\DeviceLocation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\DeviceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'GTP',
            'email' => 'georgetheprogrammer@gmail.com',
        ]);

        (new DeviceSeeder)->run();
        (new SensorSeeder)->run();
        (new SensorDataSeeder)->run();
        (new DeviceLocationSeeder)->run();
    }
}
