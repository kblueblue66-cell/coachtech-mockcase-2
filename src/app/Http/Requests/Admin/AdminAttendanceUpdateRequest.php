<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => ['required','date_format:H:i'],
            'clock_out' => ['required','date_format:H:i','after:clock_in'],
            'rests.*.start' => ['required','date_format:H:i','after_or_equal:clock_in','before_or_equal:clock_out'],
            'rests.*.end' => ['required','date_format:H:i','after:rests.*.start','before_or_equal:clock_out'],
            'remarks' => ['required'],
        ];
    }

    public function messages()
    {
        return[
            'clock_in.required' => '出勤時間もしくは退勤時間が不適切な値で',
            'clock_out.required' => '出勤時間もしくは退勤時間が不適切な値で',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'rests.*.start.required' => '休憩時間が不適切な値です',
            'rests.*.start.after_or_equal' => '休憩時間が不適切な値です',
            'rests.*.start.before_or_equal' => '休憩時間が不適切な値です',
            'rests.*.end.required' => '休憩時間もしくは退勤時間が不適切な値です',
            'rests.*.end.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'rests.*.end.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',
            'remarks.required' => '備考を記入してください',
        ];
    }
}
