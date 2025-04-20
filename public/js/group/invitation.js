document.addEventListener('DOMContentLoaded', function () {
    const selectedUsers = new Set();

    function showInviteSection() {
        const instruction = document.getElementById('invite-instruction');
        const section = document.getElementById('invite-section');
        if (instruction) instruction.style.display = 'none';
        if (section) section.style.display = 'block';
    }

    window.showInviteSection = showInviteSection;

    const searchInput = document.getElementById('user-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', async function () {
            const keyword = this.value.trim();
            if (!keyword) return;

            const resultsContainer = document.getElementById('invite-results');
            resultsContainer.innerHTML = '検索中...';

            try {
                const response = await fetch(`/search-users?keyword=${encodeURIComponent(keyword)}`);
                const users = await response.json();

                if (users.length === 0) {
                    resultsContainer.innerHTML = '<p>該当なし</p>';
                    return;
                }

                resultsContainer.innerHTML = '';
                users.forEach(user => {
                    if (selectedUsers.has(user.user_name)) return;

                    const label = document.createElement('label');
                    label.innerHTML = `
                        <input type="checkbox" value="${user.user_name}">
                        ${user.user_name}
                    `;
                    const checkbox = label.querySelector('input');
                    checkbox.addEventListener('change', function () {
                        selectUser(this);
                    });
                    resultsContainer.appendChild(label);
                });
            } catch (err) {
                resultsContainer.innerHTML = '<p>検索に失敗しました</p>';
            }
        });
    }

    function selectUser(checkbox) {
        const username = checkbox.value;
        if (checkbox.checked) {
            selectedUsers.add(username);
        } else {
            selectedUsers.delete(username);
        }
        renderSelectedUsers();
    }

    function removeUser(username) {
        selectedUsers.delete(username);
        renderSelectedUsers();
    }

    function renderSelectedUsers() {
        const list = document.getElementById('selected-users-list');
        const hidden = document.getElementById('selected-users-hidden');
        list.innerHTML = '';
        hidden.innerHTML = '';
    
        selectedUsers.forEach(username => {
            const li = document.createElement('li');
            li.className = 'selected-user-item';
    
            // 左側（丸＋名前）
            const left = document.createElement('div');
            left.className = 'selected-user-left';
    
            const dot = document.createElement('div');
            dot.className = 'selected-user-dot';
    
            const nameSpan = document.createElement('span');
            nameSpan.className = 'selected-user-name';
            nameSpan.textContent = username;
    
            left.appendChild(dot);
            left.appendChild(nameSpan);
    
            // 右側（×ボタン）
            const button = document.createElement('button');
            button.className = 'remove-user-btn';
            button.textContent = '×';
            button.onclick = () => {
                selectedUsers.delete(username);
                renderSelectedUsers();
            };
    
            li.appendChild(left);
            li.appendChild(button);
            list.appendChild(li);
    
            // hidden input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'invites[]';
            input.value = username;
            hidden.appendChild(input);
        });
    }
    
});
