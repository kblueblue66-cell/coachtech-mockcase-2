<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','date','clock_in','clock_out','remarks'];

    public function rests(){
        return $this->hasMany(Rest::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getTotalRestTimeAttribute()
    {
        $totalRestSeconds = 0;

        foreach($this->rests as $rest){
            if($rest->start_time && $rest->end_time){
                $start = Carbon::parse($rest->start_time);
                $end = Carbon::parse($rest->end_time);
                $totalRestSeconds += $start->diffInSeconds($end);
            }
        }
        return gmdate('H:i',$totalRestSeconds);
    }
    public function getTotalWorkTimeAttribute()
    {
        if($this->clock_in && $this->clock_out){
            $in = Carbon::parse($this->clock_in);
            $out = Carbon::parse($this->clock_out);

            $staySeconds = $in->diffInSeconds($out);

            $restSeconds = 0;
            foreach($this->rests as $rest){
                if($rest->start_time && $rest->end_time){
                    $restSeconds += Carbon::parse($rest->start_time)->diffInSeconds(Carbon::parse($rest->end_time));
                }
            }
            $workSeconds = $staySeconds - $restSeconds;
            return gmdate('H:i',$workSeconds);
        }
        return '';
    }
}
