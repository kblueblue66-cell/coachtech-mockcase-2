<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/guest.css') }}">
    @yield('css')
    <title>@yield('title')</title>
</head>

<body>
    <header class="simple-header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="/">
                    <img src="{{ asset('img/COACHTECHヘッダーロゴ (1).png')}}" alt="COACHTECH">
                </a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>