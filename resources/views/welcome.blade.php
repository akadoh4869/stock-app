<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="在庫管理はすとログ">
    <title>すとログ</title>
    {{-- <title>TimeMafia - タイムマフィアで楽しくクイズ！</title> --}}
    {{-- <link rel="manifest" href="/manifest.json"> --}}
    {{-- <meta name="theme-color" content="#111111"> --}}

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

</head>
<body>
    <div class="background">
        <!-- 上部 -->
        <div class="circle circle-top1 floating"></div>
        <div class="circle circle-top2 pulse"></div>
        <div class="circle circle-top3 floating-delay1"></div>
        <div class="circle circle-top4 float-x"></div>
        <div class="circle circle-top5 floating-delay2"></div>
        <div class="circle circle-top6 pulse"></div>
        <div class="circle circle-top7 float-x"></div>

        <!-- 下部 -->
        <div class="circle circle-bottom1 floating"></div>
        <div class="circle circle-bottom2 pulse"></div>
        <div class="circle circle-bottom3 floating-delay2"></div>
        <div class="circle circle-bottom4 float-x"></div>
        <div class="circle circle-bottom5 floating-delay1"></div>
        <div class="circle circle-bottom6 pulse"></div>
        <div class="circle circle-bottom7 float-x"></div>

    </div>

    <div class="container">
        <div class="subtitle">ストック管理するなら</div>
        <div class="title">すとログ</div>
        <a href="{{ route('register') }}" class="button btn-register">新規登録</a>
        <a href="{{ route('login') }}" class="button btn-login">ログイン</a>
    </div>
    
</body>
</html>
