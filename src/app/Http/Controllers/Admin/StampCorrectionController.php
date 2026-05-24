<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\RestCorrectRequest;
use Illuminate\Support\Facades\DB;


class StampCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab','pending');

        $status = ($tab === 'approved')? 2 : 1;

        $requests = AttendanceCorrectRequest::with('user')
            ->where('status',$status)
            ->orderBy('created_at','desc')
            ->get();

        return view('admin.request.list',compact('requests','tab'));
    }
    public function show($attendance_correct_request_id)
    {
        $attendanceCorrectRequest = AttendanceCorrectRequest::with(['user','restCorrectRequests'])->findOrFail($attendance_correct_request_id);

        return view('admin.request.approve',compact('attendanceCorrectRequest'));
    }
    public function approve($attendance_correct_request_id)
    {
        $requestData = AttendanceCorrectRequest::with('restCorrectRequests')
            ->findOrFail($attendance_correct_request_id);

        DB::transaction(function()use($requestData){

            $attendance = Attendance::findOrFail($requestData->attendance_id);
            $attendance->update([
                'clock_in' => $requestData->revised_clock_in,
                'clock_out'=> $requestData->revised_clock_out,
            ]);

            Rest::where('attendance_id',$attendance->id)->delete();

            foreach($requestData->restCorrectRequests as $restRequest){
                Rest::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $restRequest->revised_start_time,
                    'end_time' => $restRequest->revised_end_time,
                ]);
            }

            $requestData->update(['status' => 2]);
        });

        return back();
    }
}
