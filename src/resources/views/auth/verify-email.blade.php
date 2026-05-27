@extends('layouts.guest')

@section('title','メール認証')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify_email.css') }}">
@endsection

@section('content')
<div class="verify-email__container">
    <div class="verify-email__content">
        <p class="verify-email__message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <div class="verify-email__action">
            <a href="https://mailtrap.io/sandboxes" target="_blank" class="verify-email__btn">
                認証はこちらから
            </a>
        </div>

        <form class="verify-email__resend-form" method="post" action="{{ route('verification.send') }}" >
            @csrf
            <button type="submit" class="verify-email__resend-link">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection