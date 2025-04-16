<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント設定ページ</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; text-align: center; }
        .setting-list { max-width: 430px; margin: 0 auto; padding: 30px 20px; }
        .setting-item { padding: 15px; margin-bottom: 15px; background: #f0f8ff; border-radius: 10px; cursor: pointer; font-size: 18px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 9999; }
        .overlay-content { background: #fff; padding: 20px; border-radius: 10px; max-width: 90%; max-height: 90%; overflow-y: auto; text-align: left; position: relative; }
        .overlay-content h3 { margin-top: 0; }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 20px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="setting-list">
        <div class="setting-item" onclick="openOverlay('account-overlay')">アカウント設定</div>
        <div class="setting-item" onclick="openOverlay('delete-overlay')">削除一覧</div>
        <div class="setting-item" onclick="openOverlay('option-overlay')">有料オプション</div>
        <div class="setting-item" onclick="openOverlay('logout-overlay')">ログアウト</div>
    </div>

    <!-- アカウント設定 -->
    <div id="account-overlay" class="overlay">
        <div class="overlay-content">
            <span class="close-btn" onclick="closeOverlay('account-overlay')">&times;</span>
            <h3>アカウント情報</h3>
            <p>ユーザーネーム: <strong>{{ Auth::user()->user_name }}</strong></p>
            <p>アカウント名: <strong>{{ Auth::user()->name }}</strong></p>
            <p>メールアドレス: <strong>{{ Auth::user()->email }}</strong></p>
        </div>
    </div>

    <!-- 削除一覧 -->
    <div id="delete-overlay" class="overlay">
        <div class="overlay-content">
            <span class="close-btn" onclick="closeOverlay('delete-overlay')">&times;</span>
            <h3>削除されたカテゴリとアイテム</h3>
    
            @foreach($deletedCategories->groupBy('inventory_id') as $inventoryId => $categories)
                @php
                    $inventory = \App\Models\Inventory::withTrashed()->find($inventoryId);
                @endphp
    
                @if($inventory)
                    <div class="space-block">
                        <h4 class="space-name">📦 {{ $inventory->name ?? '不明なスペース' }}</h4>
    
                        @foreach($categories as $category)
                            <div class="category-block" style="margin-left: 10px;">
                                <strong>📂 {{ $category->name }}</strong>
    
                                <ul style="margin-left: 15px; margin-top: 5px;">
                                    @foreach($deletedItems->where('category_id', $category->id) as $item)
                                        <li>📝 {{ $item->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    

    <!-- 有料オプション -->
    <div id="option-overlay" class="overlay">
        <div class="overlay-content">
            <span class="close-btn" onclick="closeOverlay('option-overlay')">&times;</span>
            <h3>有料オプション</h3>
            <p>・カテゴリ・グループ数の上限解除</p>
            <p>・広告の非表示</p>
            <p>・その他特典をご利用いただけます</p>
        </div>
    </div>

    <!-- ログアウト -->
    <div id="logout-overlay" class="overlay">
        <div class="overlay-content">
            <span class="close-btn" onclick="closeOverlay('logout-overlay')">&times;</span>
            <h3>ログアウトしますか？</h3>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        </div>
    </div>

    <script>
        function openOverlay(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeOverlay(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>
</html>