<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectRequest extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_id','user_id','date','revised_clock_in','revised_clock_out','remarks','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function restCorrectRequests()
    {
        return $this->hasMany(RestCorrectRequest::class,'attendance_correct_request_id');
    }
}
