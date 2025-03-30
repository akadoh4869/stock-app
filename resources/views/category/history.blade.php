<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除履歴</title>
    <link rel="stylesheet" href="{{ asset('css/category/history.css') }}">
</head>
<body>
    <h1>削除されたカテゴリとアイテム一覧</h1>

    <a href="{{ route('stock.index') }}">← 戻る</a>

    @if($deletedCategories->isEmpty())
        <p>削除されたカテゴリはありません。</p>
    @else
        <ul>
            @foreach($deletedCategories as $category)
                <li>
                    <strong>カテゴリ名：</strong> {{ $category->name }} <br>
                    <ul>
                        @foreach($category->items as $item)
                            <li>・{{ $item->name }}（数量: {{ $item->quantity }}）</li>
                        @endforeach
                    </ul>

                    <!-- ✅ 復元ボタン -->
                    <form action="{{ route('category.restore', $category->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">カテゴリを復元</button>
                    </form>
                    
                    <hr>
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
