<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_list()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['name' => 'スタッフA','email' => 'staffA@example.com','role' => 0]);
        $user = User::factory()->create(['name' => 'スタッフB','email' => 'staffB@example.com','role' => 0]);

        $response = $this->actingAs($admin)->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSee('スタッフA');
        $response->assertSee('staffA@example.com');
        $response->assertSee('スタッフB');
        $response->assertSee('staffB@example.com');
    }

    public function test_admin_can_view_staff_monthly_attendance()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['name' => '対象スタッフ','role' => 0]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee('対象スタッフ');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_admin_staff_monthly_navigation()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['role' => 0]);

        $lastMonth = now()->subMonth();
        $nextMonth = now()->addMonth();

        $responsePrev = $this->actingAs($admin)->get("admin/attendance/staff/{$user->id}?month=" . $lastMonth->format('Y-m'));
        $responsePrev->assertSee($lastMonth->format('Y/m'));

        $responseNext = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}?month=" . $nextMonth->format('Y-m'));
        $responseNext->assertSee($nextMonth->format('Y/m'));
    }

    public function test_admin_staff_monthly_list_transitions_to_daily_detail()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");

        $response->assertSee('admin/attendance/' . $attendance->id);
    }
}
