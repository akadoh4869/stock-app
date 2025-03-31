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
    <h2 style="text-align:center; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;">
        <!-- 戻るボタン -->
        <a href="/top" style="margin-right: 10px;">←</a>

    
        {{ $category->name }} のアイテム一覧
    </h2>
    
    @if($items->isEmpty())
        {{-- <p style="text-align: center;">このカテゴリにはアイテムがありません。</p> --}}
    @else
        <div style="max-width: 500px; margin: 0 auto;">
            @foreach($items as $index => $item)
                <div class="item-card" style="margin-bottom: 20px;">
                    <div class="item-number">{{ $index + 1 }}</div>
            
                    <div class="item-header">
                        <input type="text" 
                            name="name" 
                            value="{{ $item->name }}" 
                            data-item-id="{{ $item->id }}" 
                            class="autosave-input" 
                            required 
                            style="width: 100%;">
                    </div>
            
                    <div class="item-row">
                        <label>期限日：</label>
                        <input type="date" 
                            name="expiration_date" 
                            value="{{ $item->expiration_date }}" 
                            data-item-id="{{ $item->id }}" 
                            class="autosave-input">
                    </div>
            
                    <div class="item-row">
                        <label>購入日：</label>
                        <input type="date" 
                            name="purchase_date" 
                            value="{{ $item->purchase_date }}" 
                            data-item-id="{{ $item->id }}" 
                            class="autosave-input">
                    </div>
            
                    @if($currentType === 'group')
                        <div class="item-row">
                            <label>所有者：</label>
                            <span>{{ $item->owner?->user_name ?? '共有' }}</span>
                        </div>
                    @endif
            
                    <div class="item-row">
                        <label>個数：</label>
                        <input type="number" 
                            name="quantity" 
                            value="{{ $item->quantity }}" 
                            data-item-id="{{ $item->id }}" 
                            class="autosave-input" 
                            required>
                    </div>
            
                    <div class="item-row">
                        <label>メモ：</label>
                        <textarea name="description" 
                                rows="2" 
                                data-item-id="{{ $item->id }}" 
                                class="autosave-input">{{ $item->description }}</textarea>
                    </div>
                    <!-- 🔽 削除ボタンを右下に表示 -->
                    <div style="text-align: right; margin-top: 10px;">
                        <button class="delete-item-button" 
                                data-item-id="{{ $item->id }}" 
                                style="background: none; border: none; color: #dc3545; font-size: 16px; cursor: pointer;"
                                title="削除">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <!-- プラスマークのアイテム追加フォーム -->
    {{-- <h3 style="text-align: center; margin-top: 30px;">アイテムを追加</h3> --}}
    <form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto;">
        @csrf
        <input type="hidden" name="category_id" value="{{ $category->id }}">

        <div id="item-form-container">
            <!-- 追加されるフォーム欄 -->
        </div>

        <div style="text-align: center; margin-top: 10px;">
            <button type="button" id="add-item-button" style="padding: 10px 15px; font-size: 16px;">
                <i class="fa fa-plus"></i> アイテムを追加
            </button>
        </div>
    </form>

    
</body>
</html>
<script>


    const currentType = "{{ $currentType }}";
    @if(isset($currentGroup))
        const members = @json($currentGroup->users->makeVisible(['user_name']));
    @else
        const members = [];
    @endif

    const draftItems = {}; // 一時的に未保存のフォーム内容を保持
    let itemIndex = 0;

    document.getElementById('add-item-button').addEventListener('click', function () {
        const container = document.getElementById('item-form-container');

        let ownerSelect = '';
        if (currentType === 'group' && members.length > 0) {
            ownerSelect += `
            <div style="margin-bottom: 10px;">
                <label>所有者：</label>
                <select class="autosave-new" name="owner_id" data-index="${itemIndex}">
                    <option value="">共有</option>`;
            members.forEach(member => {
                ownerSelect += `<option value="${member.id}">${member.user_name}</option>`;
            });
            ownerSelect += `</select>
            </div>`;
        }

        const formGroup = document.createElement('div');
        formGroup.classList.add('item-form-box');
        formGroup.style.border = '1px solid #ccc';
        formGroup.style.padding = '15px';
        formGroup.style.marginBottom = '15px';
        formGroup.style.borderRadius = '8px';

        formGroup.innerHTML = `
            <div style="margin-bottom: 10px;">
                <label>アイテム名：</label>
                <input type="text" class="autosave-new" name="name" data-index="${itemIndex}" required>
            </div>

            <div style="margin-bottom: 10px;">
                <label>写真：</label>
                <input type="file" name="photo" disabled>
            </div>

            <div style="margin-bottom: 10px;">
                <label>期限日：</label>
                <input type="date" class="autosave-new" name="expiration_date" data-index="${itemIndex}">
            </div>

            <div style="margin-bottom: 10px;">
                <label>購入日：</label>
                <input type="date" class="autosave-new" name="purchase_date" data-index="${itemIndex}">
            </div>

            ${ownerSelect}

            <div style="margin-bottom: 10px;">
                <label>個数：</label>
                <input type="number" class="autosave-new" name="quantity" data-index="${itemIndex}" min="1" value="1" required>
            </div>

            <div style="margin-bottom: 10px;">
                <label>メモ：</label>
                <textarea class="autosave-new" name="description" data-index="${itemIndex}" rows="2" style="width: 100%;"></textarea>
            </div>
        `;

        container.appendChild(formGroup);
        itemIndex++;
    });

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('autosave-new')) {
            const index = event.target.dataset.index;
            const field = event.target.name;
            const value = event.target.value;

            if (!draftItems[index]) {
                draftItems[index] = {};
            }

            draftItems[index][field] = value;
            draftItems[index]['category_id'] = "{{ $category->id }}"; // カテゴリIDも送る

            // 既にIDが付与されたらPUTに切り替える
            if (draftItems[index].id) {
                fetch(`/items/${draftItems[index].id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ [field]: value })
                });
            } else {
                // 新規作成（POST）
                fetch(`{{ route('item.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(draftItems[index])
                })
                .then(response => response.json())
                .then(data => {
                    // IDが返ってきたら保存して、次回からPUTに切り替える
                    if (data.id) {
                        draftItems[index].id = data.id;
                        console.log(`新規作成成功: ID=${data.id}`);
                    }
                });
            }
        }
    });

    

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.autosave-input').forEach(input => {
            input.addEventListener('change', function () {
                const itemId = this.dataset.itemId;
                const field = this.name;
                const value = this.value;

                fetch(`/items/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ [field]: value })
                })
                .then(response => {
                    if (!response.ok) throw new Error('保存に失敗しました');
                    console.log('自動保存成功:', field);
                })
                .catch(error => {
                    alert('保存失敗: ' + error.message);
                });
            });
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('.delete-item-button')) {
            const button = event.target.closest('.delete-item-button');
            const itemId = button.dataset.itemId;

            if (!confirm('このアイテムを削除しますか？')) return;

            fetch(`/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('削除に失敗しました');
                // アイテムカードを非表示にする
                button.closest('.item-card').remove();
            })
            .catch(error => {
                alert('削除失敗: ' + error.message);
            });
        }
    });


</script>

