<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_attendance_list_shows_all_users_records()
    {
        $admin = User::factory()->create(['role' => 1]);

        $user1 = User::factory()->create(['name' => 'ユーザー1','role' => 0]);
        $user2 = User::factory()->create(['name' => 'ユーザー2','role' => 0]);
        $today = now()->toDateString();

        Attendance::create([
            'user_id' => $user1->id,
            'date' => $today,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'date' => $today,
            'clock_in' =>'10:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('ユーザー2');
        $response->assertSee('10:00');
    }

    public function test_admin_attendance_list_shows_current_date()
    {
        $admin = User::factory()->create(['role' => 1]);
        $todayDisplay = now()->format('Y/m/d');

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee($todayDisplay);
    }

    public function test_admin_attendance_list_previous_day()
    {
        $admin = User::factory()->create(['role' => 1]);
        $yesterday = now()->subDay();

        $user = User::factory()->create(['role' => 0]);
        Attendance::create([
            'user_id' => $user->id,
            'date'=> $yesterday->toDateString(),
            'clock_in' => '08:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $yesterday->format('Y-m-d'));

        $response->assertSee($yesterday->format('Y/m/d'));
        $response->assertSee('08:00');
    }

    public function test_admin_attendance_list_next_day_navigation()
    {
        $admin = User::factory()->create(['role' => 1]);
        $tomorrow = now()->addDay();

        $user = User::factory()->create(['role' => 0]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => $tomorrow->toDateString(),
            'clock_in' => '11:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $tomorrow->format('Y-m-d'));

        $response->assertSee($tomorrow->format('Y/m/d'));
        $response->assertSee('11:00');
    }
}
