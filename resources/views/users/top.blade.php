<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>トップページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .group-list {
            list-style: none;
            padding: 0;
        }
        .group-list li {
            margin: 10px 0;
        }
        .category-list {
            list-style: none;
            padding: 0;
            border: 2px solid purple;
            display: inline-block;
            padding: 10px;
        }
        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px dotted #999;
            padding: 5px;
        }
        .category-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>

    @if(isset($user))
        <h2>{{ $user->user_name }}</h2>
    @endif

    
    <p>現在のスペース: 
        <strong>
            @if(isset($currentType) && $currentType === 'personal')
                個人スペース
            @else
                {{ $currentGroup->name ?? 'なし' }}
            @endif
        
        </strong>
    </p>

    <p>スペースを切り替え:</p>
    <ul class="group-list">
        <li>
            <a href="{{ route('stock.switch.space', ['type' => 'personal']) }}" 
                style="{{ isset($currentType) && $currentType === 'personal' ? 'font-weight: bold;' : '' }}">
                個人スペース
            </a>
        </li>
        @if(isset($groups) && $groups->isNotEmpty())
            @foreach($groups as $group)
                <li>
                    <a href="{{ route('stock.switch.space', ['type' => 'group', 'groupId' => $group->id]) }}" 
                    style="{{ isset($currentType) && $currentType === 'group' && isset($currentGroup) && $group->id == $currentGroup->id ? 'font-weight: bold;' : '' }}">
                        {{ $group->name }}
                    </a>
                </li>
            @endforeach
        @else
            <li>グループがありません</li>
        @endif

    
    </ul>

    <h3>在庫カテゴリ</h3>
    @if(isset($categories) && count($categories) > 0)
    <ul class="category-list">
        @foreach($categories as $category)
            <li class="category-item">
                <div class="category-dot" style="background-color: {{ $loop->index % 2 == 0 ? 'pink' : 'cyan' }};"></div>
                {{ $category->name }}（{{ $category->items->count() }}）
            </li>
        @endforeach
    </ul>
@else
    <p>カテゴリがありません</p>
@endif


    <br>
    <button onclick="window.location.href='/create-group'">グループ作成</button>
    <button onclick="window.location.href='/history'">履歴</button>
    <button onclick="window.location.href='/settings'">設定</button>

</body>
</html>
