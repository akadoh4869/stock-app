<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/users/top.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>トップページ</title>
</head>
<body>

     <!-- ▼ 現在のスペース名（クリックで切り替えメニュー表示） -->
    <div id="space-selector" style="cursor: pointer; display: inline-block;">
        <h2 style="display: inline;">
            {{ $currentType === 'group' && isset($currentGroup) ? $currentGroup->name . ' グループ' : $user->user_name }}
            <i class="fa fa-chevron-down"></i>
        </h2>
    </div>

   
    <!-- ▼ スペース切り替えメニュー -->
    <div id="space-menu" style="display: none; margin-bottom: 20px;">
        <!-- 個人スペース -->
        <form method="POST" action="{{ route('stock.switch', ['type' => 'personal']) }}">
            @csrf
            <button type="submit"
                style="{{ $currentType === 'personal' ? 'font-weight: bold;' : '' }}">
                {{ $user->user_name }}
            </button>
        </form>

        <!-- グループスペース一覧 -->
        @foreach ($groups as $group)
            <form method="POST" action="{{ route('stock.switch', ['type' => 'group', 'groupId' => $group->id]) }}">
                @csrf
                <button type="submit"
                    style="{{ $currentType === 'group' && $currentGroup && $group->id === $currentGroup->id ? 'font-weight: bold;' : '' }}">
                    {{ $group->name }} 
                </button>
            </form>
        @endforeach

        <!-- ✅ 合計スペース数が5未満のときに表示 -->
        @if($groups->count() + 1 < 5)
            <button onclick="window.location.href='/create-group'" style="margin-top: 10px; font-weight: bold;">
                ＋ スペースを追加
            </button>
        @endif
    </div>

    <h3>在庫カテゴリ</h3>

    <!-- ✅ 一括作成ボタン -->
    <div style="margin-bottom: 10px;">
        <button onclick="window.location.href='/category-bulk-create'">
            カテゴリ＋アイテム一括作成
        </button>
    </div>
    
    @if(isset($categories) && count($categories) > 0)
        <ul class="category-list">
            @foreach($categories as $category)
                <li class="category-item">
                    <div class="category-dot" style="background-color: {{ $loop->index % 2 == 0 ? 'pink' : 'cyan' }};"></div>
                    
                    <!-- ✅ カテゴリ名をクリックで遷移 -->
                    <a href="{{ route('category.items', ['id' => $category->id]) }}">
                        {{ $category->name }}（{{ $category->items->count() }}）
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    
    <!-- ✅ カテゴリ追加（5つ未満のとき） -->
    @if(!isset($categories) || count($categories) < 5)
        <div id="category-add-section" style="margin-top: 10px;">
            <button id="show-category-input" style="font-weight: bold;">
                ＋ カテゴリを追加
            </button>
    
            <!-- 入力フォーム（初期は非表示） -->
            <form id="category-form" action="{{ route('category.store') }}" method="POST" style="display: none; margin-top: 10px;">
                @csrf
                <input type="text" name="name" placeholder="カテゴリ名" required>
                <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">
                <button type="submit">追加</button>
            </form>
        </div>
    @endif
    
    
    


    <br>
    <button onclick="window.location.href='/create-group'">グループ作成</button>
    <button onclick="window.location.href='/history'">履歴</button>
    <button onclick="window.location.href='/settings'">設定</button>

</body>
</html>

<script>
    document.getElementById('space-selector').addEventListener('click', function () {
        const menu = document.getElementById('space-menu');
        menu.style.display = (menu.style.display === 'none') ? 'block' : 'none';
    });

    document.getElementById('show-category-input')?.addEventListener('click', function () {
        document.getElementById('category-form').style.display = 'block';
        this.style.display = 'none'; // ボタン自体を隠す
    });
</script>



