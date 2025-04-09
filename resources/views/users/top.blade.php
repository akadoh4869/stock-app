<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/users/top.css')}}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>トップページ</title>
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <div class="user-name" id="user-name-toggle" style="cursor: pointer;">
                            <!-- ユーザー名 -->
                            @if ($currentType === 'group' && isset($currentGroup))
                                {{ $currentGroup->name }}
                            @elseif ($currentType === 'personal' && $inventory)
                                {{ str_replace('の個人在庫', '', $inventory->name) }}
                            @else
                                {{ $user->user_name }}
                            @endif
                        </div>
                        <div class="app-name">ストログ</div>
                    </div>
                </div>
            </div>
            
    
            <!-- ▼ スペース切り替えポップアップ -->
            <div id="space-popup-overlay" style="display: none;" class="popup-overlay">
                <div id="space-menu" class="popup-menu">
                    @foreach ($personalInventories as $inv)
                        <form method="POST" action="{{ route('stock.switch', ['type' => 'personal', 'inventoryId' => $inv->id]) }}">
                            @csrf
                            <button type="submit" class="{{ $inventory && $inventory->id === $inv->id ? 'selected' : '' }}">
                                {{ str_replace('の個人在庫', '', $inv->name) }}
                            </button>
                        </form>
                    @endforeach
    
                    @foreach ($groups as $group)
                        <form method="POST" action="{{ route('stock.switch', ['type' => 'group', 'groupId' => $group->id]) }}">
                            @csrf
                            <button type="submit" class="{{ $currentType === 'group' && $currentGroup && $group->id === $currentGroup->id ? 'selected' : '' }}">
                                {{-- {{ $group->name }} （{{ $group->users->count() }}） --}}
                                {{ $group->name }} <span class="member-count">（{{ $group->users->count() }}）</span>

                            </button>
                        </form>
                    @endforeach
    
                    <!-- グループ一覧の最後に表示 -->
                    @if(($personalInventories->count() + $groups->count()) < 5)
                        <div class="add-space-container">
                            <button onclick="window.location.href='{{ route('group.create.form') }}'" class="add-space-button-no-border">
                                <i class="fa-solid fa-circle-plus"></i> グループを追加
                            </button>
                        </div>
                    @endif

                </div>
            </div>
    
            
        </div>
        <div class="main">
            @if(isset($categories) && count($categories) < 5)
                <div class="bulk-create" style="margin-bottom: 10px;">
                    <button onclick="window.location.href='{{ route('category.create') }}'">
                        カテゴリ＋アイテム一括作成
                    </button>
                </div>
            @endif
    
            @if(isset($categories) && count($categories) > 0)
                <ul class="category-list">
                    @foreach($categories as $category)
                    <li class="category-item"
                        data-id="{{ $category->id }}"
                        ontouchstart="handleTouchStart(event)"
                        ontouchmove="handleTouchMove(event, this)"
                        ontouchend="handleTouchEnd(event, this)">
                        
                        <div class="category-dot" style="background-color: {{ $loop->index % 2 == 0 ? 'hotpink' : 'cyan' }}"></div>

                        <div class="category-content" style="border-color: {{ $loop->index % 2 == 0 ? 'cyan' : 'hotpink' }}">
                            <a href="{{ route('category.items', ['id' => $category->id]) }}"
                            style="text-decoration: none; color: inherit;">
                                {{ $category->name }}
                            </a>
                            <span class="category-count">（{{ $category->items->count() }}）</span>
                        </div>

                        <!-- 削除ボタン -->
                        <form method="POST" action="{{ route('category.destroy', $category->id) }}"
                            onsubmit="return confirmDelete({{ $category->items->count() }})">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">削除</button>
                        </form>
                    </li>     
                    @endforeach
                </ul>
            @endif

            @php
                $lastIndex = isset($categories) ? count($categories) : 0;
            @endphp

            @if(!isset($categories) || count($categories) < 5)
                <li class="category-item category-add">
                    <div class="category-dot"
                        style="background-color: hotpink; display: flex; justify-content: center; align-items: center; color: white; font-weight: bold;">
                        ＋
                    </div>
                    <div class="category-content"
                        style="border-color: {{ $lastIndex % 2 === 0 ? 'cyan' : 'hotpink' }};">
                        <form id="category-form" action="{{ route('category.store') }}" method="POST">
                            @csrf
                            <input type="text" id="new-category-name" name="category_name" placeholder="カテゴリ追加" required style="border: none; background: transparent; outline: none; font-size: 16px; color: gray; width: 80%;">  
                            <input type="hidden" name="inventory_id" value="{{ $inventory->id ?? '' }}">
                        </form>
                    </div>
                </li>
            @endif
        </div>
        
        <!-- フッター背景画像（画面最下部に固定） -->
        <div class="line-bottom-fixed">
            <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
        </div>

        <!-- フッターのボタン群（画像の上に表示） -->
        <div class="footer-overlay-fixed">
            @if($currentType === 'group' && isset($currentGroup))
                <button onclick="window.location.href='{{ route('group.invite') }}'">
                    <i class="fa-solid fa-user-plus"></i><br>メンバー招待
                </button>
                <form method="POST" action="{{ route('group.leave', ['groupId' => $currentGroup->id]) }}"
                    onsubmit="return confirm('本当にこのグループから退出しますか？');">
                    @csrf
                    <button type="submit" style="color: red;">
                        <i class="fa-solid fa-user-minus"></i><br>退出
                    </button>
                </form>
            @elseif($currentType === 'personal' && $inventory)
                <form method="POST" action="{{ route('inventory.destroy', ['inventory' => $inventory->id]) }}"
                    onsubmit="return confirm('この個人スペースを削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: red;">
                        <i class="fa-solid fa-trash"></i><br>削除
                    </button>
                </form>
            @endif

            <button onclick="window.location.href='/history'">
                <i class="fa-solid fa-clock"></i><br>履歴
            </button>
            <button onclick="window.location.href='/settings'">
                <i class="fa-solid fa-gear"></i><br>設定
            </button>
        </div>

    
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
        

    </main>
    
    

    <!-- CDN読み込み（Echo & Pusher） -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

    <script>

        let startX = 0;

        function handleTouchStart(e) {
            startX = e.touches[0].clientX;
        }

        function handleTouchMove(e, element) {
            const deltaX = e.touches[0].clientX - startX;
            if (deltaX < -50) {
                element.classList.add('swiped');
            } else if (deltaX > 50) {
                element.classList.remove('swiped');
            }
        }

        function handleTouchEnd(e, element) {
            // no-op
        }


        // スペース切り替えポップアップを開く
        document.getElementById('user-name-toggle').addEventListener('click', function () {
            document.getElementById('space-popup-overlay').style.display = 'flex';
        });

        // オーバーレイをクリックしたら閉じる
        document.getElementById('space-popup-overlay').addEventListener('click', function (e) {
            if (e.target.id === 'space-popup-overlay') {
                e.target.style.display = 'none';
            }
        });

        // 削除確認
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

            function handleCategorySubmit(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    event.target.closest('form').submit();
                }
            }

            document.getElementById('new-category-name').addEventListener('blur', function () {
            const input = this;
            const name = input.value.trim();

            if (name === '') return;

            fetch('/category/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    category_name: name,
                    inventory_id: '{{ $inventory->id ?? '' }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.id && data.name) {
                    const categoryList = document.querySelector('.category-list');

                    // 新しいカテゴリの li を作成
                    const li = document.createElement('li');
                    li.classList.add('category-item');
                    li.dataset.id = data.id;

                    const dot = document.createElement('div');
                    dot.classList.add('category-dot');
                    dot.style.backgroundColor = (categoryList.children.length % 2 === 0) ? 'pink' : 'cyan';

                    const content = document.createElement('div');
                    content.classList.add('category-content');
                    content.style.borderColor = (categoryList.children.length % 2 === 0) ? 'cyan' : 'hotpink';

                    const span = document.createElement('span');
                    span.classList.add('category-name');
                    span.textContent = data.name;

                    const count = document.createElement('span');
                    count.classList.add('category-count');
                    count.textContent = '（0）';

                    const inputEdit = document.createElement('input');
                    inputEdit.classList.add('category-edit-input');
                    inputEdit.type = 'text';
                    inputEdit.value = data.name;
                    inputEdit.style.display = 'none';

                    content.appendChild(span);
                    content.appendChild(inputEdit);
                    content.appendChild(count);

                    li.appendChild(dot);
                    li.appendChild(content);

                    categoryList.appendChild(li);
                }

                input.value = '';
            });
        });

    </script>
    
</body>
</html>
