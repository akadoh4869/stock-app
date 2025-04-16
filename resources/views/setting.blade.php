<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>設定ページ</title>
    <link rel="stylesheet" href="{{ asset('css/setting.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/setting.js') }}"></script>
    
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <div class="app-name">ストログ</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="main">
            <div class="setting-wrapper">
                <div class="setting-title">設定</div>
        
                <div class="setting-item" onclick="window.location.href='/account'">
                    <i class="fa-solid fa-user" style="color:#ff66cc;"></i>
                    <div class="setting-label">アカウント設定</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('invitation-overlay')">
                    <i class="fa-solid fa-envelope" style="color:#5ce0f0;"></i>
                    <div class="setting-label">招待一覧</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('policy-overlay')">
                    <i class="fa-solid fa-scroll" style="color:#ff66cc;"></i>
                    <div class="setting-label">ストログ規約</div>
                </div>
        
                <div class="setting-item" onclick="window.location.href='/contact'">
                    <i class="fa-solid fa-comments" style="color:#5ce0f0;"></i>
                    <div class="setting-label">お問い合せ</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('withdraw-overlay')">
                    <i class="fa-solid fa-hand" style="color:#ff66cc;"></i>
                    <div class="setting-label">退会する</div>
                </div>

                @if(Auth::user() && Auth::user()->is_admin)
                    <div class="setting-item" onclick="window.location.href='/admin'">
                        <i class="fa-solid fa-user-shield" style="color:#5ce0f0;"></i>
                        <div class="setting-label">管理者ページ</div>
                    </div>
                @endif
        
                
            </div>
        
            <!-- オーバーレイ：招待一覧 -->
            <div id="invitation-overlay" class="overlay">
                <div class="overlay-content">
                    <button class="close" onclick="closeOverlay('invitation-overlay')">&times;</button>
                    <h2>保留中のグループ招待</h2>
                        @if($pendingInvitations->isEmpty())
                            <p>保留中のグループ招待はありません。</p>
                        @else
                            <ul>
                                @foreach($pendingInvitations as $invitation)
                                    <li style="margin-bottom: 10px;">
                                        「{{ $invitation->group->name }}」への招待
                                        <form action="{{ route('invitation.respond') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                                            @if($totalSpaceCount < 3)
                                                <button name="response" value="accept">参加</button>
                                            @endif
                                            <button name="response" value="decline">辞退</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                </div>
            </div>
        
            <!-- ストログ規約のメニュー一覧オーバーレイ -->
            <div id="policy-overlay" class="overlay">
                <div class="overlay-content">
                <button class="close" onclick="closeOverlay('policy-overlay')">&times;</button>
                <h3>ストログ規約</h3>
                <ul class="policy-menu">
                    <li onclick="alert('バージョン: 1.0.0')">バージョン表示</li>
                    <li onclick="openOverlay('terms-modal')">利用規約</li>
                    <li onclick="openOverlay('privacy-modal')">プライバシーポリシー</li>
                    <li onclick="openOverlay('copyright-modal')">著作権情報</li>
                    <li onclick="clearAppCache()">キャッシュクリア</li>
                    
                </ul>
                </div>
            </div>
            
            <!-- 利用規約モーダル -->
            <div id="terms-modal" class="overlay fullscreen-modal" style="display: none;">
                <div class="overlay-content">
                <button class="close" onclick="closeOverlay('terms-modal')">&times;</button>
                <h3>利用規約</h3>
                <div class="modal-scroll-content">
                    <p>ここに利用規約の全文を記載します。</p>
                </div>
                </div>
            </div>
            
            <!-- プライバシーポリシーモーダル -->
            <div id="privacy-modal" class="overlay fullscreen-modal" style="display: none;">
                <div class="overlay-content">
                <button class="close" onclick="closeOverlay('privacy-modal')">&times;</button>
                <h3>プライバシーポリシー</h3>
                <div class="modal-scroll-content">
                    <p>ここにプライバシーポリシーの全文を記載します。</p>
                </div>
                </div>
            </div>
            
            <!-- 著作権情報モーダル -->
            <div id="copyright-modal" class="overlay fullscreen-modal" style="display: none;">
                <div class="overlay-content">
                <button class="close" onclick="closeOverlay('copyright-modal')">&times;</button>
                <h3>著作権情報</h3>
                <div class="modal-scroll-content">
                    <p>ここに著作権情報の全文を記載します。</p>
                </div>
                </div>
            </div>
            
            
        
            <!-- オーバーレイ：退会確認 -->
            <div id="withdraw-overlay" class="overlay">
                <div class="overlay-content">
                    <button class="close" onclick="closeOverlay('withdraw-overlay')">&times;</button>
                    <h3>本当に退会しますか？</h3>
                    <p>この操作は取り消せません。</p>
                    <div class="confirm-buttons">
                        <button class="cancel" onclick="closeOverlay('withdraw-overlay')">キャンセル</button>
                        <button class="confirm" onclick="alert('退会処理を実行')">退会する</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- フッター背景画像（画面最下部に固定） -->
        <div class="line-bottom-fixed">
            <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
        </div>

        <!-- フッターのボタン群（画像の上に表示） -->
        <div class="footer-overlay-fixed">
            
            <button onclick="window.location.href='/top'">
                <i class="fa-solid fa-house" style="color:#5ce0f0;"></i><br>ホーム
            </button>
            <button onclick="window.location.href='/history'">
                <i class="fa-solid fa-clock"></i><br>履歴
            </button>
        </div>
    </main>
    

    <script>
        function openOverlay(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeOverlay(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>
</html>
