<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coachtech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
        @yield('css')
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="サイトのロゴ">
            </a>
            @yield('link')
        </div>
    </header>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>