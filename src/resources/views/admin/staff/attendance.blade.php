@extends('layouts.admin')

@section('title','スタッフ別勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff-attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="staff-attendance__title">
        <h2>{{ $user->name }}さんの勤怠</h2>
    </div>

    <div class="month-nav">
        <a href="{{ route('admin.staff.attendance.show',['id' => $user->id,'month' => $prevMonth]) }}" class="nav-btn">← 前月</a>
        <span class="current-month">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        <a href="{{ route('admin.staff.attendance.show',['id' => $user->id,'month' => $nextMonth]) }}" class="nav-btn">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendar as $date => $attendance)
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->isoFormat('MM/DD(ddd)') }}</td>
                <td>{{ $attendance ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                <td>{{ ($attendance && $attendance->clock_out) ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                <td>{{ $attendance ?$attendance->total_rest_time : '' }}</td>
                <td>{{ $attendance ? $attendance->total_work_time : '' }}</td>
                <td>
                    @if($attendance)
                    <a href="{{ route('admin.attendance.show',['id' => $attendance->id]) }}" class="detail-link">詳細</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-export">
        <form action="{{ route('admin.staff.attendance.export',['id' => $user->id]) }}" method="get">
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="csv-btn">CSV出力</button>
        </form>
    </div>
</div>
@endsection