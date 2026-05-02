@extends('layouts.guest')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h1 class="login-title">ログイン</h1>

    <form method="post" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="button-login">ログインする</button>
    </form>

    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection