<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除履歴</title>
    <link rel="stylesheet" href="{{ asset('css/category/history.css') }}">
    @include('partials.head')
    {{-- <script src="{{ asset('js/category/history.js') }}"></script> --}}
</head>
<body>
    {{-- {{ dd($deletedItems->toArray()) }} --}}
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <a href="{{ route('stock.index') }}" class="back-link">← 戻る</a>
                    </div>
                    <div class="history-title">履歴</div>
                </div>
            </div>

        </div>
        <div class="main">
            <!-- あなたのページコンテンツ -->
            <div class="content">
                <!-- 中身 -->
                    @if(session('success'))
                        <p style="color: green;">{{ session('success') }}</p>
                    @endif
                    @if(session('error'))
                        <p style="color: red;">{{ session('error') }}</p>
                    @endif
                    
                    @if(isset($filteredItemOnly) && $filteredItemOnly)
                        {{-- 特定カテゴリのアイテム履歴だけを表示する場合 --}}
                        @if($deletedItems->isEmpty())
                            <p>このカテゴリには削除されたアイテムがありません。</p>
                        @else
                            <ul>
                                @foreach($deletedItems as $item)
                                    <div class="category-wrapper" ontouchstart="handleTouchStart(event)" ontouchmove="handleTouchMove(event, this)" ontouchend="handleTouchEnd(event, this)">
                                        
                                        <!-- スライドする本体 -->
                                        <div class="swipe-content" onclick='openItemDetailModal(@json($item))'>
                                            {{-- <div class="circle"></div> <!-- ピンク丸（アイテムでも統一するなら） --> --}}
                                            <div class="category-texts">
                                                <div class="category-name">{{ $item->name }}</div>
                                                <div class="category-count">（{{ $item->quantity }}）</div>
                                            </div>
                                        </div>
                                
                                        <!-- ボタンエリア -->
                                        <div class="action-buttons">
                                            <form action="{{ route('item.restore', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="restore-btn">
                                                    {{-- <i class="fa-solid fa-rotate-left"></i> --}}
                                                    復元
                                                </button>
                                            </form>
                                
                                            <form action="{{ route('item.forceDelete', $item->id) }}" method="POST" onsubmit="return confirm('このアイテムを完全に削除しますか？復元できなくなります。');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn">
                                                    {{-- <i class="fa-solid fa-bomb"></i> --}}
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                
                                    </div>
                                @endforeach
                            </ul>
                            
                        @endif
                    @else
                        {{-- 通常のカテゴリ履歴表示 --}}
                        @if($deletedCategories->isEmpty())
                            <p>削除されたカテゴリはありません。</p>
                        @else
                            <ul>
                                @foreach($deletedCategories as $category)
                                    <div class="category-wrapper" ontouchstart="handleTouchStart(event)" ontouchmove="handleTouchMove(event, this)" ontouchend="handleTouchEnd(event, this)">
                                        <!-- スライドさせる本体 -->
                                        {{-- <div class="swipe-content"> --}}
                                        <div class="swipe-content category-item" onclick="toggleItemList(this)">
                                            <div class="circle"></div> <!-- ピンク丸 -->
                                            <div class="category-texts">
                                                <div class="category-name">{{ $category->name }}</div>
                                                <div class="category-count">（{{ $category->items->count() }}）</div>
                                            </div>
                                            
                                        </div>

                                        <!-- ここにドロップダウン表示するリストを隠して用意しておく -->
                                        <ul class="item-list">
                                            @foreach($category->items as $item)
                                                <li>{{ $item->name }}</li>
                                            @endforeach
                                        </ul>
                                    
                                        <!-- ボタンは swipe-content の外に置く -->
                                        <div class="action-buttons">
                                            @if($currentCategoryCount < 5)
                                            <form action="{{ route('category.restore', $category->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="restore-btn">復元</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('category.forceDelete', $category->id) }}" method="POST" >
                                                {{-- onsubmit="return confirm('このカテゴリを完全に削除しますか？復元できなくなります。');"> --}}
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn">削除</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach            
                            </ul>
                        @endif
                    @endif
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
                <i class="fa-solid fa-house"></i><br>ホーム
            </button>
            <button onclick="window.location.href='/history'">
                <i class="fa-solid fa-clock"></i><br>履歴
            </button>
            <button onclick="window.location.href='/settings'">
                <i class="fa-solid fa-gear"></i><br>設定
            </button>
        </div>
    </main>

    <div id="item-detail-modal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <button onclick="closeItemDetailModal()" class="modal-close">×</button>
    
            
            <div class="modal-header">
                <div class="modal-number" id="detail-number"></div>
                <div class="modal-name" id="detail-name"></div>
            </div>
    
            <div class="modal-info">
                <div class="modal-text">
                    <div><strong>期限日：</strong><span id="detail-expiration"></span></div>
                    <div><strong>購入日：</strong><span id="detail-purchase"></span></div>
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
    


    <script>
        function toggleItemList(element) {
            const allCategories = document.querySelectorAll('.category-item');
            allCategories.forEach(cat => {
                if (cat !== element) {
                    cat.classList.remove('active');
                    if (cat.nextElementSibling && cat.nextElementSibling.classList.contains('item-list')) {
                        cat.nextElementSibling.style.display = 'none';
                    }
                }
            });

            // アクティブ化
            element.classList.toggle('active');
            const list = element.nextElementSibling;
            if (list && list.classList.contains('item-list')) {
                list.style.display = list.style.display === 'block' ? 'none' : 'block';
            }
        }

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

        function openItemDetailModal(item) {
            const modal = document.getElementById('item-detail-modal');

            document.getElementById('detail-name').textContent = item.name ?? 'なし';
            document.getElementById('detail-expiration').textContent = item.expiration_date ?? 'なし';
            document.getElementById('detail-purchase').textContent = item.purchase_date ?? 'なし';
            document.getElementById('detail-quantity').textContent = item.quantity ?? '0';
            document.getElementById('detail-description').textContent = item.description ?? 'なし';

            const image = document.getElementById('detail-image');
            const noImage = document.getElementById('detail-no-image');

            if (item.image_url) {
                image.src = item.image_url;
                image.style.display = 'block';
                noImage.style.display = 'none';
            } else {
                image.style.display = 'none';
                noImage.style.display = 'flex';
            }

            modal.style.display = 'flex';
        }
        
        function closeItemDetailModal() {
            const modal = document.getElementById('item-detail-modal');
            modal.style.display = 'none';
        }




    </script>
        

    
</body>
</html>
