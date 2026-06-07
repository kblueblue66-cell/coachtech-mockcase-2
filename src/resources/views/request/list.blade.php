@extends('layouts.app')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-list__container">
    <h2 class="request-list__title">申請一覧</h2>

    <div class="tabs">
        <a href="/stamp_correction_request/list?status=pending" class="tab-item {{ request('status') !== 'approved' ? 'is-active' : '' }}">
            承認待ち
        </a>
        <a href="/stamp_correction_request/list?status=approved" class="tab-item {{ request('status') == 'approved' ? 'is-active' : '' }}">
            承認済み
        </a>
    </div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @if(request('status') !== 'approved')
                @foreach($pendingRequests as $request)
                <tr>
                    <td><span class="status-badge pending">承認待ち</span></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                    <td>{{ $request->remarks }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td><a href="{{ route('attendance.detail', ['id' => $request->attendance_id]) }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            @else
                @foreach($approvedRequests as $request)
                <tr>
                    <td><span class="status-badge approved">承認済み</span></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                    <td>{{ $request->remarks }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td><a href="/stamp_correction_request/approve/{{ $request->id }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
