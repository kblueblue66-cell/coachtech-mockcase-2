<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\StampCorrectionRequestController;
//管理者用コントローラー
use App\Http\Controllers\Admin\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\StampCorrectionController as AdminStampCorrectionController;

//会員登録(認証不要)
Route::get('/register',[RegisterUserController::class,'create'])->name('register');
Route::post('/register',[RegisterUserController::class,'store']);

//一般ユーザー用用ログイン
Route::get('/login',[AuthenticatedSessionController::class,'create'])->name('login');
Route::post('/login',[AuthenticatedSessionController::class,'store']);

//管理者用ログイン
Route::prefix('admin')->group(function(){
    Route::get('/login',[AdminAuthenticatedSessionController::class,'create'])->name('admin.login');
    Route::post('/login',[AdminAuthenticatedSessionController::class,'store']);
});

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
    Route::get('/attendance/list',[AttendanceListController::class,'index'])->name('attendance.list');

    //勤怠詳細・修正
    Route::get('/attendance/detail/{id}',[AttendanceDetailController::class,'show'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}',[AttendanceDetailController::class,'update'])->name('attendance.update');

    //申請一覧(一般/管理者 共通パス）
    Route::get('/stamp_correction_request/list',function(Illuminate\Http\Request $request){
        return auth()->user()->role === 1
        ? app(\App\Http\Controllers\Admin\StampCorrectionController::class)->index($request) : app(\App\Http\Controllers\StampCorrectionRequestController::class)->index($request);
        })->name('stamp_correction_request.list');

    //ログアウト
    Route::post('/logout',[AuthenticatedSessionController::class,'destroy'])->name('logout');

    //管理者用(要認証）
Route::middleware(['can:admin'])->group(function(){
    //勤怠一覧
    Route::get('/admin/attendance/list',[AdminAttendanceController::class,'index'])->name('admin.attendance.list');

    //勤怠詳細・修正
    Route::get('/admin/attendance/{id}',[AdminAttendanceController::class,'show'])->name('admin.attendance.show');
    Route::post('/admin/attendance/{id}',[AdminAttendanceController::class,'update'])->name('admin.attendance.update');

    //スタッフ一覧
    Route::get('/admin/staff/list',[AdminStaffController::class,'index'])->name('admin.staff.list');

    //スタッフ別勤怠一覧
    Route::get('/admin/attendance/staff/{id}',[AdminStaffController::class,'show'])->name('admin.staff.attendance.show');
    Route::get('/admin/attendance/staff/{id}/export',[AdminStaffController::class,'export'])->name('admin.staff.attendance.export');

    //修正申請承認
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminStampCorrectionController::class,'show'])->name('admin.stamp_correction_request.show');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminStampCorrectionController::class,'approve'])->name('admin.stamp_correction_request.approve');

    //管理者ログアウト
    Route::post('/admin/logout',[AdminAuthenticatedSessionController::class,'destroy'])->name('admin.logout');
    });
});