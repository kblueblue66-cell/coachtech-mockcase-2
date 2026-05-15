<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
    <title>@yield('title')</title>
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/attendance">
                    <img src="{{ asset('img/COACHTECHヘッダーロゴ (1).png')}}" alt="COACHTECH">
                </a>
            </div>

            <nav class="header__nav">
                <ul class="header__nav-list">
                    <li class="header__nav-item">
                        <a href="/attendance" class="header__nav-link">勤怠一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="{{ route('admin.staff.list') }}" class="header__nav-link">スタッフ一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="/stamp_correction_request/list" class="header__nav-link">申請一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <form action="{{ route('logout') }}" method="post" class="form-logout">
                            @csrf
                            <button type="submit" class="header__nav-button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>