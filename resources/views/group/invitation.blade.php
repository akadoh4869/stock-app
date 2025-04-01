<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー招待</title>
    <link rel="stylesheet" href="{{ asset('css/group/invite.css') }}">
</head>
<body>
    <h1>メンバー招待</h1>

    <form method="GET" action="{{ route('group.invite') }}">
        <input type="text" name="keyword" placeholder="ユーザーネーム または アカウント名" value="{{ request('keyword') }}">
        <button type="submit">検索</button>
    </form>

    @if(isset($results) && count($results) > 0)
        <ul>
            @foreach($results as $user)
                <li>
                    {{ $user->user_name }}（{{ $user->name }}）

                    <form method="POST" action="{{ route('group.invite.send') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                        <button type="submit">招待</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @elseif(request('keyword'))
        <p>一致するユーザーが見つかりませんでした。</p>
    @endif

    <br>
    <a href="{{ route('stock.index') }}">← トップに戻る</a>
</body>
</html>
