<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー招待</title>
    <link rel="stylesheet" href="{{ asset('css/group/invitation.css') }}">
    <script src="{{ asset('js/group/invitation.js') }}"></script>
    @include('partials.head')
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
                </div>
            </div>

        </div>
        <div class="main">
            <!-- ✅ from=create のときはボタン選択式 -->
            @if(request('from') === 'create')
                <div id="invite-instruction" class="invite-instruction-wrapper">
                    <p class="instruction-text">グループを作成しました。<br>次にメンバーを招待しますか？</p>
                    <div class="invite-button-group">
                        <button class="invite-large-button" onclick="showInviteSection()">メンバーを招待する</button>
                        <button class="skip-large-button" onclick="window.location.href='{{ route('stock.index') }}'">スキップ</button>
                    </div>
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
                            <p>招待予定ユーザー:</p>
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

        </div>
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

</body>
</html>
