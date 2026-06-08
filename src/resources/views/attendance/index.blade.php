@extends('layouts.app')

@section('title','勤怠登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="status-badge">
        {{ $status }}
    </div>

    <div id="realtime-date" class="date-display">
        {{ $date }}
    </div>

    <div id="realtime-time" class="time-display">
        {{ $time }}
    </div>

    <div class="button-group">
        @if($status === '勤務外')
            <form action="{{ route('attendance.clock-in') }}" method="post" >
                @csrf
                <button type="submit" class="btn-black">出勤</button>
            </form>

        @elseif($status === '出勤中')
            <form action="{{ route('attendance.clock-out') }}" method="post">
                @csrf
                <button type="submit" class="btn-black">退勤</button>
            </form>
            <form action="{{ route('attendance.rest-start') }}" method="post">
                @csrf
                <button type="submit" class="btn-white">休憩入</button>
            </form>

        @elseif($status === '休憩中')
            <form action="{{ route('attendance.rest-end') }}" method="post">
                @csrf
                <button type="submit" class="btn-white">休憩戻</button>
            </form>

        @elseif($status === '退勤済')
            <p class="finish-message">お疲れ様でした。</p>
        @endif
    </div>
</div>

<script>
    function updateClock(){
        const now = new Date();

        // 日付のフォーマット (例: 2023年6月1日(木)) [Source 4のデザインに準拠]
        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const date = now.getDate();
        const dayNum = now.getDay();
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        const day = weekdays[dayNum];

        // 時刻のフォーマット (例: 08:00)
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const dateString = `${year}年${month}月${date}日(${day})`;
        const timeString = `${hours}:${minutes}`;

        // 画面の要素を書き換え
        document.getElementById('realtime-date').textContent = dateString;
        document.getElementById('realtime-time').textContent = timeString;
    }

    setInterval(updateClock,1000);

    updateClock();
</script>
@endsection