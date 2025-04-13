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

}
