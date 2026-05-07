@extends('layouts.app')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">

    <h2>勤怠詳細</h2>

    <div class="attendance-detail-card">
        <form action="{{ url('/attendance/detail/' . $attendance->id) }}" method="post">
        @csrf
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>
                        <span class="date-year">{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('YYYY年') }}</span>
                        <span class="date-day">{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('M月D日') }}</span>
                        <input type="hidden" name="date" value="{{ $attendance->date }}">
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        @if($isPending)
                            {{ $attendance->clock_in }} ~ {{ $attendance->clock_out }}
                        @else
                            <input type="time" name="clock_in" value="{{ old('clock_in',$attendance->clock_in) }}">
                            @error('clock_in')
                                <p class="error">{{ $message }}</p>
                            @enderror
                            ~
                            <input type="time" name="clock_out" value="{{ old('clock_out',$attendance->clock_out) }}">
                            @error('clock_out')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        @endif
                    </td>
                </tr>

                @foreach($attendance->rests as $index => $rest)
                <tr>
                    <th>休憩{{ $loop->iteration }}</th>
                    <td>
                        @if($isPending)
                            {{ $rest->start_time }} ~ {{ $rest->end_time }}
                        @else
                            <input type="time" name="rest_start[]" value="{{ old('rest_start.'.$index,$rest->start_time) }}">
                            ~
                            <input type="time" name="rest_end[]" value="{{ old('rest_end.'.$index,$rest->end_time) }}">
                        @endif
                    </td>
                </tr>
                @endforeach

                @unless($isPending)
                <tr>
                    <th>休憩{{ $attendance->rests->count()+ 1 }}</th>
                    <td>
                        <input type="time" name="rest_start[]"> ~ <input type="time" name="rest_end[]">
                    </td>
                </tr>
                @endunless

                <tr>
                    <th>備考</th>
                    <td>
                        @if($isPending)
                            {{ $attendance->remarks }}
                        @else
                            <textarea name="remarks">{{ old('remarks',$attendance->remarks) }}</textarea>
                            @error('remarks')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        @endif
                    </td>
                </tr>
            </table>

            <div class="form-footer">
                @if($isPending)
                    <p class="pending-message" style="color: red">承認待ちのため修正はできません。</p>
                @else
                    <button type="submit" class="submit-btn">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection