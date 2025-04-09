document.addEventListener('DOMContentLoaded', function () {
    const draftItems = {};
    let itemIndex = 0;
    let updatedItem = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // フォーム追加
    const addButton = document.getElementById('add-item-button');
    if (addButton) {
        addButton.addEventListener('click', () => {
            const container = document.getElementById('item-form-container');
            let ownerSelect = '';

            if (window.currentType === 'group' && window.members.length > 0) {
                ownerSelect += `<div><label>所有者：</label><select name="owner_id"><option value="">共有</option>`;
                window.members.forEach(member => {
                    ownerSelect += `<option value="${member.id}">${member.user_name}</option>`;
                });
                ownerSelect += `</select></div>`;
            }

            const formGroup = document.createElement('div');
            formGroup.classList.add('item-form-box');
            formGroup.setAttribute('data-index', itemIndex);
            formGroup.style = 'border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px; position: relative;';
            formGroup.innerHTML = `
                <button type="button" class="close-form-button" style="position: absolute; top: 5px; right: 10px; font-size: 18px;">✕</button>
                <div><label>アイテム名：</label><input type="text" name="name" required></div>
                <div><label>画像：</label><input type="file" name="image" accept="image/*"></div>
                <div><label>期限日：</label><input type="date" name="expiration_date"></div>
                <div><label>購入日：</label><input type="date" name="purchase_date"></div>
                ${ownerSelect}
                <div><label>個数：</label><input type="number" name="quantity" min="1" value="1" required></div>
                <div><label>メモ：</label><textarea name="description" rows="2" style="width: 100%;"></textarea></div>
                <div style="text-align:center; margin-top:10px;">
                    <button type="button" class="submit-item-button" data-index="${itemIndex}">追加</button>
                </div>
            `;
            container.appendChild(formGroup);
            itemIndex++;
        });
    }

    // フォーム削除
    document.addEventListener('click', event => {
        if (event.target.classList.contains('close-form-button')) {
            event.target.closest('.item-form-box')?.remove();
        }
    });

    // アイテム追加
    document.addEventListener('click', event => {
        if (event.target.classList.contains('submit-item-button')) {
            const index = event.target.dataset.index;
            const formBox = document.querySelector(`.item-form-box[data-index="${index}"]`);
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

            formData.append('_token', csrfToken);

            fetch(window.itemStoreUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(item => {
                const listContainer = document.querySelector('.main > div');
                const newCard = document.createElement('div');
                newCard.classList.add('item-card');
                newCard.setAttribute('data-id', item.id);
                newCard.setAttribute('onclick', `openOverlay(${JSON.stringify(item)})`);
                newCard.innerHTML = `
                    <div class="item-number">?</div>
                    <div class="item-header">
                        <span class="item-name">${item.name}</span>
                        <span class="item-quantity">x ${item.quantity}</span>
                    </div>
                    <div class="item-row"><label>期限：</label><span>${item.expiration_date ?? ''}</span></div>
                `;
                listContainer.prepend(newCard);
                formBox.remove();
            })
            .catch(err => alert('保存失敗: ' + err.message));
        }
    });

    // オーバーレイを開く
    window.openOverlay = function (item) {
        updatedItem = structuredClone(item);
        const overlay = document.getElementById('item-overlay');
        const body = document.getElementById('overlay-body');

        let ownerSelect = '';
        if (window.currentType === 'group' && window.members.length > 0) {
            ownerSelect += `<select name="owner_id" data-item-id="${item.id}" class="autosave-input"><option value="">共有</option>`;
            window.members.forEach(user => {
                ownerSelect += `<option value="${user.id}" ${user.id === item.owner_id ? 'selected' : ''}>${user.user_name}</option>`;
            });
            ownerSelect += `</select>`;
        }

        body.innerHTML = `
            <div class="item-row"><label>名前：</label><input type="text" name="name" data-item-id="${item.id}" value="${item.name}" class="autosave-input"></div>
            <div class="item-row"><label>期限日：</label><input type="date" name="expiration_date" data-item-id="${item.id}" value="${item.expiration_date}" class="autosave-input"></div>
            <div class="item-row"><label>購入日：</label><input type="date" name="purchase_date" data-item-id="${item.id}" value="${item.purchase_date}" class="autosave-input"></div>
            ${ownerSelect ? `<div class="item-row"><label>所有者：</label>${ownerSelect}</div>` : ''}
            <div class="item-row"><label>個数：</label><input type="number" name="quantity" data-item-id="${item.id}" value="${item.quantity}" class="autosave-input"></div>
            <div class="item-row"><label>メモ：</label><textarea name="description" data-item-id="${item.id}" class="autosave-input">${item.description ?? ''}</textarea></div>
            <div class="item-row"><label>画像：</label>
                <input type="file" name="image" accept="image/*" class="image-upload-input" data-item-id="${item.id}">
            </div>
        `;

        overlay.style.display = 'flex';
    };

    // オーバーレイを閉じる
    window.closeOverlay = function () {
        document.getElementById('item-overlay').style.display = 'none';

        if (updatedItem) {
            const card = document.querySelector(`.item-card[data-id="${updatedItem.id}"]`);
            if (card) {
                card.querySelector('.item-name').textContent = updatedItem.name;
                card.querySelector('.item-quantity').textContent = 'x ' + updatedItem.quantity;
                card.querySelector('.item-row span').textContent = updatedItem.expiration_date ?? '';
            }
        }

        updatedItem = null;
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
            if (!confirm('このアイテムを削除しますか？')) return;

            fetch(`/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('削除に失敗しました');
                button.closest('.item-card').remove();
            })
            .catch(error => alert(error.message));
        }
    });

    const detailImage = document.getElementById('detail-image');
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');

    if (detailImage && modal && modalImage) {
        detailImage.addEventListener('click', function () {
            const src = detailImage.getAttribute('src');
            if (src) {
                modalImage.src = src;
                modal.style.display = 'flex';
            }
        });

        modal.addEventListener('click', function () {
            modal.style.display = 'none';
            modalImage.src = '';
        });
    }
    
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
document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.edit-item-button');
    if (editBtn) {
        e.stopPropagation(); // これでカードのクリックイベントを止める
        const item = JSON.parse(editBtn.dataset.item);
        openOverlay(item);
        return; // 後続の処理をしない（詳細モーダルを防ぐ）
    }
});


window.openDetailModal = function(item, number = '?') {
    document.getElementById('detail-number').textContent = number;
    document.getElementById('detail-name').textContent = item.name ?? 'なし';

    // 日付・個数・メモ
    const expirationWrapper = document.querySelector('#detail-expiration').parentElement;
    const purchaseWrapper = document.querySelector('#detail-purchase').parentElement;
    const quantityWrapper = document.querySelector('#detail-quantity').parentElement;
    const descriptionWrapper = document.querySelector('#detail-description').parentElement;

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

    // 所有者（グループのみ）
    if (window.currentType === 'group' && item.owner && item.owner.user_name) {
        document.getElementById('detail-owner').textContent = item.owner.user_name;
        document.getElementById('detail-owner-wrapper').style.display = 'block';
    } else {
        document.getElementById('detail-owner-wrapper').style.display = 'none';
    }

    // 個数
    if (item.quantity) {
        document.getElementById('detail-quantity').textContent = item.quantity;
        quantityWrapper.style.display = 'block';
    } else {
        quantityWrapper.style.display = 'none';
    }

    // メモ
    if (item.description) {
        document.getElementById('detail-description').textContent = item.description;
        descriptionWrapper.style.display = 'block';
    } else {
        descriptionWrapper.style.display = 'none';
    }

    // 画像
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



