@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="list__container">
    <h2 class="list__title">勤怠一覧</h2>

    <div class="month-nav">
        <a class="month-nav__link month-nav__link--prev" href="/attendance/list?month={{ $prevMonth }}">
            <img src="{{ asset('img/088deff71873c09816bca59dd0d7efa7308e8fba (1).png') }}" alt="前月" class="arrow-icon__right">前月
        </a>

        <div class="month-nav__current">
            <img src="{{ asset('img/50f4850c610ecd6f85b7ef666143260b91151a78.png')}}" alt="カレンダー" class="calender-icon">
            <span>{{ $displayDate->format('Y/m') }}</span>
        </div>

        <a class="month-nav__link month-nav__link--next" href="/attendance/list?month={{ $nextMonth }}">翌月
            <img src="{{ asset('img/088deff71873c09816bca59dd0d7efa7308e8fba (1).png')}}" alt="翌月" class='arrow-icon__left'>
        </a>
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
            @foreach($allDates as $dateString)
            @php
                //一時的にデータ取得
                $attendance = $attendances->get($dateString);
            @endphp
            <tr>
                {{-- データがあれば表示、なければ空 --}}
                <td>{{ \Carbon\Carbon::parse($dateString)->isoFormat('M/D(ddd)') }}</td>
                <td>{{ ($attendance && $attendance->clock_in) ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                <td>{{ ($attendance && $attendance->clock_out) ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                <td>{{$attendance ? $attendance->total_rest_time : '' }}</td>
                <td>{{$attendance ? $attendance->total_work_time : ''}}</td>
                <td>
                    {{-- データがある場合のに詳細リンク表示--}}
                    @if($attendance)
                        <a class="detail-link" href="/attendance/detail/{{ $attendance->id }}">詳細</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection