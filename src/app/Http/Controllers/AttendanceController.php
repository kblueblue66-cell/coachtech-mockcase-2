<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $date = $now->isoFormat('YYYY年M月D日(ddd)');
        $time = $now->format('H:i');

        $attendance = Attendance::where('user_id',$user->id)
            ->where('date',$now->toDateString())
            ->first();

        $status = '勤務外';

        if($attendance){
            if($attendance->clock_out){
                $status = '退勤済';
            }else{
                $latestRest = Rest::where('attendance_id',$attendance->id)
                    ->whereNull('end_time')
                    ->exists();

                $status = $latestRest ? '休憩中' : '出勤中';
            }
        }

        return view('attendance.index',compact('date','time','status'));
    }
    //出勤機能
    public function clockIn()
    {
        $user = Auth::user();
        $now = Carbon::now();

    //すでにレコードが存在していないかチェック
        $exists = Attendance::where('user_id',$user->id)
            ->where('date',$now->toDateString())
            ->exists();

        Attendance::create([
            'user_id'  => $user->id,
            'date'     => $now->toDateString(),
            'clock_in' => $now->toTimeString(),
        ]);

        return redirect()->back();
    }
    //休憩入機能
    public function restStart()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id',$user->id)
            ->where('date',$now->toDateString())
            ->first();
    //万が一、出勤データがない状態の回避
        if(!$attendance){
            return redirect()->back()->with('error','出勤データが見つかりません。');
        }

        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time'    => $now->toTimeString(),
        ]);

        return redirect()->back();
    }
    //休憩戻機能
    public function restEnd()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id',$user->id)
            ->where('date',$now->toDateString())
            ->first();

        $rest = Rest::where('attendance_id',$attendance->id)
            ->whereNull('end_time')
            ->first();

        $rest->update([
            'end_time' => $now->toTimeString(),
        ]);

        return redirect()->back();
    }
    //退勤機能
    public function clockOut()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id',$user->id)
            ->where('date',$now->toDateString())
            ->first();

        $attendance->update([
            'clock_out' => $now->toTimeString(),
        ]);

        return redirect()->back();
    }
}
