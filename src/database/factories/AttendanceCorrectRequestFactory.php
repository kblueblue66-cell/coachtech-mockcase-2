<?php

namespace Database\Factories;

use App\Models\AttendanceCorrectRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectRequestFactory extends Factory
{
    protected $model = AttendanceCorrectRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'date' => $this->faker->date(),
            'revised_clock_in' => '09:00:00',
            'revised_clock_out' => '18:00:00',
            'remarks' => 'テスト用備考',
            'status' => 1,
        ];
    }
}