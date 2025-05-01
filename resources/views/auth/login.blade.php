<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ストログ') }}</title>
    @include('partials.head')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <style>

        
    </style>
</head>
    <body>

        
    
        <div class="container">
            <a href="{{ url('/') }}" class="back-btn">← 戻る</a>
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
   
    </body>
    
   



</html>
