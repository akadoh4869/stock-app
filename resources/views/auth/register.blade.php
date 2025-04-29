<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>新規登録 - </title>
    {{-- <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#111111"> --}}
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
    
    
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
        <h2>新規登録</h2>
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div>
                <label for="name">アカウント名</label><br>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="user_name">ユーザー名</label><br>
                <input id="user_name" type="text" name="user_name" value="{{ old('user_name') }}" required>
                @error('user_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password">パスワード</label><br>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password_confirmation">パスワード（確認用）</label><br>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <button type="submit">登録</button>
        </form>
    </div>
</body>
</html>
