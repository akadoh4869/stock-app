<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カテゴリ＋アイテム一括作成</title>
    <link rel="stylesheet" href="{{ asset('css/category/create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <a href="{{ route('stock.index') }}" class="back-link">← 戻る</a>
                    </div>
                    @if(isset($category))
                        <form action="{{ route('item.bulkStore') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                            <div class="group-input">
                                {{ $category->name }}
                            </div>
                    @else
                        <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="text" name="category_name" class="group-input" placeholder="例：スキンケア用品" required>
                    @endif
                </div>
            </div>

        </div>
        <div class="main">
            <div class="container">
                <div id="items-container">
                    @for ($i = 1; $i <= 3; $i++)
                        @php
                            $placeholders = [
                                1 => '例：化粧水',
                                2 => '例：乳液',
                                3 => '例：顔パック',
                                4 => '例：美容液',
                                5 => '例：日焼け止め',
                            ];
                        @endphp
                        <div class="item-box">
                            <div class="item-header">
                                <div class="item-number">{{ $i }}</div>
                                <input type="text" name="items[{{ $i }}][name]" placeholder="{{ $placeholders[$i] }}" required>
                            </div>
                            <div class="item-row">
                                <div class="row-picture">写真:<input type="file" name="items[{{ $i }}][image]" accept="image/*"></div>
                            </div>
                            <div class="item-row-row">
                                <div class="row-expiration">期限:<input type="date" name="items[{{ $i }}][expiration_date]" ></div>
                                <div class="row-date">購入日:<input type="date" name="items[{{ $i }}][purchase_date]" ></div>
                            </div>
                            @if ($currentType === 'group' && isset($currentGroup))
                                <div class="item-row-row">
                                    <div class="row-quantity">個数:<input type="number" name="items[{{ $i }}][quantity]" min="1" value="1"></div>
                                    <div class="row-owner">
                                        所有者:
                                        <select name="items[{{ $i }}][owner_id]" required>
                                            <option value="">共有</option>
                                            @foreach ($currentGroup->users as $member)
                                                <option value="{{ $member->id }}">{{ $member->user_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="item-row">
                                メモ:<br>
                                <input type="text" name="items[{{ $i }}][memo]">
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="buttons">
                    <button type="button" class="btn add-button" onclick="addItem()">
                        <i class="fa-solid fa-circle-plus"></i> 記入欄追加
                    </button>
                    <button type="submit" class="btn submit-button">ストック記入完了</button>
                </div>
                
                
            </form>
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

    <script>
        let itemIndex = 3;
        const currentType = "{{ $currentType }}";
        const groupUsers = @json($currentType === 'group' && isset($currentGroup) ? $currentGroup->users : []);
    
        function addItem() {
            itemIndex++;
            const container = document.getElementById('items-container');
    
            let ownerSelect = '';
            if (currentType === 'group' && groupUsers.length > 0) {
                ownerSelect += `
                    <div class="row-owner">
                        所有者:
                        <select name="items[${itemIndex}][owner_id]" required>
                            <option value="">共有</option>
                            ${groupUsers.map(user => `<option value="${user.id}">${user.user_name}</option>`).join('')}
                        </select>
                    </div>`;
            }
    
            const template = `
                <div class="item-box">
                    <div class="item-header">
                        <div class="item-number">${itemIndex}</div>
                        <input type="text" name="items[${itemIndex}][name]" placeholder="アイテム名" required>
                    </div>
                    <div class="item-row">
                        <div class="row-picture">写真:<input type="file" name="items[${itemIndex}][image]" accept="image/*"></div>
                    </div>
                    <div class="item-row-row">
                        <div class="row-expiration">期限:<input type="date" name="items[${itemIndex}][expiration_date]" ></div>
                        <div class="row-date">購入日:<input type="date" name="items[${itemIndex}][purchase_date]" ></div>
                    </div>
                    <div class="item-row-row">
                        <div class="row-quantity">個数:<input type="number" name="items[${itemIndex}][quantity]" min="1" value="1"></div>
                        ${ownerSelect}
                    </div>
                    <div class="item-row">
                        メモ:<br>
                        <input type="text" name="items[${itemIndex}][memo]">
                    </div>
                </div>
            `;
    
            container.insertAdjacentHTML('beforeend', template);
        }
    </script>
    
</body>
</html>
