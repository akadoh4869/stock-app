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
                        <input type="text" name="name" value="{{ $item->name }}" data-item-id="{{ $item->id }}" class="autosave-input" required style="width: 100%;">
                    </div>
                    <div class="item-row">
                        <label>期限日：</label>
                        <input type="date" name="expiration_date" value="{{ $item->expiration_date }}" data-item-id="{{ $item->id }}" class="autosave-input">
                    </div>
                    <div class="item-row">
                        <label>購入日：</label>
                        <input type="date" name="purchase_date" value="{{ $item->purchase_date }}" data-item-id="{{ $item->id }}" class="autosave-input">
                    </div>
                    @if($currentType === 'group')
                        <div class="item-row">
                            <label>所有者：</label>
                            <select name="owner_id" data-item-id="{{ $item->id }}" class="autosave-input">
                                <option value="">共有</option>
                                @foreach($currentGroup->users as $userOption)
                                    <option value="{{ $userOption->id }}" {{ $item->owner_id === $userOption->id ? 'selected' : '' }}>{{ $userOption->user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="item-row">
                        <label>個数：</label>
                        <input type="number" name="quantity" value="{{ $item->quantity }}" data-item-id="{{ $item->id }}" class="autosave-input" required>
                    </div>
                    <!-- 画像アップロード（ファイルがないときのみ表示） -->
                    @if ($item->images && $item->images->count())
                        <!-- 画像あり：表示＋変更フォーム -->
                        <div class="item-row" style="margin-top: 10px;">
                            <label>画像：</label>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="画像" style="width: 80px; height: auto; border-radius: 8px; cursor: pointer;">
                                <form method="POST" action="{{ route('item.image.upload', ['item' => $item->id]) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="image" accept="image/*" onchange="this.form.submit()">
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- 画像なし：アップロードフォームだけ表示 -->
                        <div class="item-row">
                            <label>画像：</label>
                            <form method="POST" action="{{ route('item.image.upload', ['item' => $item->id]) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="image" accept="image/*" onchange="this.form.submit()">
                            </form>
                        </div>
                    @endif
                 
                    <div class="item-row">
                        <label>メモ：</label>
                        <textarea name="description" rows="2" data-item-id="{{ $item->id }}" class="autosave-input">{{ $item->description }}</textarea>
                    </div>
                    <div style="text-align: right; margin-top: 10px;">
                        <button class="delete-item-button" data-item-id="{{ $item->id }}" style="background: none; border: none; color: #dc3545; font-size: 16px; cursor: pointer;" title="削除">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto;">
        @csrf
        <input type="hidden" name="category_id" value="{{ $category->id }}">
        <div id="item-form-container"></div>
        <div style="text-align: center; margin-top: 10px;">
            <button type="button" id="add-item-button" style="padding: 10px 15px; font-size: 16px;">
                <i class="fa fa-plus"></i> アイテムを追加
            </button>
        </div>
    </form>
    

    <div class="bottom-menu">
        <a href="/top">
            <div>🏠<br>ホーム</div>
        </a>
        <a href="{{ route('category.history.category', ['categoryId' => $category->id]) }}">
            🕒 履歴
        </a>
        
        <a href="/settings">
            <div>⚙️<br>設定</div>
        </a>
    </div>
    
    <!-- 🔽 拡大表示用モーダル -->
    <div id="image-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center; z-index: 9999;">
        <img id="modal-image" src="" style="max-width: 90%; max-height: 90%; border-radius: 10px;">
    </div>


    
</body>
</html>
<script>
     const currentType = "{{ $currentType }}";
    @if(isset($currentGroup))
        const members = @json($currentGroup->users->makeVisible(['user_name']));
    @else
        const members = [];
    @endif

    const draftItems = {};
    let itemIndex = 0;

    document.getElementById('add-item-button').addEventListener('click', function () {
        const container = document.getElementById('item-form-container');

        let ownerSelect = '';
        if (currentType === 'group' && members.length > 0) {
            ownerSelect += `<div style="margin-bottom: 10px;"><label>所有者：</label><select class="autosave-new" name="owner_id" data-index="${itemIndex}"><option value="">共有</option>`;
            members.forEach(member => {
                ownerSelect += `<option value="${member.id}">${member.user_name}</option>`;
            });
            ownerSelect += `</select></div>`;
        }

        const formGroup = document.createElement('div');
        formGroup.classList.add('item-form-box');
        formGroup.style = 'border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px;';
        formGroup.innerHTML = `
            <div style="margin-bottom: 10px;"><label>アイテム名：</label><input type="text" class="autosave-new" name="name" data-index="${itemIndex}" required></div>
            <div style="margin-bottom: 10px;"><label>画像：</label><input type="file" class="autosave-new-image" name="image" data-index="${itemIndex}" accept="image/*"></div>
            <div style="margin-bottom: 10px;"><label>期限日：</label><input type="date" class="autosave-new" name="expiration_date" data-index="${itemIndex}"></div>
            <div style="margin-bottom: 10px;"><label>購入日：</label><input type="date" class="autosave-new" name="purchase_date" data-index="${itemIndex}"></div>
            ${ownerSelect}
            <div style="margin-bottom: 10px;"><label>個数：</label><input type="number" class="autosave-new" name="quantity" data-index="${itemIndex}" min="1" value="1" required></div>
            <div style="margin-bottom: 10px;"><label>メモ：</label><textarea class="autosave-new" name="description" data-index="${itemIndex}" rows="2" style="width: 100%;"></textarea></div>
        `;
        container.appendChild(formGroup);
        itemIndex++;
    });

    document.addEventListener('change', function (event) {
        if (event.target.classList.contains('autosave-new-image')) {
            const index = event.target.dataset.index;
            const file = event.target.files[0];
            if (!draftItems[index]?.id || !file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/items/${draftItems[index].id}/image`, {
                method: 'POST',
                body: formData
            }).then(res => {
                if (!res.ok) throw new Error('画像の保存に失敗しました');
                return res.json();
            }).then(data => {
                console.log('画像アップロード成功');
            }).catch(err => {
                alert(err.message);
            });
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('autosave-new')) {
            const index = event.target.dataset.index;
            const field = event.target.name;
            const value = event.target.value;
            if (!draftItems[index]) draftItems[index] = {};
            draftItems[index][field] = value;
            draftItems[index]['category_id'] = "{{ $category->id }}";
            if (draftItems[index].id) {
                fetch(`/items/${draftItems[index].id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ [field]: value })
                });
            }
        }
    });

    document.addEventListener('blur', function (event) {
        if (event.target.classList.contains('autosave-new') && event.target.name === 'name') {
            const index = event.target.dataset.index;
            const name = event.target.value;
            if (!name || draftItems[index]?.id) return;

            if (!draftItems[index]) draftItems[index] = {};
            draftItems[index]['name'] = name;
            draftItems[index]['category_id'] = "{{ $category->id }}";

            // 画像ファイル取得
            const imageInput = document.querySelector(`input.autosave-new-image[data-index="${index}"]`);
            const imageFile = imageInput?.files?.[0];

            const formData = new FormData();
            formData.append('name', draftItems[index]['name']);
            formData.append('category_id', draftItems[index]['category_id']);
            if (draftItems[index]['expiration_date']) formData.append('expiration_date', draftItems[index]['expiration_date']);
            if (draftItems[index]['purchase_date']) formData.append('purchase_date', draftItems[index]['purchase_date']);
            if (draftItems[index]['owner_id']) formData.append('owner_id', draftItems[index]['owner_id']);
            if (draftItems[index]['quantity']) formData.append('quantity', draftItems[index]['quantity']);
            if (draftItems[index]['description']) formData.append('description', draftItems[index]['description']);
            if (imageFile) formData.append('image', imageFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`{{ route('item.store') }}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    draftItems[index].id = data.id;
                    console.log(`アイテム保存成功: ID=${data.id}`);
                }
            })
            .catch(error => {
                alert('保存失敗: ' + error.message);
            });
        }
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('image-modal');
        const modalImage = document.getElementById('modal-image');

        document.querySelectorAll('.item-card img').forEach(img => {
            img.addEventListener('click', () => {
                modalImage.src = img.src;
                modal.style.display = 'flex';
            });
        });

        modal.addEventListener('click', () => {
            modal.style.display = 'none';
            modalImage.src = '';
        });

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

        document.querySelectorAll('.image-upload-input').forEach(input => {
            input.addEventListener('change', function () {
                const itemId = this.dataset.itemId;
                const file = this.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                fetch(`/items/${itemId}/image`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('画像のアップロードに失敗しました');
                    return response.json();
                })
                .then(data => {
                    console.log('画像アップロード成功:', data);
                    location.reload();
                })
                .catch(error => {
                    alert(error.message);
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
                button.closest('.item-card').remove();
            })
            .catch(error => {
                alert('削除失敗: ' + error.message);
            });
        }
    });

</script>