<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('partials.head')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <title>管理者ページ</title>
</head>
<body>
    <div class="user-count">
        <h1>ユーザー登録者数: {{ $userCount }}</h1>
    </div>
    <div class="user-list">
        <h2>ユーザー名一覧</h2>
        <ul>
            @foreach ($users as $user)
                <li>{{ $user->user_name }}</li>
            @endforeach
        </ul>
    </div>
    
</body>
</html>