<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\GroupInvitation;

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
    
        // ✅ グループ招待の未対応データを取得（acceptedがnull）
        $pendingInvitations = GroupInvitation::where('invitee_id', $user->id)
        ->where('status', 'pending') // ← 修正点
        ->with('group')
        ->get();
    

    
        return view('users.top', [
            'user' => $user,
            'groups' => $groups,
            'currentGroup' => $currentGroup,
            'categories' => $categories,
            'currentType' => $currentType,
            'inventory' => $inventory,
            'pendingInvitations' => $pendingInvitations // ← 招待情報をBladeに渡す
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

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $request->validate([
            'category_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.memo' => 'nullable|string',
            'items.*.expiration_date' => 'nullable|date',
            'items.*.purchase_date' => 'nullable|date',
            'items.*.owner_id' => 'nullable|exists:users,id',
        ]);

        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');

        // 在庫を取得（個人 or グループ）
        if ($currentType === 'personal') {
            $inventory = Inventory::where('owner_id', $user->id)->firstOrFail();
        } elseif ($currentType === 'group' && $currentGroupId) {
            $inventory = Inventory::where('group_id', $currentGroupId)->firstOrFail();
        } else {
            return redirect()->back()->with('error', '在庫スペースが見つかりません');
        }

        // カテゴリ（group_name）を inventory_categories に登録（または取得）
        $category = InventoryCategory::firstOrCreate([
            'inventory_id' => $inventory->id,
            'name' => $request->input('category_name'),
        ]);

        // アイテムを1つずつ inventory_items に登録
        foreach ($request->input('items') as $item) {
            InventoryItem::create([
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'description' => $item['memo'] ?? null,
                'purchase_date' => isset($item['has_purchase']) ? $item['purchase_date'] : null,
                'expiration_date' => isset($item['has_expiration']) ? $item['expiration_date'] : null,
                'category_id' => $category->id,
                'owner_id' => $currentType === 'personal'
                    ? $user->id
                    : ($item['owner_id'] ?? null),
            ]);
        }

        return redirect()->route('stock.index')->with('success', '在庫が登録されました');
    }

}
