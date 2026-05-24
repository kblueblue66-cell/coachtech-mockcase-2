@extends('layouts.admin')

@section('title','管理者用勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail__container">
    <h2 class="attendance-detail__title">勤怠詳細</h2>

    @if($isPending)
        <p class="attendance-detail__error-message" style="color:red">承認待ちのため修正はできません。</p>
    @endif

    <form action="{{ route('admin.attendance.update',['id' => $attendance->id]) }}" method="post" id="attendance-form">
        @csrf
        <table class="attendance-detail__table">
            <tr>
                <th>名前</th>
                <td>
                    {{ $attendance->user->name }}
                </td>
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
                    <input type="time" name="clock_in" value="{{ old('clock_in', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')) }}" {{ $isPending ? 'readonly' : '' }}>
                    @error('clock_in')
                        <p class="error">{{ $message }}</p>
                    @enderror
                    <span class="time-separator">〜</span>
                    <input type="time" name="clock_out" value="{{ old('clock_out',$attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}" {{ $isPending ? 'readonly' : '' }}>
                    @error('clock_out')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                <td>
                    <input type="time" name="rests[{{ $rest->id }}][start]" value="{{ old('rests.'.$rest->id.'.start', \Carbon\Carbon::parse($rest->start_time)->format('H:i')) }}" {{ $isPending ? 'readonly' : '' }}>
                    <span class="time-separator">〜</span>
                    <input type="time" name="rests[{{ $rest->id }}][end]" value="{{ old('rests.'.$rest->id.'.end', $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '') }}" {{ $isPending ? 'readonly' : '' }}>
                </td>
            </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="remarks" {{ $isPending ? 'readonly' : ''}}>{{ old('remarks', $attendance->remarks) }}</textarea>
                    @error('remarks')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
    </form>
    <div class="form-footer">
        @if(!$isPending)
            <button type="submit" class="submit-btn" form="attendance-form">修正</button>
        @endif
    </div>
</div>
@endsection
