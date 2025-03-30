<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/category/items.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>アイテムページ</title>

</head>
<body>
    <h2 style="text-align:center; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;">
        <!-- 戻るボタン -->
        <a href="/top" style="margin-right: 10px;">←</a>

    
        {{ $category->name }} のアイテム一覧
    </h2>
    

    @if($items->isEmpty())
        <p style="text-align: center;">このカテゴリにはアイテムがありません。</p>
        <!-- プラスマークの追加ボタン -->
        {{-- <a href="{{ route('item.create', ['category_id' => $category->id]) }}"
            style="display: inline-block; margin-top: 10px; padding: 12px 20px; font-size: 18px; background-color: #f88; color: white; border-radius: 30px; text-decoration: none;">
             <i class="fa fa-plus"></i> アイテムを追加
         </a> --}}
    @else
        <div style="max-width: 500px; margin: 0 auto;">
            @foreach($items as $index => $item)
                <div class="item-card">
                    <div class="item-number">{{ $index + 1 }}</div>
                    <button class="photo-btn" title="写真を追加">
                        <i class="fa fa-camera"></i>
                    </button>
                    <div class="item-header">{{ $item->name }}</div>
    
                    <div class="item-row">
                        <label>期限日：</label>
                        <span>{{ $item->expiration_date ?? '未設定' }}</span>
                    </div>
    
                    <div class="item-row">
                        <label>購入日：</label>
                        <span>{{ $item->purchase_date ?? '未設定' }}</span>
                    </div>

                    @if($currentType === 'group')
                        <div class="item-row">
                            <label>所有者：</label>
                            <span>
                                {{ $item->owner?->user_name ?? '共有' }}
                            </span>
                        </div>
                    @endif
    
                    <div class="item-row">
                        <label>個数：</label>
                        <span>{{ $item->quantity }}</span>
                    </div>
    
                    <div class="item-row">
                        <label>メモ：</label>
                        <span>{{ $item->description ?? 'なし' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    
</body>
</html>

