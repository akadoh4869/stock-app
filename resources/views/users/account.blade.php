<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント設定ページ</title>
    <link rel="stylesheet" href="{{ asset('css/users/account.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&display=swap" rel="stylesheet">
    {{-- <script src="{{ asset('js/setting.js') }}"></script> --}}
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <div class="app-name">ストログ</div>
                    </div>
                </div>
            </div>
    
        </div>
        <div class="main">
            <div class="setting-list">
                <div class="setting-item" onclick="openOverlay('account-overlay')">
                    <i class="fa-solid fa-user" style="color:#ff66cc;"></i>
                    <div class="setting-label">アカウント設定</div>
                </div>
                {{-- <div class="setting-item" onclick="openOverlay('delete-overlay')">
                    <i class="fa-solid fa-trash" style="color:#5ce0f0;"></i>
                    <div class="setting-label">削除一覧</div>
                </div> --}}
                <div class="setting-item" onclick="openOverlay('option-overlay')">
                    <i class="fa-solid fa-star" style="color:#5ce0f0;"></i>
                    <div class="setting-label">有料オプション</div>
                </div>
                <div class="setting-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-sign-out-alt" style="color:#ff66cc;"></i>
                    <div class="setting-label">ログアウト</div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            
            <!-- アカウント設定 -->
            <div id="account-overlay" class="overlay">
                <div class="overlay-content">
                    <span class="close-btn" onclick="closeOverlay('account-overlay')">&times;</span>
                    <h3>アカウント情報</h3>
                    <p>ユーザーネーム: <strong>{{ Auth::user()->user_name }}</strong></p>
                    <p>アカウント名: <strong>{{ Auth::user()->name }}</strong></p>
                    <p>メールアドレス: <strong>{{ Auth::user()->email }}</strong></p>
                    <p>会員ステータス: 
                        <strong>
                            @if(Auth::user()->subscription_status)
                                有料会員（買い切り）
                            @else
                                無料会員
                            @endif
                        </strong>
                    </p>
                    
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
    
        </div>
        <!-- フッター背景画像（画面最下部に固定） -->
        <div class="line-bottom-fixed">
            <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
        </div>

        <!-- フッターのボタン群（画像の上に表示） -->
        <div class="footer-overlay-fixed">
            
            <button onclick="window.location.href='/top'">
                <i class="fa-solid fa-house" style="color:#5ce0f0;"></i><br>ホーム
            </button>
            <button onclick="window.location.href='/history'">
                <i class="fa-solid fa-clock"></i><br>履歴
            </button>
            <button onclick="window.location.href='/settings'">
                <i class="fa-solid fa-gear"></i><br>設定
            </button>
        </div>

    </main>
    
    

    
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