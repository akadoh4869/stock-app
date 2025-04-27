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
        
                <div class="setting-item" onclick="openOverlay('withdraw-modal')">
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
            <!-- オーバーレイ（フルスクリーンページ風） -->
            <div id="invitation-overlay" class="overlay" style="display: none;">
                
                <!-- 上ライン -->
                <div class="line-top">
                    <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
                    <div class="header-overlay">
                        <div class="header-container">
                            <a href="#" class="back-link" onclick="closeOverlay('invitation-overlay')">← 戻る</a>
                            <div class="app-name">ストログ</div>
                        </div>
                    </div>
                </div>


                <!-- メインエリア -->
                <div class="main">
                    <div class="content">
                        
                        <div class="history-title">保留中招待一覧</div>

                        <div class="invitation-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                       

                        @if($pendingInvitations->isEmpty())
                            <p style="text-align: center; margin-top: 50px;">保留中のグループ招待はありません。</p>
                        @else
                            <div class="invitation-list">
                                @foreach($pendingInvitations as $invitation)
                                    <div class="invitation-card">
                                        <div class="invitation-text">
                                            {{ $invitation->group->name }}
                                        </div>
                                        <div class="invitation-buttons">
                                            <form action="{{ route('invitation.respond') }}" method="POST" style="display: flex; gap: 10px;">
                                                @csrf
                                                <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                                                @if($totalSpaceCount < 3)
                                                    <button class="accept-btn" name="response" value="accept">参加</button>
                                                @endif
                                                <button class="decline-btn" name="response" value="decline">辞退</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 下ライン -->
                <div class="line-bottom-fixed">
                    <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
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
            <div id="withdraw-modal" class="overlay fullscreen-modal" style="display: none;">
                <div class="overlay-content">
                    <button class="close" onclick="closeOverlay('withdraw-modal')">&times;</button>
                    <h3>退会確認</h3>
                    <div class="modal-scroll-content">
                        <p>本当に退会しますか？<br>すべてのデータが削除されます。</p>
                    </div>
                    <div class="confirm-buttons">
                        <form method="POST" action="{{ route('withdraw') }}">
                            @csrf
                            <button type="submit" class="confirm">退会する</button>
                            <button type="button" class="cancel" onclick="closeOverlay('withdraw-modal')">キャンセル</button>
                        </form>
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
