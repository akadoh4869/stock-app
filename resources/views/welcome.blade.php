<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="在庫管理はストログ">
    <title>ストログ</title>
    @include('partials.head')
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
    
    <div class="container">
        <!-- アーチ状サブタイトル -->
        <svg width="280" height="100" viewBox="0 0 280 50" class="subtitle-arc">
            <defs>
            <path id="curve" d="M20,45 A120,40 0 0,1 260,45" />
            </defs>
            <text width="100%">
            <textPath href="#curve" startOffset="50%" text-anchor="middle" class="subtitle-text">
                ストックの記録は
            </textPath>
            </text>
        </svg>
  
  
        <div class="title">ストログ</div>
        <a href="{{ route('register') }}" class="button btn-register">新規登録</a>
        <a href="{{ route('login') }}" class="button btn-login">ログイン</a>
    </div>
    
</body>
</html>
