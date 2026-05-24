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
        $attendance = Attendance::with(['user','rests'])->findOrFail($id);

        $pendingRequest = AttendanceCorrectRequest::where('attendance_id',$id)
            ->where('status', 1)
            ->with('restCorrectRequests')
            ->first();

        $isPending = (bool)$pendingRequest;

        return view('attendance.detail',compact('attendance','isPending','pendingRequest'));
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
                $endTime = $request->rest_end[$index] ?? null;

                if(!empty($startTime) && !empty($endTime)){
                    $correctionRequest->restCorrectRequests()->create([
                        'revised_start_time' => $startTime,
                        'revised_end_time'   => $endTime,
                    ]);
                }
            }
        }
        return redirect()->route('attendance.detail',['id' => $id]);
    }
}