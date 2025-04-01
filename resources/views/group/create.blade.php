<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スペース作成</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 30px;
        }

        .form-section {
            margin-bottom: 20px;
        }

        label {
            margin-right: 15px;
        }
    </style>
</head>
<body>

    <h1>スペースを作成</h1>

    <form method="POST" action="{{ route('group.create') }}">
        @csrf

        <div class="form-section">
            <label>
                <input type="radio" name="type" value="personal" checked>
                個人スペース
            </label>
            <label>
                <input type="radio" name="type" value="group">
                グループスペース
            </label>
        </div>

        <div class="form-section">
            <label for="name">スペース名:</label><br>
            <input type="text" id="name" name="name" placeholder="スペース名を入力" required>
        </div>

        <button type="submit">作成する</button>
    </form>

    <div style="margin-top: 20px;">
        <a href="{{ route('stock.index') }}">← 戻る</a>
    </div>

</body>
</html>
