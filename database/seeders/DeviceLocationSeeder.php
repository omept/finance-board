<?php

namespace Database\Seeders;

use App\Models\DeviceLocation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DeviceLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeviceLocation::factory(1)->create();
    }
}
