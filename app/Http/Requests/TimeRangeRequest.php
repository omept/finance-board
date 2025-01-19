<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeRangeRequest extends FormRequest
{
    // In a request class or controller
    private const TIME_RANGES = [
        '30_minutes',
        '1_hour',
        '6_hours',
        '24_hours',
        '7_days',
        '14_days',
        '1_month',
        '6_month',
        '1_year',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sensor_id' => ['required', 'exists:sensors,id'],
            'time_stamp' => ['required', 'string', 'in:' . implode(',', self::TIME_RANGES)],
        ];
    }
}
