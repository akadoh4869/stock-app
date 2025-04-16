<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/category/items.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>アイテムページ</title>
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
        
                <div class="header-inner">
                    <!-- 戻るボタン -->
                    <a href="/top" class="back-button">←</a>
        
                    <!-- 中央タイトル -->
                    <h2 class="page-title">{{ $category->name }} のストック</h2>
        
                    <!-- ハンバーガー -->
                    <div class="hamburger-menu">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="main">
            @if($items->isEmpty())
                <div class="bulk-buttons" style="text-align: center; margin-top: 20px;">
                    {{-- 一括追加ボタン --}}
                    <a href="{{ route('category.bulkCreate', ['category_id' => $category->id]) }}" class="blue-button" style="text-align:center; margin-top: 20px;">
                        <i class="fa fa-plus"></i>ストック一括追加
                    </a>
                    {{-- 通常のストック追加 --}}
                    <button class="pink-button" id="add-item-button-bottom">
                        <i class="fa fa-plus"></i> ストック追加
                    </button>
                </div>
            @else
                <div style="max-width: 400px; margin: 0 auto;">
                    @foreach($items as $index => $item)
                        <div class="item-card" data-item='@json($item)' data-number="{{ $index + 1 }}">

                            <div class="item-header-flex">
                                <div class="item-number">{{ $index + 1 }}</div>
                                <div class="item-details">
                                    <div class="item-name">{{ $item->name }}</div>
                                    <div class="item-info">
                                        @if (!empty($item->expiration_date))
                                            <div class="item-expiration">
                                                <label>期限：</label><span>{{ $item->expiration_date }}</span>
                                            </div>
                                        @endif
                                        <div class="item-quantity">
                                            <label>個数：</label><span>{{ $item->quantity }}</span>
                                        </div>
                                        @if (!empty($item->purchase_date))
                                            <div class="item-purchase">
                                                <label>購入日：</label><span>{{ $item->purchase_date }}</span>
                                            </div>
                                        @endif

                                        @if (!empty($item->description))
                                            <div class="item-description">
                                                <label>メモ：</label><span style="white-space: pre-wrap;">{{ $item->description }}</span>
                                            </div>
                                        @endif
                                        
                                        
                                    </div>
                                </div>
                        
                                <div class="item-image">
                                    @if ($item->images && $item->images->count())
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="画像">
                                    @else
                                        <div class="item-image-placeholder">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        
                            <div class="item-actions">
                                <button class="edit-item-button" data-item='@json($item)' title="編集">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="delete-item-button" data-item-id="{{ $item->id }}" title="削除">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    
                    
                    @endforeach
                </div>
                {{-- 一覧下に表示するボタン（デフォルト表示） --}}
                <div id="bottom-add-button" class="add-button-bottom" style="text-align: center; margin-top: 20px;">
                    <button class="pink-button" id="add-item-button-bottom">
                        <i class="fa fa-plus"></i> ストック追加
                    </button>
                </div>
            @endif
        
            <form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto;">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <div id="item-form-container"></div>
                <div style="text-align: center; margin-top: 10px;"></div>
            </form>
            
        </div>
        
        <!-- フッター背景画像（画面最下部に固定） -->
        <div class="line-bottom-fixed">
            <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
        </div>

        <!-- フッターのメニュー＆追加ボタン（画像の上に固定） -->
        <div class="footer-overlay-fixed footer-stock">
            <div class="footer-buttons">
                <a href="/top" class="footer-button">
                    <i class="fa-solid fa-house"></i><br>ホーム
                </a>
                <a href="{{ route('category.history.category', ['categoryId' => $category->id]) }}" class="footer-button">
                    <i class="fa-solid fa-clock"></i><br>履歴
                </a>
                <a href="/settings" class="footer-button">
                    <i class="fa-solid fa-gear"></i><br>設定
                </a>
            </div>

            {{-- 右下に固定するボタン（5件以上で表示） --}}
            <button class="add-stock-button" id="add-item-button-fixed" style="display: none;">
                <i class="fa fa-plus"></i>
            </button>
              
        </div>

        <div id="item-overlay" class="overlay" style="display: none;">
            <div class="overlay-content">
                <div id="overlay-body"></div>
                <button onclick="closeOverlay()" style="position: absolute; top: 10px; right: 10px; font-size: 20px;">✕</button>
            </div>
        </div>

        <!-- 詳細モーダル -->
        <div id="item-detail-modal" class="modal-overlay" style="display: none;">
            <div class="modal-card">
                <button class="modal-close" onclick="closeDetailModal()">✕</button>
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-number" id="detail-number"></div>
                        <div class="modal-name" id="detail-name"></div>
                    </div>

                    <div class="modal-info">    
                        <div class="modal-text">
                            <div><strong>期限日：</strong><span id="detail-expiration"></span></div>
                            <div><strong>購入日：</strong><span id="detail-purchase"></span></div>
                            <div id="detail-owner-wrapper" style="display: none;"><strong>所有者：</strong><span id="detail-owner"></span></div>
                            <div><strong>個数：</strong><span id="detail-quantity"></span></div>
                            <div class="memo-block">
                                <strong>メモ：</strong>
                                <div id="detail-description" class="memo-lines"></div>
                            </div>
                            
                              
                        </div>
                        <div class="modal-image-wrapper">
                            <img id="detail-image" src="" alt="画像" style="display: none;">
                            <div id="detail-no-image" class="modal-image-placeholder"><i class="fas fa-camera"></i></div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- 🔽 拡大表示用モーダル -->
        <div id="image-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center; z-index: 9999; position: fixed;">
            <!-- ✕ボタン -->
            <button id="image-modal-close" style="position: absolute; top: 15px; right: 20px; font-size: 24px; color: white; background: none; border: none; cursor: pointer;">
                ✕
            </button>
            <img id="modal-image" src="" style="max-width: 90%; max-height: 90%; border-radius: 10px;">
        </div>
        <!-- ✅ ここに追加 -->
        <!-- アイテム追加オーバーレイ -->
        <div id="add-form-overlay" class="modal-overlay" style="display: none;">
            <div class="modal-card" style="max-width: 500px;">
                <button class="modal-close" onclick="closeAddForm()">✕</button>
                <div id="add-form-body"></div>
            </div>
        </div>

        <!-- 検索オーバーレイ -->
        <div id="search-overlay" class="search-overlay">
            <div class="search-header">
                <input type="text" id="search-input"
                    placeholder="{{ $currentType === 'group' ? 'キーワードまたは所有者名を検索する' : 'キーワードを検索する' }}">
                <button onclick="closeSearchOverlay()" class="close-search-button">✕</button>
            </div>
            <div id="search-results" class="search-results">
                <!-- 検索結果がここに表示されます -->
            </div>
        </div>


    </main>
   
    <script>
        window.currentType = "{{ $currentType }}";
        window.members = @json($currentGroup?->users->makeVisible(['user_name']) ?? []);
        window.categoryId = {{ $category->id }};
        window.itemStoreUrl = "{{ route('item.store') }}";
        window.items = @json($itemsForJs); // ← ✅ JS専用に整形された配列
    </script>
    
    
    <script src="{{ asset('js/category/item.js') }}"></script>

    
</body>
</html>