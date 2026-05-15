<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::where('role',0)->get();

        return view('admin.staff.list',compact('users'));
    }

    public function show(Request $request,$id)
    {
        $user = User::findOrFail($id);

        $month = $request->query('month',Carbon::now()->format('Y-m'));
        $currentDate = Carbon::parse($month . '-01');

        $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with('rests')
            ->where('user_id',$id)
            ->whereMonth('date',$currentDate->month)
            ->whereYear('date',$currentDate->year)
            ->get()
            ->keyBy('date');

        $daysInMonth = $currentDate->daysInMonth;
        $calendar = [];
        for($i = 1; $i <= $daysInMonth; $i++){
            $date = $currentDate->copy()->day($i)->toDateString();
            $calendar[$date] = $attendances->get($date);
        }
        return view('admin.staff.attendance',compact('user','attendances','calendar','month','prevMonth','nextMonth'));
    }
}
