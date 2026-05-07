<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
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
            'clock_in'     => ['required','before:clock_out'],
            'clock_out'    => ['required','after:clock_in'],
            'rest_start.*' => ['required','after:clock_in','before:clock_out'],
            'rest_end.*'   => ['required','after:rest_start.*','before:clock_out'],
            'remarks'      => ['required'],
        ];
    }
    public function messages()
    {
        return[
            'clock_in.before'     => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.after'     => '出勤時間もしくは退勤時間が不適切な値です',
            'rest_start.*.before' => '休憩時間が不適切な値です',
            'rest_start.*.after'  => '休憩時間が不適切な値です',
            'rest_end.*.before'    => '休憩時間もしくは退勤時間が不適切な値です',
            'rest_end.*.after'    => '休憩時間もしくは退勤時間が不適切な値です',
            'remarks.required'    => '備考を記入してください',
        ];
    }
}
