@extends('layouts.admin')

@section('title','修正申請承認')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approve.css') }}">
@endsection

@section('content')
<div class="attendance-detail__container">
    <h2 class="attendance-detail__title">勤怠詳細</h2>

    <form action="{{ route('admin.stamp_correction_request.approve',['attendance_correct_request_id' => $attendanceCorrectRequest->id]) }}" method="post">
        @csrf
        <table class="attendance-detail__table">
            <tr>
                <th>名前</th>
                <td>{{ $attendanceCorrectRequest->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>
                    <span class="date-year">{{ \Carbon\Carbon::parse($attendanceCorrectRequest->date)->format('Y年') }}</span>
                    <span class="date-day">{{ \Carbon\Carbon::parse($attendanceCorrectRequest->date)->format('n月j日') }}</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <span class="time-display">{{ \Carbon\Carbon::parse($attendanceCorrectRequest->revised_clock_in)->format('H:i') }}</span>
                    <span class="time-separator">〜</span>
                    <span class="time-display">{{ \Carbon\Carbon::parse($attendanceCorrectRequest->revised_clock_out)->format('H:i') }}</span>
                </td>
            </tr>
            @foreach($attendanceCorrectRequest->restCorrectRequests as $index => $rest)
            <tr>
                <th>休憩{{ $index > 0 ? $index +1 : '' }}</th>
                <td>
                    <span class="time-display">{{ \Carbon\Carbon::parse($rest->revised_start_time)->format('H:i') }}</span>
                    <span class="time-separator">〜</span>
                    <span class="time-display">{{ \Carbon\Carbon::parse($rest->revised_end_time)->format('H:i') }}</span>
                </td>
            </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td>
                    <div class="remarks-box">
                        {{ $attendanceCorrectRequest->remarks }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="attendance-detail__actions">
            @if($attendanceCorrectRequest->status === 1)
                <button type="submit" class="approve-btn">承認</button>
            @else
                <button type="submit" class="approved-label" disabled>承認済み</button>
            @endif
        </div>
    </form>
</div>
@endsection