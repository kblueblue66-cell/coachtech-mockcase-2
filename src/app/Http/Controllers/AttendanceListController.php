<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceListController extends Controller
{
    public function index(Request $request)
    {
        //表示する年月を決定する
        $month = $request->query('month',Carbon::now()->format('Y-m'));
        $displayDate = Carbon::parse($month);
        //１ヶ月分の勤怠全てが表示される
        $startOfMonth = $displayDate->copy()->startOfMonth();
        $endOfMonth = $displayDate->copy()->endOfMonth();

        $allDates = [];
        for($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()){
            $allDates[] = $date->toDateString();
        }

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereYear('date',$displayDate->year)
            ->whereMonth('date',$displayDate->month)
            ->get()
            ->keyBy('date');

        //前月と翌月のリンク作成
        $prevMonth = $displayDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $displayDate->copy()->addMonth()->format('Y-m');

        return view('attendance.list',compact('allDates','attendances','displayDate','prevMonth','nextMonth'));
    }
}
