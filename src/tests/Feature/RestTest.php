<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class RestTest extends TestCase
{
    use RefreshDatabase;

    public function test_rest_start_functional()
    {
        $user = User::factory()->create(['role' => 0]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');

        $this->actingAs($user)->post('attendance/rest-start');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩中');

        $this->assertDatabaseHas('rests',[
            'attendance_id' => $attendance->id,
            'end_time' => null,
        ]);
    }

    public function test_rest_end_functional()
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
        $response->assertSee('休憩戻');

        $this->actingAs($user)->post('/attendance/rest-end');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_multi_rests_possible()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user)->post('/attendance/rest-start');
        $this->actingAs($user)->post('/attendance/rest-end');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
    }

    public function test_multi_rests_returns_possible()
    {
        $user = User::factory()->create(['role' => 0]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user)->post('/attendance/rest-start');
        $this->actingAs($user)->post('/attendance/rest-end');

        $this->actingAs($user)->post('/attendance/rest-start');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩戻');
    }

    public function test_rest_time_is_visible_in_list()
    {
        $user = User::factory()->create(['role' => 0]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $attendance = Attendance::where('user_id',$user->id)->first();
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('1:00');
    }
}

