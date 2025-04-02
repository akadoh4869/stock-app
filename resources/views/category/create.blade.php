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
    <div style="margin: 10px;">
        <a href="{{ route('stock.index') }}" style="text-decoration: none; font-size: 18px;">← 戻る</a>
    </div>
    <div class="container">
        <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="category_name" class="group-input" placeholder="例：スキンケア用品" required>

            <div id="items-container">
                @for ($i = 1; $i <= 5; $i++)
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
                        <div class="item-number">{{ $i }}</div>
                        <input type="text" name="items[{{ $i }}][name]" placeholder="{{ $placeholders[$i] }}" required>
                        <div class="item-row">
                            写真 <input type="file" name="items[{{ $i }}][image]" accept="image/*">
                        </div>
                        <div class="item-row">
                            <label><input type="checkbox" name="items[{{ $i }}][has_expiration]"> 期限</label>
                            <input type="date" name="items[{{ $i }}][expiration_date]">
                        </div>
                        <div class="item-row">
                            <label><input type="checkbox" name="items[{{ $i }}][has_purchase]"> 購入日</label>
                            <input type="date" name="items[{{ $i }}][purchase_date]">
                        </div>
                        @if ($currentType === 'group' && isset($currentGroup))
                            <div class="item-row">
                                所有者
                                <select name="items[{{ $i }}][owner_id]" required>
                                    <option value="">共有</option>
                                    @foreach ($currentGroup->users as $member)
                                        <option value="{{ $member->id }}">{{ $member->user_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="item-row">
                            個数 <input type="number" name="items[{{ $i }}][quantity]" min="1" value="1">
                        </div>
                        <div class="item-row">
                            メモ <input type="text" name="items[{{ $i }}][memo]">
                        </div>
                    </div>
                @endfor
            </div>

            <button type="button" class="btn add-button" onclick="addItem()">＋ 記入欄追加</button>
            <br>
            <button type="submit" class="btn">ストック記入完了</button>
        </form>

        <div class="bottom-menu">
            <a href="/top"><div>🏠<br>ホーム</div></a>
            <a href="/history"><div>🕒<br>履歴</div></a>
            <a href="/settings"><div>⚙️<br>設定</div></a>
        </div>
    </div>

    <script>
        let itemIndex = 5;
        const currentType = "{{ $currentType }}";
        const groupUsers = @json($currentType === 'group' && isset($currentGroup) ? $currentGroup->users : []);

        function addItem() {
            itemIndex++;
            const container = document.getElementById('items-container');

            let ownerSelect = '';
            if (currentType === 'group' && groupUsers.length > 0) {
                ownerSelect += `<div class="item-row">
                    所有者
                    <select name="items[${itemIndex}][owner_id]" required>
                        <option value="">選択してください</option>`;
                groupUsers.forEach(user => {
                    ownerSelect += `<option value="${user.id}">${user.user_name}</option>`;
                });
                ownerSelect += `</select></div>`;
            }

            const template = `
            <div class="item-box">
                <div class="item-number">${itemIndex}</div>
                <input type="text" name="items[${itemIndex}][name]" placeholder="アイテム名" required>
                <div class="item-row">
                    写真 <input type="file" name="items[${itemIndex}][image]" accept="image/*">
                </div>
                <div class="item-row">
                    <label><input type="checkbox" name="items[${itemIndex}][has_expiration]"> 期限</label>
                    <input type="date" name="items[${itemIndex}][expiration_date]">
                </div>
                <div class="item-row">
                    <label><input type="checkbox" name="items[${itemIndex}][has_purchase]"> 購入日</label>
                    <input type="date" name="items[${itemIndex}][purchase_date]">
                </div>
                ${ownerSelect}
                <div class="item-row">
                    個数 <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1">
                </div>
                <div class="item-row">
                    メモ <input type="text" name="items[${itemIndex}][memo]">
                </div>
            </div>
            `;

            container.insertAdjacentHTML('beforeend', template);
        }
    </script>
</body>
</html>
