<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pendingRequests = AttendanceCorrectRequest::where('user_id',$userId)
            ->where('status', 1)
            ->get();

        $approvedRequests = AttendanceCorrectRequest::where('user_id',$userId)
            ->where('status', 2)
            ->get();

        return view('request.list',compact('pendingRequests','approvedRequests'));
    }
}
