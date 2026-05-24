@extends('layouts.admin')

@section('title','管理者用勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-title">
        <h2>{{ \Carbon\Carbon::parse($date)->isoFormat('YYYY年M月D日') }}の勤怠</h2>
    </div>

    <div class="date-navigation">
        <a href="{{ route('admin.attendance.list',['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}" class="nav-link">
            <img src="{{ asset('img/088deff71873c09816bca59dd0d7efa7308e8fba (1).png') }}" alt="前月" class="arrow-icon__right">前日
        </a>

        <span class="current-date-text">
            <img src="{{ asset('img/50f4850c610ecd6f85b7ef666143260b91151a78.png')}}" alt="カレンダー" class="calender-icon">
            {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
        </span>

        <a href="{{ route('admin.attendance.list',['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}" class="nav-link">翌日
            <img src="{{ asset('img/088deff71873c09816bca59dd0d7efa7308e8fba (1).png')}}" alt="翌月" class='arrow-icon__left'>
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                <td>{{ $attendance->total_rest_time }}</td>
                <td>{{ $attendance->total_work_time }}</td>
                <td>
                    <a href="{{ route('admin.attendance.show',['id' => $attendance->id]) }}" class="detail-link">詳細</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="no-date">指定された日付の勤怠記録はありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection