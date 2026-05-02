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

    <div class="date-display">
        {{ $date }}
    </div>

    <div class="time-display">
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
@endsection