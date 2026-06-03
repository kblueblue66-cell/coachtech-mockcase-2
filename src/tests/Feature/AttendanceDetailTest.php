<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrectRequest;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_update_validation_clock_in_after_out()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'remarks' => 'テスト備考',
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_attendance_update_validation_rest_start_after_out()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'rest_start' => ['19:00'],
            'rest_end' => ['20:00'],
            'remarks' => 'テスト備考',
        ]);

        $response->assertSessionHasErrors(['rest_start.0' => '休憩時間が不適切な値です']);
    }

    public function test_attendance_update_validation_rest_end_after_out()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'rest_start' => ['12:00'],
            'rest_end' => ['19:00'],
            'remarks' => 'テスト備考',
        ]);

        $response->assertSessionHasErrors(['rest_end.0' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test_attendance_update_validation_remarks_empty()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors(['remarks' => '備考を記入してください']);
    }

    public function test_attendance_correction_request_flow()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' =>'09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'date' => $attendance->date,
            'clock_in' => '08:30',
            'clock_out' => '17:30',
            'rest_start' => ['12:00'],
            'rest_end' => ['13:00'],
            'remarks' => '早出の修正',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_correct_requests',[
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => 1,
            'remarks' => '早出の修正',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');
        $response->assertSee('承認待ち');
        $response->assertSee('早出の修正');
    }

    public function test_attendance_correction_request_all_pending_visible()
    {
        $user = User::factory()->create(['role' => 0]);

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00'
            ]);
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-02',
            'clock_in' => '09:00:00',
        ]);

        AttendanceCorrectRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance1->id,
            'date' => '2026-06-01',
            'revised_clock_in' => '08:00:00',
            'revised_clock_out' => '17:00:00',
            'remarks' => '申請その1',
            'status' => 1,
        ]);

        AttendanceCorrectRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance2->id,
            'date' => '2026-06-02',
            'revised_clock_in' => '08:30:00',
            'revised_clock_out' => '17:30:00',
            'remarks' => '申請その2',
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertSee('申請その1');
        $response->assertSee('申請その2');
    }

    public function test_attendance_correction_request_approved_list()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            ]);

        AttendanceCorrectRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => now()->toDateString(),
            'revised_clock_in' => '09:00:00',
            'revised_clock_out' => '18:00:00',
            'remarks' => '過去の承認済み申請',
            'status' => 2,
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?status=approved');
        $response->assertSee('承認済み');
        $response->assertSee('過去の承認済み申請');
    }

    public function test_correction_request_transition_to_detail()
    {
        $user = User::factory()->create(['role' => 0]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        \App\Models\AttendanceCorrectRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => $attendance->date,
            'revised_clock_in' => '08:30:00',
            'revised_clock_out' => '17:30:00',
            'remarks' => 'テスト申請',
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertSee('/attendance/detail/' . $attendance->id);
    }
}

