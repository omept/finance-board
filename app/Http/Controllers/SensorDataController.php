<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRangeRequest;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    /**
     * Fetch sensor data within a time range
     *
     * @param TimeRangeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function timeRangedData(Request  $request)
    {
        $timeStamp = $request->get('time_stamp');
        $sensorId = $request->get('sensor_id');
        $timeRanges = [
            '30_minutes' => now()->subMinutes(30),
            '1_hour' => now()->subHours(1),
            '6_hours' => now()->subHours(6),
            '24_hours' => now()->subDay(),
            '7_days' => now()->subDays(7),
            '14_days' => now()->subDays(14),
            '1_month' => now()->subMonth(),
            '6_month' => now()->subMonths(6),
            '1_year' => now()->subYear(),
        ];

        // Validate and retrieve the time range
        if (!isset($timeRanges[$timeStamp])) {
            return response()->json(['message' => 'Invalid time range'], 400);
        }

        // Define the cache key based on sensor ID and timestamp
        $cacheKey = "sensor_{$sensorId}_timeRanged_{$timeStamp}";

        // Check if data is already cached
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            Log::info("Data retrieved from cache for sensor {$sensorId} and timestamp {$timeStamp}. CacheKey {$cacheKey}");
            // Return cached data as a stream
            return response()->stream(function () use ($cachedData) {
                echo $cachedData;
                flush();
            }, 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        // If data is not cached, fetch from database and cache it
        $sensor = Sensor::find($sensorId);

        // Determine the start time based on the timestamp
        $startTime = $timeRanges[$timeStamp];

        // Fetch data from the database
        $streamedData = "";
        $query = $sensor->data()
            ->where('created_at', '>=', $startTime)
            ->orderBy('created_at');

        // Stream the data and cache it simultaneously
        return response()->stream(function () use ($query, $cacheKey, &$streamedData) {
            $isFirstChunk = true;
            $streamedData = '['; // Start the JSON array

            $query->chunk(100, function ($rows) use (&$isFirstChunk, &$streamedData) {
                $valueChunk = [];

                foreach ($rows as $row) {
                    $valueChunk[] = $row->value;
                }

                // Add a comma before the chunk if it's not the first chunk
                if (!$isFirstChunk) {
                    $streamedData .= ',';
                }
                $streamedData .= json_encode($valueChunk);
                $isFirstChunk = false;

                // Stream the chunk
                echo json_encode($valueChunk);
                flush();
            });

            // Close the JSON array
            $streamedData .= ']';

            // Cache the full streamed data for later use
            if($streamedData != "[]"){
                Cache::put($cacheKey, $streamedData, now()->addMinutes(10));
            }

            // Finish the response with the closing bracket
            echo ']';
        }, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
