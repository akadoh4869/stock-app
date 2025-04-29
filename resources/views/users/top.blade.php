<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/users/top.css')}}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mark-viewed-route" content="{{ route('invitation.markViewed') }}">
    <meta name="user-id" content="{{ Auth::id() }}">
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
                        <div class="user-name-group" id="user-name-toggle" style="cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <span class="user-name">
                                @if ($currentType === 'group' && isset($currentGroup))
                                    {{ $currentGroup->name }}
                                @elseif ($currentType === 'personal' && $inventory)
                                    {{ str_replace('の個人在庫', '', $inventory->name) }}
                                @else
                                    {{ $user->user_name }}
                                @endif
                            </span>
                            <i class="fa-solid fa-user" style="color: #5cc0ff; font-size: 16px;"></i>
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
                    @if(($personalInventories->count() + $groups->count()) < 3)
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
                    <i class="fa-solid fa-user-plus"></i><br>招待
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
            <div id="invitation-popup-overlay" class="invitation-popup-overlay">
                <div id="invitation-popup" class="invitation-popup">
                    <!-- ×ボタン（全招待を保留にする） -->
                    <button id="close-popup" class="invitation-close-btn">×</button>
                    <h3>グループ招待があります</h3>

                    @foreach($pendingInvitations as $invitation)
                        <div class="invitation-box">
                            <p>「{{ $invitation->group->name }}」に参加しますか？</p>

                            @if($totalSpaceCount < 3)
                                <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                                    <button name="response" value="accept">参加</button>
                                    <button name="response" value="decline">辞退</button>
                                </form>
                            @else
                                <form action="{{ route('invitation.markViewed') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="invitation_ids[]" value="{{ $invitation->id }}">
                                    <button type="submit" value="pending">保留</button>
                                </form>

                                <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                                    <button name="response" value="decline">辞退</button>
                                </form>
                            @endif
                        </div>
                    @endforeach

                    <!-- hidden form for × ボタン一括保留 -->
                    <form id="close-popup-form" action="{{ route('invitation.markViewed') }}" method="POST" style="display: none;">
                        @csrf
                        @foreach($pendingInvitations as $invitation)
                            <input type="hidden" name="invitation_ids[]" value="{{ $invitation->id }}">
                        @endforeach
                    </form>
                </div>
            </div>
        @endif
        <br>
        <br>
    </main>

    <!-- CDN読み込み（Echo & Pusher） -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
    <script src="{{ asset('js/users/top.js') }}"></script>
    
    
</body>
</html>
