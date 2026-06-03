<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_in_functional()
    {
        $user = User::factory()->create(['role' => 0]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤');

        $response = $this->actingAs($user)->post('/attendance/clock-in');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_cannot_clock_in_twice()
    {
        $user = User::factory()->create(['role' => 0]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
    }

    public function test_clock_in_time_is_visible_in_list()
    {
        $user = User::factory()->create(['role' => 0]);
        $clockInTime = '09:00';

        Carbon::setTestNow(now()->toDateString() . '' . $clockInTime);
        $this->actingAs($user)->post('/attendance/clock-in');

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee($clockInTime);

        Carbon::setTestNow();
    }
}
