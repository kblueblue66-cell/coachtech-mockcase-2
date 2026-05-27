<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'     => '管理者ユーザー',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role'     => 1,
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'name'     => '一般ユーザー',
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
            'role'     => 0,
        ]);

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-05-01',
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
            'remarks'   => ''
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time'    => '12:00:00',
            'end_time'      => '13:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time'    => '15:00:00',
            'end_time'      => '15:15:00',
        ]);
    }
}
