@extends('layouts.admin')

@section('title','修正申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request-list.css') }}">
@endsection

@section('content')
<div class="request-list__container">
    <h2 class="request-list__title">申請一覧</h2>

    <div class="tab-menu">
        <a href="{{ route('stamp_correction_request.list',['tab' => 'pending']) }}" class="tab-item {{ $tab === 'pending' ? 'is-active' : '' }}">承認待ち</a>
        <a href="{{ route('stamp_correction_request.list',['tab' => 'approved']) }}" class="tab-item {{ $tab === 'approved' ? 'is-active' : '' }}">承認済み</a>
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
            @foreach($requests as $req)
            <tr>
                <td>{{ $req->status === 1 ? '承認待ち' : '承認済み' }}</td>
                <td>{{ $req->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($req->date)->format('Y/m/d') }}</td>
                <td>{{ $req->remarks }}</td>
                <td>{{ $req->created_at->format('Y/m/d') }}</td>
                <td>
                    <a href="{{ route('admin.stamp_correction_request.approve',['attendance_correct_request_id' => $req->id]) }}" class="detail-link">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection