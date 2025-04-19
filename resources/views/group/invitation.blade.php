<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー招待</title>
    <link rel="stylesheet" href="{{ asset('css/group/invitation.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h1>メンバー招待</h1>

    <!-- ✅ from=create のときはボタン選択式 -->
    @if(request('from') === 'create')
        <div id="invite-instruction">
            <p>グループを作成しました。次にメンバーを招待しますか？</p>
            <button onclick="showInviteSection()">メンバーを招待する</button>
            <button onclick="window.location.href='{{ route('stock.index') }}'">スキップ</button>
        </div>
        <!-- ✅ 招待フォーム開始 -->
        <form method="POST" action="{{ route('group.invite.send') }}" id="invite-form">
            @csrf
            <input type="hidden" name="group_id" value="{{ $group->id }}">
            <div id="invite-section" style="display: none;" class="invite-wrapper">
    @else
        <!-- ✅ 通常時：最初から表示 -->
        <form method="POST" action="{{ route('group.invite.send') }}" id="invite-form">
            @csrf
            <input type="hidden" name="group_id" value="{{ $group->id }}">
            <div id="invite-section" class="invite-wrapper">
    @endif

                <label>メンバーを招待する</label>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="user-search-input" placeholder="ユーザー名で検索">
                </div>

                <!-- 検索結果を表示 -->
                <div id="invite-results" class="invite-results"></div>

                <!-- ✅ 選択済みユーザー表示 -->
                <div id="selected-users-display" style="margin-top:10px;">
                    {{-- <p>招待予定ユーザー:</p> --}}
                    <ul id="selected-users-list"></ul>
                </div>

                <!-- ✅ hidden input 埋め込み -->
                <div id="selected-users-hidden"></div>

                <!-- ✅ 招待ボタン -->
                <div style="text-align: center; margin-top: 15px;">
                    <button type="submit" style="padding: 10px 30px; background-color: #ff66cc; color: white; border: none; border-radius: 20px; font-size: 16px; cursor: pointer;">
                        招待する
                    </button>
                </div>

            </div>
        </form>


    <br>
    <a href="{{ route('stock.index') }}">← トップに戻る</a>

    <script>
        function showInviteSection() {
            document.getElementById('invite-instruction').style.display = 'none';
            document.getElementById('invite-section').style.display = 'block';
        }

        const selectedUsers = new Set();

        document.getElementById('user-search-input').addEventListener('input', async function () {
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
                        <input type="checkbox" value="${user.user_name}" onchange="selectUser(this)">
                        ${user.user_name}
                    `;
                    resultsContainer.appendChild(label);
                });
            } catch (err) {
                resultsContainer.innerHTML = '<p>検索に失敗しました</p>';
            }
        });

        function selectUser(checkbox) {
            const username = checkbox.value;
            if (checkbox.checked) {
                selectedUsers.add(username);
            } else {
                selectedUsers.delete(username);
            }
            renderSelectedUsers();
        }

        function renderSelectedUsers() {
            const list = document.getElementById('selected-users-list');
            const hidden = document.getElementById('selected-users-hidden');
            list.innerHTML = '';
            hidden.innerHTML = '';

            selectedUsers.forEach(username => {
                const li = document.createElement('li');
                li.textContent = username;
                list.appendChild(li);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'invites[]';
                input.value = username;
                hidden.appendChild(input);
            });
        }
    </script>
</body>
</html>

<script>
    const selectedUsers = new Set();

document.getElementById('user-search-input').addEventListener('input', async function() {
    const keyword = this.value.trim();
    if (!keyword) return;

    // ✅ 検索結果の表示先
    const resultsContainer = document.getElementById('invite-results');
    resultsContainer.innerHTML = '検索中...';

    try {
        const response = await fetch(`/search-users?keyword=${encodeURIComponent(keyword)}`)

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
                <input type="checkbox" value="${user.user_name}" onchange="selectUser(this)">
                ${user.user_name}
            `;
            resultsContainer.appendChild(label);
        });
    } catch (err) {
        resultsContainer.innerHTML = '<p>検索に失敗しました</p>';
    }
});

function selectUser(checkbox) {
    const username = checkbox.value;
    if (checkbox.checked) {
        selectedUsers.add(username);
    } else {
        selectedUsers.delete(username);
    }
    renderSelectedUsers();
}

function renderSelectedUsers() {
    const list = document.getElementById('selected-users-list');
    const hidden = document.getElementById('selected-users-hidden');
    list.innerHTML = '';
    hidden.innerHTML = '';

    selectedUsers.forEach(username => {
        const li = document.createElement('li');
        li.textContent = username;
        list.appendChild(li);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'invites[]';
        input.value = username;
        hidden.appendChild(input);
    });
}

// ✅ グループが選択された時だけ招待セクションを表示
function selectType(type) {
    document.getElementById('space-type').value = type;
    document.querySelectorAll('.type-option').forEach(el => el.classList.remove('selected'));
    document.querySelector(`.type-option:nth-child(${type === 'personal' ? 1 : 2})`).classList.add('selected');

    const inviteSection = document.getElementById('invite-section');
    if (type === 'group') {
        inviteSection.style.display = 'block';
    } else {
        inviteSection.style.display = 'none';
    }
}
</script>