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
        $data = $request->json()->all();
    
        $item = new InventoryItem();
        $item->fill([
            'name' => $data['name'] ?? '',
            'expiration_date' => $data['expiration_date'] ?? null,
            'purchase_date' => $data['purchase_date'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'description' => $data['description'] ?? '',
            'owner_id' => $data['owner_id'] ?? null,
            'category_id' => $data['category_id'],
        ]);
        $item->save();
    
        return response()->json(['message' => '作成成功', 'id' => $item->id]);
    }
    

    public function update(Request $request, InventoryItem $item)
    {
        $data = $request->json()->all(); // fetchからのJSONを受け取る
    
        $fields = ['name', 'expiration_date', 'purchase_date', 'quantity', 'description'];
    
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $item->$field = $data[$field];
            }
        }
    
        $item->save();
    
        return response()->json(['message' => '保存成功']);
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete(); // ソフトデリート
    
        return response()->json(['message' => '削除成功']);
    }

    public function history()
    {
        $deletedItems = InventoryItem::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('items.history', compact('deletedItems'));
    }
    
    

}
