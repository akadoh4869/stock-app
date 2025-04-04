<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/users/top.css')}}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <title>トップページ</title>
</head>
<body>

    <!-- ▼ 現在のスペース名 -->
    <div id="space-selector" style="cursor: pointer; display: inline-block;">
        <h2 style="display: inline;">
            @if ($currentType === 'group' && isset($currentGroup))
                {{ $currentGroup->name }} グループ
            @elseif ($currentType === 'personal' && $inventory)
                {{ $inventory->name }}
            @else
                {{ $user->user_name }}
            @endif
            <i class="fa fa-chevron-down"></i>
        </h2>
    </div>

    <!-- ▼ スペース切り替えメニュー -->
    <div id="space-menu" style="display: none; margin-bottom: 20px;">
        @foreach ($personalInventories as $inv)
            <form method="POST" action="{{ route('stock.switch', ['type' => 'personal', 'inventoryId' => $inv->id]) }}">
                @csrf
                <button type="submit" style="{{ $inventory && $inventory->id === $inv->id ? 'font-weight: bold;' : '' }}">
                    {{ $inv->name }}
                </button>
            </form>
        @endforeach

        @foreach ($groups as $group)
            <form method="POST" action="{{ route('stock.switch', ['type' => 'group', 'groupId' => $group->id]) }}">
                @csrf
                <button type="submit" style="{{ $currentType === 'group' && $currentGroup && $group->id === $currentGroup->id ? 'font-weight: bold;' : '' }}">
                    {{ $group->name }}
                </button>
            </form>
        @endforeach

        @if(($personalInventories->count() + $groups->count()) < 5)
            <button onclick="window.location.href='{{ route('group.create.form') }}'" style="margin-top: 10px; font-weight: bold;">
                ＋ スペースを追加
            </button>
        @endif

    </div>


    <h3>在庫カテゴリ</h3>

    @if(isset($categories) && count($categories) < 5)
        <div style="margin-bottom: 10px;">
            <button onclick="window.location.href='{{ route('category.create') }}'">
                カテゴリ＋アイテム一括作成
            </button>
        </div>
    @endif

    @if(isset($categories) && count($categories) > 0)
        <ul class="category-list">
            @foreach($categories as $category)
                <li class="category-item">
                    <div class="category-dot" style="background-color: {{ $loop->index % 2 == 0 ? 'pink' : 'cyan' }};"></div>
                    <a href="{{ route('category.items', ['id' => $category->id]) }}">
                        {{ $category->name }}（{{ $category->items->count() }}）
                    </a>
                    <form method="POST" action="{{ route('category.destroy', $category->id) }}" style="display: inline;" onsubmit="return confirmDelete({{ $category->items->count() }})">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">削除</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    @if(!isset($categories) || count($categories) < 5)
        <div id="category-add-section" style="margin-top: 10px;">
            <button id="show-category-input" style="font-weight: bold;">
                ＋ カテゴリを追加
            </button>
            <form id="category-form" action="{{ route('category.store') }}" method="POST" style="display: none; margin-top: 10px;">
                @csrf
                <input type="text" name="category_name" placeholder="カテゴリ名" required>
                <input type="hidden" name="inventory_id" value="{{ $inventory->id ?? '' }}">
                <button type="submit">追加</button>
            </form>
        </div>
    @endif

    @if($pendingInvitations->isNotEmpty())
    <div id="invitation-popup" class="popup">
        <h3>グループ招待があります</h3>
        @foreach($pendingInvitations as $invitation)
            <div class="invitation-box">
                <p>「{{ $invitation->group->name }}」に参加しますか？</p>
                <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                    <button name="response" value="accept">参加</button>
                    <button name="response" value="decline">辞退</button>
                </form>
            </div>
        @endforeach
    </div>
    @endif

    <br>
    <br>
    @if($currentType === 'group' && isset($currentGroup))
        <button onclick="window.location.href='{{ route('group.invite') }}'">メンバーを招待</button>

        <form method="POST" action="{{ route('group.leave', ['groupId' => $currentGroup->id]) }}" onsubmit="return confirm('本当にこのグループから退出しますか？');" style="margin-top: 10px;">
            @csrf
            <button type="submit" style="color: red;">グループから退出</button>
        </form>
    @elseif($currentType === 'personal' && $inventory)
        <form method="POST" action="{{ route('inventory.destroy', ['inventory' => $inventory->id]) }}" onsubmit="return confirm('この個人スペースを削除しますか？');" style="margin-top: 10px;">
            @csrf
            @method('DELETE')
            <button type="submit" style="color: red;">スペースを削除</button>
        </form>
    @endif


    <button onclick="window.location.href='/history'">履歴</button>
    <button onclick="window.location.href='/settings'">設定</button>

    <!-- CDN読み込み（Echo & Pusher） -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

    <script>
        document.getElementById('space-selector').addEventListener('click', function () {
            const menu = document.getElementById('space-menu');
            menu.style.display = (menu.style.display === 'none') ? 'block' : 'none';
        });

        document.getElementById('show-category-input')?.addEventListener('click', function () {
            document.getElementById('category-form').style.display = 'block';
            this.style.display = 'none';
        });

        function confirmDelete(itemCount) {
            if (itemCount > 0) {
                return confirm('このカテゴリにはアイテムが含まれています。一括削除してもよろしいですか？');
            } else {
                return confirm('このカテゴリを削除しますか？');
            }
        }

        // Echo 初期化
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'local',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws'], // ← WebSocketのみに限定
        });

        // ユーザーごとのプライベートチャンネルでリアルタイム通知受信
        Echo.private(`user.{{ Auth::id() }}`)
            .listen('.InvitationSent', (e) => {
                const token = '{{ csrf_token() }}';
                const popup = document.getElementById('invitation-popup');

                const box = document.createElement('div');
                box.classList.add('invitation-box');
                box.innerHTML = `
                    <p>「${e.group_name}」に参加しますか？</p>
                    <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${token}">
                        <input type="hidden" name="invitation_id" value="${e.invitation_id}">
                        <button name="response" value="accept">参加</button>
                        <button name="response" value="decline">辞退</button>
                    </form>
                `;

                if (popup) {
                    popup.appendChild(box);
                    popup.style.display = 'block';
                } else {
                    const newPopup = document.createElement('div');
                    newPopup.id = 'invitation-popup';
                    newPopup.classList.add('popup');
                    newPopup.innerHTML = `<h3>グループ招待があります</h3>`;
                    newPopup.appendChild(box);
                    document.body.appendChild(newPopup);
                }
            });
    </script>

</body>
</html>
