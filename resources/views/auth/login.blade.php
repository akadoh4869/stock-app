<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'すとログ') }}</title>
    {{-- <link rel="manifest" href="/manifest.json"> --}}
    {{-- <meta name="theme-color" content="#111111"> --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <style>

        
    </style>
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
        
    
        <a href="{{ url('/') }}" class="back-btn">戻る</a>
    
        <div class="container">
            <h2>ログイン</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <label for="user_name">ユーザー名</label><br>
                    <input id="user_name" type="text" name="user_name" autocomplete="username" value="{{ old('user_name') }}" required autofocus>

                </div>
                <div>
                    <label for="password">パスワード</label><br>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>

                </div>
                <button type="submit">ログイン</button>
            </form>
        </div>

        
        <!-- JavaScriptを追加 -->
        <script>
            function redirectToWelcome() {
                window.location.href = "{{ url('/') }}";
            }
        </script>
   
    </body>
    
   



</html>
