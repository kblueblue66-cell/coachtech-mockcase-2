<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AdminAttendanceUpdateRequest;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date',now()->format('Y-m-d'));

        $attendances = Attendance::with(['user','rests'])
            ->whereDate('date',$date)
            ->get();

        return view('admin.attendance.list',compact('attendances','date'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user','rests'])->findOrFail($id);

        $isPending = AttendanceCorrectRequest::where('attendance_id',$id)
            ->where('status',1)
            ->exists();

        return view('admin.attendance.detail',[
            'attendance' => $attendance,
            'isPending'  => $isPending,
        ]);
    }

    public function update(AdminAttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        DB::transaction(function() use ($request,$attendance){
            $attendance->update([
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'remarks' => $request->remarks,
            ]);

            if($request->has('rests')){
                foreach($request->rests as $restId => $restData){
                    $rest = Rest::find($restId);
                    if($rest){
                        $rest->update([
                            'start_time' => $restData['start'],
                            'end_time' => $restData['end'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.attendance.list');
    }
}
