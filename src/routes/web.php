<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\StampCorrectionRequestController;
//管理者用コントローラー
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\StampCorrectionController as AdminStampCorrectionController;

//会員登録(認証不要)
Route::get('/register',[RegisterUserController::class,'create'])->name('register');
Route::post('/register',[RegisterUserController::class,'store']);

//一般ユーザー用用ログイン
Route::get('login',[AuthenticatedSessionController::class,'create'])->name('login');
Route::post('login',[AuthenticatedSessionController::class,'store']);

//一般ユーザー（要認証）
Route::middleware(['auth'])->group(function(){
    //勤怠登録画面
    Route::get('/attendance',[AttendanceController::class,'index'])->name('attendance.index');
    //出勤・退勤機能
    Route::post('/attendance/clock-in',[AttendanceController::class,'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out',[AttendanceController::class,'clockOut'])->name('attendance.clock-out');
    //休憩機能
    Route::post('/attendance/rest-start',[AttendanceController::class,'restStart'])->name('attendance.rest-start');
    Route::post('/attendance/rest-end',[AttendanceController::class,'restEnd'])->name('attendance.rest-end');

    //勤怠一覧
    Route::get('/attendance/list',[AttendanceListController::class,'index']);

    //勤怠詳細
    Route::get('/attendance/detail/{id}',[AttendanceDetailController::class,'show']);
    Route::post('/attendance/detail/{id}',[AttendanceDetailController::class,'update']);

    //申請一覧
    Route::get('/stamp_correction_request/list',[StampCorrectionRequestController::class,'index']);

    //管理者用(要認証）
Route::middleware(['can:admin'])->group(function(){
    //勤怠一覧
    Route::get('/admin/attendance/list',[AdminAttendanceController::class,'index']);

    //勤怠詳細
    Route::get('/admin/attendance/{id}',[AdminAttendanceController::class,'show']);
    Route::post('/admin/attendance/{id}',[AdminAttendanceController::class,'update']);

    //スタッフ一覧
    Route::get('/admin/staff/list',[AdminStaffController::class,'index']);

    //スタッフ別勤怠一覧
    Route::get('/admin/attendance/staff/{id}',[AdminStaffController::class,'show']);
    Route::get('/admin/attendance/staff/{id}/export',[AdminStaffController::class,'export']);

    //申請一覧
    Route::get('/admin/stamp_correction_request/list',[AdminStampCorrectionController::class,'index']);

    //修正申請承認
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminStampCorrectionController::class,'show']);
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminStampCorrectionController::class,'approve']);
    });
});