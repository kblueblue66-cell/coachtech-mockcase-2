@extends('layouts.app')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-list__container">
    <h2 class="request-list__title">申請一覧</h2>

    <div class="tabs">
        <input id="pending" type="radio" name="tab_item" checked>
        <label class="tab_item" for="pending">承認待ち</label>
        <input id="approved" type="radio" name="tab_item">
        <label class="tab_item" for="approved">承認済み</label>

        {{-- 承認待ち --}}
        <div class="tab_content" id="pending_content">
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
                </tbody>
            </table>
        </div>
        {{-- 承認済み --}}
        <div class="tab_content" id="approved_content">
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
                    @foreach($approvedRequests as $request)
                    <tr>
                        <td><span class="status-badge approved">承認済み</span></td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                        <td>{{ $request->remarks }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td><a href="{{ route('attendance.detail', ['id' => $request->attendance_id]) }}" class="detail-link">詳細</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
