<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;
    // 日時取得機能
    public function test_current_date_display()
    {
        $user = User::factory()->create(['role' => 0]);

        $response = $this->actingAs($user)->get('/attendance');

        $today = now()->locale('ja')->isoFormat('YYYY年M月D日(ddd)');
        $response->assertSee($today);
    }

    public function test_status_display_outside_work()
    {
        $user = User::factory()->create(['role' => 0]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_status_display_at_work()
    {
        $user = User::factory()->create(['role' => 0]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_status_display_on_break()
    {
        $user = User::factory()->create(['role' => 0]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_status_display_clocked_out()
    {
        $user = User::factory()->create(['role' => 0]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
    }
}
