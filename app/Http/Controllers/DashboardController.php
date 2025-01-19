<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $sensors = Sensor::with(['data' => function ($query) {
            $query->latest()->take(10);
        }])->get();

        // Map the response to include sensor_name
        $mappedData = $sensors->map(function ($sensor) {
            return  [
                'label' => $sensor->name,
                'id' => $sensor->id,
                'data' => $sensor->data->pluck('value')->toArray()
            ];
        });

        return Inertia::render('Dashboard/Dashboard', ['sensorData' => $mappedData]);
    }
}
