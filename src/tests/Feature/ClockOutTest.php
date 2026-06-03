<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_out_functional()
    {
        $user = User::factory()->create(['role' => 0]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤');

        $this->actingAs($user)->post('/attendance/clock-out');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
        $response->assertSee('お疲れ様でした。');

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);

        $attendance = Attendance::where('user_id',$user->id)->first();
        $this->assertNotNull($attendance->clock_out);
    }

    public function test_clock_out_time_is_visible_in_list()
    {
        $user = User::factory()->create(['role' => 0]);
        $clockOutTime = '18:00';

        $this->actingAs($user)->post('/attendance/clock-in');

        Carbon::setTestNow(now()->toDateString() . '' . $clockOutTime);
        $this->actingAs($user)->post('/attendance/clock-out');

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee($clockOutTime);

        Carbon::setTestNow();
    }
}
