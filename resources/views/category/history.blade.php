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

        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p style="color: red;">{{ session('error') }}</p>
        @endif

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

                        @if($currentCategoryCount < 5)
                            <form action="{{ route('category.restore', $category->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">カテゴリを復元</button>
                            </form>
                        @else
                            <p style="color: red;">※カテゴリ数が上限に達しているため復元できません</p>
                        @endif
                        
                        <form action="{{ route('category.forceDelete', $category->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('このカテゴリを完全に削除しますか？復元できなくなります。');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red;">完全に削除</button>
                        </form>
                            

                        <hr>
                    </li>
                @endforeach
            </ul>
        @endif

</body>
</html>
