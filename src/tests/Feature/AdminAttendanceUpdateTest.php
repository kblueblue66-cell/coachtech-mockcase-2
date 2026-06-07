<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_attendance_detail_shows_correct_data()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['name' => 'テスト太郎','role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get("admin/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('2026');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_admin_attendance_update_validation_clock_in_after_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $attendance = Attendance::create([
            'user_id' => User::factory()->create(['role' => 0])->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->post("admin/attendance/{$attendance->id}",[
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'rest_start' => ['12:00'],
            'rest_end' => ['13:00'],
            'remarks' => '修正テスト',
        ]);

        $response->assertSessionHasErrors(['clock_out' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_admin_attendance_update_validation_rest_start_after_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $attendance = Attendance::create([
            'user_id' => User::factory()->create(['role' => 0])->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            ]);

        $response = $this->actingAs($admin)->post("admin/attendance/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'rests' => [
                ['start' => '19:00','end' => '20:00']
                ],
            'remarks' => '修正テスト',
        ]);

        $response->assertSessionHasErrors(['rests.0.start' => '休憩時間が不適切な値です']);
    }

    public function test_admin_attendance_update_validation_rest_end_after_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $attendance = Attendance::create([
            'user_id' => User::factory()->create(['role' => 0])->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            ]);

        $response = $this->actingAs($admin)->post("admin/attendance/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'rests' =>[
                ['start' => '12:00','end' => '19:00']
            ],
            'remarks' => '修正テスト',
        ]);

        $response->assertSessionHasErrors(['rests.0.end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test_admin_attendance_update_validation_remarks_empty()
    {
        $admin = User::factory()->create(['role' => 1]);
        $attendance = Attendance::create([
            'user_id' => User::factory()->create(['role' => 0])->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $response = $this->actingAs($admin)->post("admin/attendance/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors(['remarks' => '備考を記入してください']);
    }
}
