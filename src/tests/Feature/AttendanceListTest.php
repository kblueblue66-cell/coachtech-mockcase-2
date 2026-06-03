<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_list_shows_current_month()
    {
        $user = User::factory()->create(['role' => 0]);

        $currentMonth = now()->format('Y/m');

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee($currentMonth);
    }

    public function test_attendance_list_shows_own_records()
    {
        $user = User::factory()->create(['role' => 0]);
        $otherUser = User::factory()->create(['role' => 0]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        Attendance::create([
            'user_id' => $otherUser->id,
            'date' => now()->toDateString(),
            'clock_in' => '10:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('09:00');
        $response->assertDontSee('10:00');
    }

    public function test_attendance_list_previous_month_navigation()
    {
        $user = User::factory()->create(['role' => 0]);
        $lastMonth = now()->subMonth();

        Attendance::create([
            'user_id' => $user->id,
            'date' => $lastMonth->toDateString(),
            'clock_in' => '08:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=' . $lastMonth->format('Y-m'));

        $response->assertSee($lastMonth->format('Y/m'));
        $response->assertSee('08:00');
    }

    public function test_attendance_list_next_month_navigation()
    {
        $user = User::factory()->create(['role' => 0]);
        $nextMonth = now()->addMonth();

        Attendance::create([
            'user_id' => $user->id,
            'date' => $nextMonth->toDateString(),
            'clock_in' => '10:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=' . $nextMonth->format('Y-m'));

        $response->assertSee($nextMonth->format('Y/m'));
        $response->assertSee('10:00');
    }

    public function test_attendance_list_transitions_to_detail()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('詳細');
        $response->assertSee('/attendance/detail/' . $attendance->id);
    }
}
