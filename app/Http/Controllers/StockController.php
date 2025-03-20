<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\InventoryCategory;

class StockController extends Controller
{
    public function index(Request $request)
{
    // 🚀 ログインユーザーの取得
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login')->with('error', 'ログインしてください');
    }

    // 🚀 ユーザーが所属しているグループを取得
    $groups = $user->groups()->get();
    dd($groups); // 🚀 デバッグ：グループが取得できているか確認

    // 🚀 現在のスペースをセッションから取得
    $currentType = session('current_type', 'personal');
    $currentGroupId = session('current_group_id', null);

    $currentGroup = null;
    if ($currentType === 'group' && !empty($currentGroupId)) {
        $currentGroup = $groups->firstWhere('id', $currentGroupId);
    }

    // 🚀 在庫カテゴリを取得
    $categories = [];
    $inventory = ($currentType === 'personal') 
        ? Inventory::where('user_id', $user->id)->first() 
        : Inventory::where('group_id', $currentGroupId)->first();

    if ($inventory && $inventory->id) {
        $categories = InventoryCategory::where('inventory_id', $inventory->id)->get();
    }

    return view('users.top', [
        'user' => $user,
        'groups' => $groups,
        'currentGroup' => $currentGroup,
        'categories' => $categories,
        'currentType' => $currentType
    ]);
}

    

    public function switchSpace($type, $groupId = null)
    {
        // 🚀 個人スペースに切り替え
        if ($type === 'personal') {
            session(['current_type' => 'personal', 'current_group_id' => null]);
        }
        // 🚀 グループスペースに切り替え
        else if ($type === 'group' && $groupId) {
            session(['current_type' => 'group', 'current_group_id' => $groupId]);
        }

        return redirect()->route('stock.index');
    }
}
