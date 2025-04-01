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

        // セッション初期化
        if (!session()->has('current_type')) {
            session([
                'current_type' => 'personal',
                'current_inventory_id' => Inventory::where('owner_id', $user->id)->first()?->id,
                'current_group_id' => null
            ]);
        }

        $currentType = session('current_type');
        $currentInventoryId = session('current_inventory_id');
        $currentGroupId = session('current_group_id');

        // 所属グループ取得
        $groups = $user->groups()->get();

        // 現在のグループ
        $currentGroup = null;
        if ($currentType === 'group' && !empty($currentGroupId)) {
            $currentGroup = $groups->firstWhere('id', $currentGroupId);
        }

        // 現在の在庫
        $inventory = null;
        if ($currentType === 'personal' && $currentInventoryId) {
            $inventory = Inventory::find($currentInventoryId);
        } elseif ($currentType === 'group' && $currentGroupId) {
            $inventory = Inventory::where('group_id', $currentGroupId)->first();
        }

        // カテゴリ取得
        $categories = [];
        if ($inventory) {
            $categories = InventoryCategory::where('inventory_id', $inventory->id)->get();
        }

        // 個人スペース一覧
        $personalInventories = Inventory::where('owner_id', $user->id)->get();

        // 招待取得
        $pendingInvitations = GroupInvitation::where('invitee_id', $user->id)
            ->where('status', 'pending')
            ->with('group')
            ->get();

        return view('users.top', [
            'user' => $user,
            'groups' => $groups,
            'personalInventories' => $personalInventories,
            'currentGroup' => $currentGroup,
            'categories' => $categories,
            'currentType' => $currentType,
            'inventory' => $inventory,
            'pendingInvitations' => $pendingInvitations
        ]);
    }

    public function switchSpace(Request $request)
    {
        $type = $request->type;

        if ($type === 'personal') {
            session([
                'current_type' => 'personal',
                'current_inventory_id' => $request->inventoryId,
                'current_group_id' => null
            ]);
        } elseif ($type === 'group' && $request->groupId) {
            session([
                'current_type' => 'group',
                'current_group_id' => $request->groupId,
                'current_inventory_id' => null
            ]);
        }

        return redirect()->route('stock.index');
    }


    public function store(Request $request)
{
    $user = auth()->user();

    // カテゴリ作成
    $category = InventoryCategory::create([
        'name' => $request->input('category_name'),
        'inventory_id' => Inventory::where('owner_id', $user->id)->first()->id // ← 必要に応じて調整
    ]);

    // 各アイテム処理
    foreach ($request->input('items', []) as $index => $itemData) {
        if (empty($itemData['name'])) continue; // name がないアイテムは無視

        $item = new InventoryItem();
        $item->fill([
            'name' => $itemData['name'],
            'expiration_date' => !empty($itemData['has_expiration']) ? $itemData['expiration_date'] ?? null : null,
            'purchase_date' => !empty($itemData['has_purchase']) ? $itemData['purchase_date'] ?? null : null,
            'quantity' => $itemData['quantity'] ?? 1,
            'description' => $itemData['memo'] ?? '',
            'owner_id' => $itemData['owner_id'] ?? null,
            'category_id' => $category->id,
        ]);
        $item->save();

        // 画像があれば保存
        $imageKey = "items.$index.image"; // フォームから送られたファイル名と一致
        if ($request->hasFile($imageKey)) {
            $path = $request->file($imageKey)->store('item_images', 'public');

            \App\Models\InventoryItemImage::create([
                'inventory_item_id' => $item->id,
                'image_path' => $path,
            ]);
        }
    }

    return redirect()->route('stock.index')->with('success', 'カテゴリとアイテムを作成しました');
}


    public function update(Request $request, InventoryItem $item)
    {
        $data = $request->json()->all(); // fetchからのJSONを受け取る
    
        $fields = ['name', 'expiration_date', 'purchase_date', 'quantity', 'description', 'owner_id'];

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
    
    public function restore($id)
    {
        $item = InventoryItem::onlyTrashed()->findOrFail($id);
        $item->restore();
    
        return back()->with('success', 'アイテムを復元しました');
    }
    
    public function forceDelete($id)
    {
        $item = InventoryItem::onlyTrashed()->findOrFail($id);
        $item->forceDelete();
    
        return back()->with('success', 'アイテムを完全に削除しました');
    }
    
    

}
