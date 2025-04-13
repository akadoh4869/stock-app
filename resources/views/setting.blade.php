<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>設定ページ</title>
</head>
<body>
    <h2>保留中のグループ招待</h2>

@if($pendingInvitations->isEmpty())
    <p>保留中のグループ招待はありません。</p>
@else
    <ul>
        @foreach($pendingInvitations as $invitation)
            <li style="margin-bottom: 10px;">
                「{{ $invitation->group->name }}」への招待
                <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                    @if($totalSpaceCount < 3)
                        <button name="response" value="accept">参加</button>
                    @endif
                    <button name="response" value="decline">辞退</button>
                </form>
            </li>
        @endforeach
    </ul>
@endif

    
</body>
</html>