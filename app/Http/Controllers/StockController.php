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
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        // ✅ セッション初期化（初めて来た時）
        if (!session()->has('current_type')) {
            session([
                'current_type' => 'personal',
                'current_group_id' => null
            ]);
        }

        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');

        // ✅ 所属グループを取得
        $groups = $user->groups()->get();

        // ✅ 現在のグループ（選択されていれば）
        $currentGroup = null;
        if ($currentType === 'group' && !empty($currentGroupId)) {
            $currentGroup = $groups->firstWhere('id', $currentGroupId);
        }

        // ✅ 在庫取得（owner_id を見る）
        $inventory = null;
        if ($currentType === 'personal') {
            $inventory = Inventory::where('owner_id', $user->id)->first();
        } elseif ($currentType === 'group' && $currentGroupId) {
            $inventory = Inventory::where('group_id', $currentGroupId)->first();
        }

        // ✅ カテゴリ取得
        $categories = [];
        if ($inventory) {
            $categories = InventoryCategory::where('inventory_id', $inventory->id)->get();
        }

        return view('users.top', [
            'user' => $user,
            'groups' => $groups,
            'currentGroup' => $currentGroup,
            'categories' => $categories,
            'currentType' => $currentType,
            'inventory' => $inventory // ← これを追加！
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
