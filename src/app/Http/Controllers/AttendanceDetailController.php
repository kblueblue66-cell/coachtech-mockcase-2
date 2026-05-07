<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;//モデル
use App\Http\Requests\AttendanceCorrectionRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceDetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('rests')->findOrFail($id);

        $isPending = AttendanceCorrectRequest::where('attendance_id',$id)
            ->where('status', 1)
            ->exists();

        return view('attendance.detail',compact('attendance','isPending'));
    }
    public function update(AttendanceCorrectionRequest $request,$id)
    {
        $correctionRequest = AttendanceCorrectRequest::create([
            'attendance_id'     => $id,
            'user_id'           => Auth::id(),
            'date'              => $request->date,
            'revised_clock_in'  => $request->clock_in,
            'revised_clock_out' => $request->clock_out,
            'remarks'           => $request->remarks,
            'status'            => 1,
        ]);

        if($request->has('rest_start')){
            foreach($request->rest_start as $index => $startTime){
                $correctionRequest->restCorrectionRequests()->create([
                    'revised_start_time' => $startTime,
                    'revised_end_time'   => $request->rest_end[$index],
                ]);
            }
        }

        return redirect('/stamp_correction_request/list');
    }
}
