<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スペース作成</title>
    <link rel="stylesheet" href="{{ asset('css/group/create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <div class="space-title">スペース作成</div>
    
            <form method="POST" action="{{ route('group.create') }}">
                @csrf
        
                <div class="select-type">
                    <div class="type-option selected" onclick="selectType('personal')">
                        <i class="fa-solid fa-user"></i>
                        個人
                    </div>
                    <div class="type-option" onclick="selectType('group')">
                        <i class="fa-solid fa-users"></i>
                        グループ
                    </div>
                </div>
        
                <input type="hidden" name="type" id="space-type" value="personal">
        
                <div class="form-section">
                    <label for="name">スペース名</label>
                    <input type="text" id="name" name="name" placeholder="スペース名を入力" required>
                </div>

                <!-- ✅ ここに追加！チェックされたユーザーの hidden input 保持用 -->
                <div id="selected-users-hidden"></div>
        
                <button type="submit" class="submit-button">作成</button>
            </form>
        

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
    
    <script>
        function selectType(type) {
            document.getElementById('space-type').value = type;
            document.querySelectorAll('.type-option').forEach(el => el.classList.remove('selected'));
            document.querySelector(`.type-option:nth-child(${type === 'personal' ? 1 : 2})`).classList.add('selected');
        }

        function selectType(type) {
            document.getElementById('space-type').value = type;
            document.querySelectorAll('.type-option').forEach(el => el.classList.remove('selected'));
            document.querySelector(`.type-option:nth-child(${type === 'personal' ? 1 : 2})`).classList.add('selected');
        }

    </script>
</body>
</html>
