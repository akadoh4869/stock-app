<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryItemImage;
use App\Models\GroupInvitation;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }
    
        // セッション初期化（最初の訪問時のみ）
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
        $currentGroup = $currentType === 'group' && !empty($currentGroupId)
            ? $groups->firstWhere('id', $currentGroupId)
            : null;
    
        // 現在の在庫
        $inventory = null;
        if ($currentType === 'personal' && $currentInventoryId) {
            $inventory = Inventory::find($currentInventoryId);
        } elseif ($currentType === 'group' && $currentGroupId) {
            $inventory = Inventory::where('group_id', $currentGroupId)->first();
        }
    
        // カテゴリ取得
        $categories = $inventory
            ? InventoryCategory::where('inventory_id', $inventory->id)->get()
            : [];
    
        // 個人スペース一覧
        $personalInventories = Inventory::where('owner_id', $user->id)->get();
    
        // ✅ 招待一覧（未対応のものだけ）
        $pendingInvitations = GroupInvitation::where('invitee_id', Auth::id())
        ->where('status', 'pending')
        ->whereNull('responded_at') // ← これがないと再表示されます！
        ->orderByDesc('updated_at')
        ->with('group')
        ->get();
    
        // ✅ 所属スペースの合計数（上限判断用）
        $groupCount = DB::table('group_members')
            ->where('user_id', $user->id)
            ->count();
    
        $personalCount = $personalInventories->count();
    
        $totalSpaceCount = $groupCount + $personalCount;
    
        return view('users.top', [
            'user' => $user,
            'groups' => $groups,
            'personalInventories' => $personalInventories,
            'currentGroup' => $currentGroup,
            'categories' => $categories,
            'currentType' => $currentType,
            'inventory' => $inventory,
            'pendingInvitations' => $pendingInvitations,
            'totalSpaceCount' => $totalSpaceCount,
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
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory_categories,id',
            'quantity' => 'nullable|integer|min:1',
            'expiration_date' => 'nullable|date',
            'purchase_date' => 'nullable|date',
            'owner_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // アイテム作成
        $item = InventoryItem::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity ?? 1,
            'expiration_date' => $request->expiration_date,
            'purchase_date' => $request->purchase_date,
            'description' => $request->description,
            'owner_id' => $request->owner_id,
        ]);

        // 画像保存（1枚のみ対応）
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('item_images', 'public');

            InventoryItemImage::create([
                'item_id' => $item->id, // ← テーブル設計に合わせて
                'image_path' => $path,
            ]);
        }

        // 関連データを読み込む（owner, images）
        $item->load(['owner:id,user_name', 'images']);

        return response()->json($item);
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

    public function uploadImage(Request $request, InventoryItem $item)
{
    $request->validate([
        'image' => 'required|image|max:2048',
    ]);

    // 古い画像削除（1枚制限）
    foreach ($item->images as $img) {
        Storage::disk('public')->delete($img->image_path);
        $img->delete();
    }

    // 新しい画像保存
    $path = $request->file('image')->store('item_images', 'public');

    InventoryItemImage::create([
        'item_id' => $item->id,  // ← カラム名に注意！（`item_id` ではなく `inventory_item_id`）
        'image_path' => $path,
    ]);

    // ✅ JSON形式で返す
    return response()->json([
        'message' => '画像アップロード成功',
        'image_path' => $path,
    ]);
}
    

    
    public function deleteImage(InventoryItemImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
    
        return back();
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

    public function bulkStore(Request $request)
    {
        $categoryId = $request->input('category_id');
        $items = $request->input('items', []);

        foreach ($items as $data) {
            if (!empty($data['name'])) {
                InventoryItem::create([
                    'category_id'    => $categoryId,
                    'name'           => $data['name'],
                    'quantity'       => $data['quantity'] ?? 1,
                    'expiration_date'=> $data['expiration_date'] ?? null,
                    'purchase_date'  => $data['purchase_date'] ?? null,
                    'description'    => $data['memo'] ?? null,
                    'owner_id'       => $data['owner_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('stock.index', ['category' => $categoryId])
            ->with('success', 'ストックを一括作成しました');
    }
    

}
