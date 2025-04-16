<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryItemImage;
use App\Models\User;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 保留中のグループ招待を取得
        $pendingInvitations = \App\Models\GroupInvitation::where('invitee_id', $user->id)
            ->where('status', 'pending')
            ->with('group') // ← 招待元グループ名を使うなら必要
            ->get();

        // 所属グループ数
        $groupCount = \DB::table('group_members')
            ->where('user_id', $user->id)
            ->count();

        // 個人スペース数
        $personalCount = \App\Models\Inventory::where('owner_id', $user->id)->count();

        // 合計スペース数（参加制限用）
        $totalSpaceCount = $groupCount + $personalCount;

        return view('setting', compact('pendingInvitations', 'totalSpaceCount'));
    }

    public function account()
    {
        $user = Auth::user()->load('inventories', 'groups.inventories');

        // ① 個人の在庫スペース
        $personalInventoryIds = $user->inventories->pluck('id');

        // ② 所属グループの在庫スペース
        $groupInventoryIds = $user->groups->flatMap(function ($group) {
            return $group->inventories->pluck('id');
        });

        // ③ 全ての inventory_id を合体
        $allInventoryIds = $personalInventoryIds->merge($groupInventoryIds);

        // ④ 削除されたカテゴリ一覧
        $deletedCategories = \App\Models\InventoryCategory::onlyTrashed()
            ->whereIn('inventory_id', $allInventoryIds)
            ->get();

        // ⑤ 削除されたアイテム一覧（カテゴリ経由）
        $deletedItems = \App\Models\InventoryItem::onlyTrashed()
            ->whereIn('category_id', function ($query) use ($allInventoryIds) {
                $query->select('id')
                    ->from('inventory_categories')
                    ->whereIn('inventory_id', $allInventoryIds);
            })
            ->get();

        return view('users.account', [
            'user' => $user,
            'deletedCategories' => $deletedCategories,
            'deletedItems' => $deletedItems,
        ]);
    }

    
    
}
