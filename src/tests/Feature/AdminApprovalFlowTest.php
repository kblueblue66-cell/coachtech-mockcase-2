<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;

class AdminApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_pending_correction_requests()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user1 = User::factory()->create(['name' => 'ユーザーA','role' => 0]);
        $user2 = User::factory()->create(['name' => 'ユーザーB','role' => 0]);

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
        ]);

        AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance1->id,
            'user_id' => $user1->id,
            'status' => 1,
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
        ]);

        AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance2->id,
            'user_id' => $user2->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/stamp_correction_request/list?status=pending');

        $response->assertStatus(200);
        $response->assertSee('ユーザーA');
        $response->assertSee('ユーザーB');
    }

    public function test_admin_can_view_all_approved_correction_requests()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['name' => '承認済ユーザー', 'role' => 0]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
        $response->assertSee('承認済ユーザー');
    }

    public function test_admin_can_view_correct_application_details()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['name' => '申請テスト太郎' , 'role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
        ]);

        $request = AttendanceCorrectRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'revised_clock_in' => '08:30:00',
            'revised_clock_out' => '20:00:00',
            'remarks' => '電車遅延のため修正申請します',
            'status' => 1,
            ]);

        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('申請テスト太郎');
        $response->assertSee('2026');
        $response->assertSee('6月1日');
        $response->assertSee('08:30');
        $response->assertSee('20:00');
        $response->assertSee('電車遅延のため修正申請します');
    }

    public function test_admin_approval_process_updates_data_and_status()
    {
        $admin = User::factory()->create(['role' => 1]);
        $user = User::factory()->create(['role' => 0]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $request = AttendanceCorrectRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'revised_clock_in' => '10:30:00',
            'revised_clock_out' => '19:00:00',
            'remarks' => '遅延のため修正',
            'status' => 1,
            ]);

        $response = $this->actingAs($admin)->post("/stamp_correction_request/approve/{$request->id}");

        $this->assertDatabaseHas('attendance_correct_requests',[
            'id' => $request->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'clock_in' => '10:30:00',
            'clock_out' => '19:00:00',
        ]);
    }
}
