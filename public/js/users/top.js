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

function confirmDelete(itemCount) {
    if (itemCount > 0) {
        return confirm('このカテゴリにはアイテムが含まれています。一括削除してもよろしいですか？');
    } else {
        return confirm('このカテゴリを削除しますか？');
    }
}

function openEditModal(id, name) {
    // スワイプ解除
    const item = document.querySelector(`.category-item[data-id='${id}']`);
    item.classList.remove('swiped');

    // モーダル処理
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editModal').style.display = 'block'; // ← flexに注意
}


function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function submitEdit() {
    const id = document.getElementById('editCategoryId').value;
    const name = document.getElementById('editCategoryName').value;

    fetch(`/category/update-name/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 画面上の名前を更新
            const item = document.querySelector(`.category-item[data-id='${id}']`);
            if (item) {
                item.querySelector('.category-content a').textContent = name;
            }
            closeEditModal();
        } else {
            alert('更新に失敗しました。');
        }
    })
    .catch(() => alert('通信エラーが発生しました。'));
}


function closeInvitationPopup() {
    const popupOverlay = document.getElementById('invitation-popup-overlay');
    const invitationIds = [...document.querySelectorAll('#close-popup-form input[name="invitation_ids[]"]')].map(input => input.value);

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const markViewedUrl = document.querySelector('meta[name="mark-viewed-route"]')?.content;

    if (!markViewedUrl) {
        console.error('markViewedルートが見つかりません');
        return;
    }

    fetch(markViewedUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ invitation_ids: invitationIds })
    })
    .then(response => {
        if (response.ok) {
            if (popupOverlay) popupOverlay.style.display = 'none';
        } else {
            console.error('保留送信に失敗しました');
        }
    })
    .catch(error => {
        console.error('エラー:', error);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('user-name-toggle');
    const spaceOverlay = document.getElementById('space-popup-overlay');
    if (toggleBtn && spaceOverlay) {
        toggleBtn.addEventListener('click', function () {
            spaceOverlay.style.display = 'flex';
        });
        spaceOverlay.addEventListener('click', function (e) {
            if (e.target.id === 'space-popup-overlay') {
                spaceOverlay.style.display = 'none';
            }
        });
    }

    const closeBtn = document.getElementById('close-popup');
    const popupOverlay = document.getElementById('invitation-popup-overlay');

    if (closeBtn && popupOverlay) {
        closeBtn.addEventListener('click', closeInvitationPopup);
        popupOverlay.addEventListener('click', function (e) {
            if (e.target.id === 'invitation-popup-overlay') {
                closeInvitationPopup();
            }
        });
    }

    const newCategoryInput = document.getElementById('new-category-name');
    if (newCategoryInput) {
        newCategoryInput.addEventListener('blur', function () {
            const input = this;
            const name = input.value.trim();
            if (name === '') return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const inventoryId = input.closest('form')?.querySelector('input[name="inventory_id"]')?.value;

            fetch('/category/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    category_name: name,
                    inventory_id: inventoryId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.id && data.name) {
                    const categoryList = document.querySelector('.category-list');

                    const li = document.createElement('li');
                    li.classList.add('category-item');
                    li.dataset.id = data.id;

                    const dot = document.createElement('div');
                    dot.classList.add('category-dot');
                    dot.style.backgroundColor = (categoryList.children.length % 2 === 0) ? 'pink' : 'cyan';

                    const content = document.createElement('div');
                    content.classList.add('category-content');
                    content.style.borderColor = (categoryList.children.length % 2 === 0) ? 'cyan' : 'hotpink';

                    const span = document.createElement('span');
                    span.classList.add('category-name');
                    span.textContent = data.name;

                    const count = document.createElement('span');
                    count.classList.add('category-count');
                    count.textContent = '（0）';

                    const inputEdit = document.createElement('input');
                    inputEdit.classList.add('category-edit-input');
                    inputEdit.type = 'text';
                    inputEdit.value = data.name;
                    inputEdit.style.display = 'none';

                    content.appendChild(span);
                    content.appendChild(inputEdit);
                    content.appendChild(count);

                    li.appendChild(dot);
                    li.appendChild(content);

                    categoryList.appendChild(li);
                }

                input.value = '';
            });
        });
    }

    const userId = document.querySelector('meta[name="user-id"]')?.content;

    if (userId) {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'local',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws']
        });

        Echo.private(`user.${userId}`)
            .listen('.InvitationSent', (e) => {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const popup = document.getElementById('invitation-popup');

                const box = document.createElement('div');
                box.classList.add('invitation-box');
                box.innerHTML = `
                    <p>「${e.group_name}」に参加しますか？</p>
                    <form action="/invitation/respond" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${token}">
                        <input type="hidden" name="invitation_id" value="${e.invitation_id}">
                        <button name="response" value="accept">参加</button>
                        <button name="response" value="decline">辞退</button>
                    </form>
                `;

                if (popup) {
                    popup.appendChild(box);
                    popup.style.display = 'block';
                }
            });
    }
});
