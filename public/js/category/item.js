document.addEventListener('DOMContentLoaded', function () {
    const draftItems = {};
    let itemIndex = 0;
    let updatedItem = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const addButtons = [
        document.getElementById('add-item-button-bottom'),
        document.getElementById('add-item-button-fixed')
    ];
    
    addButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', createItemForm);
        }
    });

    function createItemForm() {
        const overlay = document.getElementById('add-form-overlay');
        const body = document.getElementById('add-form-body');

        const nextNumber = document.querySelectorAll('.item-card').length + 1;
    
        let ownerSelect = '';
        if (window.currentType === 'group' && window.members.length > 0) {
            ownerSelect += `<div class="modal-edit-row"><label>所有者：</label>
                <select name="owner_id" class="styled-input">
                    <option value="">共有</option>`;
            window.members.forEach(member => {
                ownerSelect += `<option value="${member.id}">${member.user_name}</option>`;
            });
            ownerSelect += `</select></div>`;
        }
    
        body.innerHTML = `
            <div class="edit-form-content">
                <div class="edit-form-left">
                    <div class="modal-edit-number-row">
                        <div class="edit-number-circle">${nextNumber}</div>
                        <input type="text" name="name" placeholder="アイテム名" class="styled-input" required>
                    </div>
                    <div class="modal-edit-row"><label>期限：</label><input type="date" name="expiration_date" class="styled-input"></div>
                    <div class="modal-edit-row"><label>購入日：</label><input type="date" name="purchase_date" class="styled-input"></div>
                    ${ownerSelect}
                    <div class="modal-edit-row"><label>個数：</label><input type="number" name="quantity" value="1" min="1" class="styled-input" required></div>
                    <div class="modal-edit-row"><label>メモ：</label><textarea name="description" rows="3" class="styled-input"></textarea></div>
                </div>
                <div class="edit-form-right">
                    <label for="new-item-image" class="image-preview-wrapper clickable-image">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="new-item-image" name="image" accept="image/*" class="hidden-input">
                </div>
            </div>
            <div class="modal-button-wrapper">
                <button type="button" id="submit-overlay-item" class="confirm-button stock-add-button">
                    ＋ ストック追加
                </button>
            </div>
        `;
    
        overlay.style.display = 'flex';
    
        // メモ自動リサイズ
        const textarea = body.querySelector('textarea[name="description"]');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    }
    

    // フォーム削除
    document.addEventListener('click', event => {
        if (event.target.classList.contains('close-form-button')) {
            event.target.closest('.item-form-box')?.remove();
        }
    });

    // アイテム追加
    document.addEventListener('click', event => {
        if (event.target.id === 'submit-overlay-item') {
            const formBox = document.getElementById('add-form-body');
            const name = formBox.querySelector('input[name="name"]').value.trim();
            const quantity = formBox.querySelector('input[name="quantity"]').value;
            if (!name || !quantity) {
                alert('名前と個数は必須です');
                return;
            }
    
            const formData = new FormData();
            formData.append('name', name);
            formData.append('quantity', quantity);
            formData.append('category_id', window.categoryId);
    
            ['expiration_date', 'purchase_date', 'owner_id', 'description'].forEach(field => {
                const el = formBox.querySelector(`[name="${field}"]`);
                if (el?.value) formData.append(field, el.value);
            });
    
            const imageInput = formBox.querySelector('input[name="image"]');
            if (imageInput?.files[0]) {
                formData.append('image', imageInput.files[0]);
            }
    
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
            fetch(window.itemStoreUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('サーバーエラー');
                return res.json();
            })
            .then(() => {
                location.reload();
            })
            .catch(err => alert('保存失敗: ' + err.message));
        }
    });
   

    // オーバーレイを開く
    window.openOverlay = function (item, number = '?') {
    updatedItem = structuredClone(item);
    const overlay = document.getElementById('item-overlay');
    const body = document.getElementById('overlay-body');

    let ownerSelect = '';
    if (window.currentType === 'group' && window.members.length > 0) {
        ownerSelect += `<select name="owner_id" data-item-id="${item.id}" class="autosave-input styled-input"><option value="">共有</option>`;
        window.members.forEach(user => {
            ownerSelect += `<option value="${user.id}" ${user.id === item.owner_id ? 'selected' : ''}>${user.user_name}</option>`;
        });
        ownerSelect += `</select>`;
    }

    body.innerHTML = `
        <div class="edit-form-content">
        <div class="edit-form-left">
           <div class="modal-edit-number-row">
                <div class="edit-number-circle">${number}</div>
                <input type="text" name="name" value="${item.name}" class="autosave-input styled-input name-input" data-item-id="${item.id}">
            </div>
            <div class="modal-edit-row"><label>期限：</label><input type="date" name="expiration_date" value="${item.expiration_date}" class="autosave-input styled-input" data-item-id="${item.id}"></div>
            <div class="modal-edit-row"><label>購入日：</label><input type="date" name="purchase_date" value="${item.purchase_date}" class="autosave-input styled-input" data-item-id="${item.id}"></div>
            <div class="modal-edit-row"><label>個数：</label><input type="number" name="quantity" value="${item.quantity}" class="autosave-input styled-input" data-item-id="${item.id}"></div>
            <div class="modal-edit-row"><label>メモ：</label><textarea name="description" class="autosave-input styled-input" rows="4" data-item-id="${item.id}">${item.description ?? ''}</textarea></div>
        </div>
        <div class="edit-form-right">
            <label for="edit-image-input-${item.id}" class="image-preview-wrapper clickable-image">
            <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="edit-image-input-${item.id}" name="image" accept="image/*"
            class="image-upload-input styled-input hidden-input" data-item-id="${item.id}">
        </div>
    
    `;

    // 編集フォームを開いたあとに実行
    const textarea = body.querySelector('textarea[name="description"]');
    if (textarea) {
        textarea.style.height = 'auto'; // 初期化
        textarea.style.height = textarea.scrollHeight + 'px'; // 高さ自動調整

        // 入力中も動的に変化させる
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }


  

    overlay.style.display = 'flex';
};


    // オーバーレイを閉じる
    window.closeOverlay = function () {
        // オーバーレイを非表示にする（見た目だけ先に閉じる）
        const overlay = document.getElementById('item-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    
        // 編集中の内容を updatedItem に反映（省略してもよいが保険として残す）
        if (updatedItem) {
            updatedItem.name = document.querySelector('input[name="name"]').value;
            updatedItem.quantity = document.querySelector('input[name="quantity"]').value;
            updatedItem.expiration_date = document.querySelector('input[name="expiration_date"]').value;
            updatedItem.purchase_date = document.querySelector('input[name="purchase_date"]').value;
            updatedItem.description = document.querySelector('textarea[name="description"]').value;
            updatedItem.owner_id = document.querySelector('select[name="owner_id"]')?.value ?? '';
        }
    
        updatedItem = null;
    
        // ✅ ページをリロードして最新の状態に更新（編集結果を確実に反映）
        location.reload();
    };

    // 自動保存
    document.addEventListener('change', event => {
        const target = event.target;
        if (target.classList.contains('autosave-input')) {
            const itemId = target.dataset.itemId;
            const field = target.name;
            const value = target.value;

            fetch(`/items/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ [field]: value })
            })
            .then(response => {
                if (!response.ok) throw new Error('保存に失敗しました');
                
                // updatedItemにも反映
                if (updatedItem && updatedItem.id == itemId) {
                    updatedItem[field] = value;
                }
            
                // DOM側も更新
                const card = document.querySelector(`.item-card[data-id="${itemId}"]`);
                if (card) {
                    if (field === 'name') {
                        card.querySelector('.item-name').textContent = value;
                    }
                    if (field === 'expiration_date') {
                        const expirationSpan = card.querySelector('.item-expiration span');
                        if (expirationSpan) {
                            expirationSpan.textContent = value;
                        } else if (value) {
                            const infoArea = card.querySelector('.item-info');
                            const newExp = document.createElement('div');
                            newExp.classList.add('item-expiration');
                            newExp.innerHTML = `<label>期限：</label><span>${value}</span>`;
                            infoArea.prepend(newExp);
                        }
                    }
                    if (field === 'quantity') {
                        card.querySelector('.item-quantity span').textContent = value;
                    }

                    if (field === 'purchase_date') {
                        const purchaseSpan = card.querySelector('.item-purchase span');
                        if (purchaseSpan) {
                            purchaseSpan.textContent = value;
                        } else if (value) {
                            const infoArea = card.querySelector('.item-info');
                            const newPurchase = document.createElement('div');
                            newPurchase.classList.add('item-purchase');
                            newPurchase.innerHTML = `<label>購入日：</label><span>${value}</span>`;
                            infoArea.appendChild(newPurchase);
                        }
                    }

                    if (field === 'description') {
                        const descSpan = card.querySelector('.item-description span');
                        if (descSpan) {
                            descSpan.textContent = value;
                        } else if (value) {
                            const infoArea = card.querySelector('.item-info');
                            const newDesc = document.createElement('div');
                            newDesc.classList.add('item-description');
                            newDesc.innerHTML = `<label>メモ：</label><span style="white-space: pre-wrap;">${value}</span>`;
                            infoArea.appendChild(newDesc);
                        }
                    }

                    if (field === 'owner_id') {
                        const ownerName = window.members.find(m => m.id == value)?.user_name ?? '共有';
                        let ownerWrapper = card.querySelector('.item-owner');
                        if (ownerWrapper) {
                            ownerWrapper.textContent = ownerName;
                        } else {
                            const infoArea = card.querySelector('.item-info');
                            const newOwner = document.createElement('div');
                            newOwner.classList.add('item-owner');
                            newOwner.innerHTML = `<label>所有者：</label><span>${ownerName}</span>`;
                            infoArea.appendChild(newOwner);
                        }
                    }
                }

            })
            
        }
    });

    // 画像アップロード
    document.addEventListener('change', event => {
        if (event.target.classList.contains('image-upload-input')) {
            const itemId = event.target.dataset.itemId;
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', csrfToken);

            fetch(`/items/${itemId}/image`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('画像アップロード失敗');
                return response.json();
            })
            .then(() => location.reload())
            .catch(error => alert(error.message));
        }
    });

    // アイテム削除
    document.addEventListener('click', event => {
        if (event.target.closest('.delete-item-button')) {
            const button = event.target.closest('.delete-item-button');
            const itemId = button.dataset.itemId;
            // if (!confirm('このアイテムを削除しますか？')) return;

            fetch(`/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('削除に失敗しました');
                location.reload(); // ✅ 削除後にページをリロード
                // button.closest('.item-card').remove();
            })
            .catch(error => alert(error.message));
        }
    });

    const detailImage = document.getElementById('detail-image');
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    const modalClose = document.getElementById('image-modal-close');
    
    if (detailImage && modal && modalImage && modalClose) {
        detailImage.addEventListener('click', function () {
            const src = detailImage.getAttribute('src');
            if (src) {
                modalImage.src = src;
                modal.style.display = 'flex';
            }
        });
    
        // ✕ボタンで閉じる
        modalClose.addEventListener('click', function () {
            modal.style.display = 'none';
            modalImage.src = '';
        });
    
        // 背景クリックで閉じる（画像自体のクリックでは閉じない）
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                modalImage.src = '';
            }
        });
    }
    

    // こちらは表示切り替え用の処理
    const itemCards = document.querySelectorAll('.item-card');
    const addButtonBottom = document.getElementById('add-item-button-bottom');
    const addButtonFixed = document.getElementById('add-item-button-fixed');

    const toggleAddButton = () => {
        if (itemCards.length >= 5) {
            addButtonBottom?.parentElement?.remove(); // 一覧下ボタン非表示
            addButtonFixed.style.display = 'block';   // 固定ボタン表示
        } else {
            addButtonFixed.style.display = 'none';
        }
    };

    toggleAddButton();

    // ✅ 変数名を変更する（addButtons → addButtonTriggersなど）
    const addButtonTriggers = [addButtonBottom, addButtonFixed];
    addButtonTriggers.forEach(btn => {
        btn?.addEventListener('click', createItemForm);
    });

});

// カードクリック時の詳細表示
document.addEventListener('click', function (e) {
    const card = e.target.closest('.item-card');

    // 編集・削除ボタンのクリックではないときのみ実行
    if (
        card &&
        !e.target.closest('.item-actions') && // 追加：アクションボタン全体を除外
        !e.target.closest('.edit-item-button') &&
        !e.target.closest('.delete-item-button')
    ) {
        try {
            const item = JSON.parse(card.dataset.item);
            const number = card.dataset.number;
            openDetailModal(item, number);
        } catch (error) {
            console.error("JSONパースエラー:", error);
        }
    }
});

// 編集ボタンのクリック時：オーバーレイを開く
// document.addEventListener('click', function (e) {
//     const editBtn = e.target.closest('.edit-item-button');
//     if (editBtn) {
//         e.stopPropagation(); // これでカードのクリックイベントを止める
//         const item = JSON.parse(editBtn.dataset.item);
//         openOverlay(item);
//         return; // 後続の処理をしない（詳細モーダルを防ぐ）
//     }
// });
document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.edit-item-button');
    if (editBtn) {
        e.stopPropagation();
        const item = JSON.parse(editBtn.dataset.item);
        const card = editBtn.closest('.item-card');
        const number = card?.dataset.number ?? '?'; // ← 番号取得
        openOverlay(item, number); // ← 番号も渡す！
        return;
    }
});

// ✅ openDetailModal の外（上でも下でもOK）
function applyMemoLines(text) {
    const container = document.getElementById('detail-description');
    container.innerHTML = ''; // 初期化

    if (!text) return;

    const firstLine = text.slice(0, 10);
    const rest = text.slice(10);
    const restLines = rest.match(/.{1,15}/g) || [];

    const allLines = [firstLine, ...restLines];

    allLines.forEach(line => {
        const div = document.createElement('div');
        div.textContent = line;
        div.classList.add('memo-line');
        container.appendChild(div);
    });
}



window.openDetailModal = function(item, number = '?') {
    document.getElementById('detail-number').textContent = number;
    document.getElementById('detail-name').textContent = item.name ?? 'なし';

    const expirationWrapper = document.querySelector('#detail-expiration').parentElement;
    const purchaseWrapper = document.querySelector('#detail-purchase').parentElement;
    const quantityWrapper = document.querySelector('#detail-quantity').parentElement;
    const descriptionWrapper = document.querySelector('#detail-description').parentElement;

    // 初期化（前回の情報が残らないように）
    document.getElementById('detail-expiration').textContent = '';
    document.getElementById('detail-purchase').textContent = '';
    document.getElementById('detail-quantity').textContent = '';
    document.getElementById('detail-description').textContent = '';
    document.getElementById('detail-owner').textContent = '';
    document.getElementById('detail-image').src = '';

    // 期限日
    if (item.expiration_date) {
        document.getElementById('detail-expiration').textContent = item.expiration_date;
        expirationWrapper.style.display = 'block';
    } else {
        expirationWrapper.style.display = 'none';
    }

    // 購入日
    if (item.purchase_date) {
        document.getElementById('detail-purchase').textContent = item.purchase_date;
        purchaseWrapper.style.display = 'block';
    } else {
        purchaseWrapper.style.display = 'none';
    }

    // 所有者
    if (window.currentType === 'group' && item.owner && item.owner.user_name) {
        document.getElementById('detail-owner').textContent = item.owner.user_name;
        document.getElementById('detail-owner-wrapper').style.display = 'block';
    } else {
        document.getElementById('detail-owner-wrapper').style.display = 'none';
    }

    // 個数（0も表示する）
    if (item.quantity !== null && item.quantity !== undefined) {
        document.getElementById('detail-quantity').textContent = item.quantity;
        quantityWrapper.style.display = 'block';
    } else {
        quantityWrapper.style.display = 'none';
    }

    // メモ（空白のみの場合も非表示）
    // function formatMemo(text) {
    //     if (!text) return '';
    
    //     const firstLineLimit = 10;
    //     const restLineLimit = 15;
    
    //     const firstLine = text.slice(0, firstLineLimit);
    //     const rest = text.slice(firstLineLimit);
    
    //     const restLines = rest.match(new RegExp(`.{1,${restLineLimit}}`, 'g')) || [];
    
    //     return [firstLine, ...restLines].join('\n');
    // }

    // メモ（空白のみの場合も非表示）
    if (item.description && item.description.trim() !== '') {
        applyMemoLines(item.description);
        descriptionWrapper.style.display = 'block';
    } else {
        descriptionWrapper.style.display = 'none';
    }

    // 画像表示
    const imageEl = document.getElementById('detail-image');
    const placeholder = document.getElementById('detail-no-image');
    if (item.images && item.images.length > 0) {
        imageEl.src = `/storage/${item.images[0].image_path}`;
        imageEl.style.display = 'block';
        placeholder.style.display = 'none';
    } else {
        imageEl.style.display = 'none';
        placeholder.style.display = 'flex';
    }

    document.getElementById('item-detail-modal').style.display = 'flex';
};


window.closeDetailModal = function() {
    document.getElementById('item-detail-modal').style.display = 'none';
};

document.addEventListener('DOMContentLoaded', () => {
    const detailModal = document.getElementById('item-detail-modal');
    const modalCard = detailModal?.querySelector('.modal-card');
    const detailImage = document.getElementById('detail-image');
    const imageWrapper = detailImage?.closest('.modal-image-wrapper');

    if (detailModal && modalCard) {
        // モーダル全体クリックで閉じる（ただし画像エリアは除く）
        detailModal.addEventListener('click', (e) => {
            const isInsideImage = imageWrapper?.contains(e.target);
            if (!isInsideImage) {
                closeDetailModal();
            }
        });

        // // モーダル本体クリックは閉じ処理を止める（フォームエリアなど）
        // modalCard.addEventListener('click', (e) => {
        //     e.stopPropagation();
        // });
    }
});



// ✖ボタンで追加オーバーレイを閉じる関数
window.closeAddForm = function () {
    document.getElementById('add-form-overlay').style.display = 'none';
};

// オーバーレイ背景クリックで閉じる（中身を除く）
document.addEventListener('click', function (e) {
    const overlay = document.getElementById('add-form-overlay');
    const card = overlay?.querySelector('.modal-card');
    if (e.target === overlay) {
        closeAddForm();
    }
});
