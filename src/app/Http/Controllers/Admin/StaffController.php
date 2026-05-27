<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    public function export(Request $request,$id)
    {
        $user = User::findOrFail($id);

        $month = $request->query('month',now()->format('Y-m'));

        $attendances = Attendance::with('rests')
            ->where('user_id',$id)
            ->where('date','like',"$month%")
            ->orderBy('date','asc')
            ->get();

        $response = new StreamedResponse(function()use ($attendances){
            $handle = fopen('php://output','w');

            fputcsv($handle,['日付','出勤','退勤','休憩合計','合計']);

            foreach($attendances as $attendance){
                $totalRestSeconds = 0;
                foreach($attendance->rests as $rest){
                    if($rest->start_time && $rest->end_time){
                        $totalRestSeconds += strtotime($rest->end_time) - strtotime($rest->start_time);
                    }
                }
                $restTotalFormatted = ($totalRestSeconds > 0) ? gmdate("H:i",$totalRestSeconds) : "00:00";

                $clockInFormatted = $attendance->clock_in ? date('H:i',strtotime($attendance->clock_in)) : '';
                $clockOutFormatted = $attendance->clock_out ? date('H:i',strtotime($attendance->clock_out)) : '';

                $workTotalFormatted = "00:00";
                if($attendance->clock_in && $attendance->clock_out){
                    $totalSeconds = strtotime($attendance->clock_out) - strtotime($attendance->clock_in);
                    $workSeconds = $totalSeconds - $totalRestSeconds;

                    if($workSeconds > 0){
                        $workTotalFormatted = gmdate("H:i",$workSeconds);
                    }
                }

                fputcsv($handle,[
                    $attendance->date,
                    $clockInFormatted,
                    $clockOutFormatted,
                    $restTotalFormatted,
                    $workTotalFormatted,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv');
        $filename = 'attendance_' . $user->name . '_' . $month . '.csv';
        $response->headers->set('Content-Disposition','attachment; filename="' . $filename . '"');

        return $response;
    }
}
