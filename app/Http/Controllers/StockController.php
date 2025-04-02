<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory_categories,id',
            'quantity' => 'nullable|integer|min:1',
            'expiration_date' => 'nullable|date',
            'purchase_date' => 'nullable|date',
            'owner_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048', // ← photo → image に修正
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
                'item_id' => $item->id, // ← item_id ではなく inventory_item_id に合わせる
                'image_path' => $path,
            ]);
        }
    
        return response()->json([
            'message' => 'アイテムを保存しました',
            'id' => $item->id,
        ]);
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
    
        // 新しい画像を保存
        $path = $request->file('image')->store('item_images', 'public');
        InventoryItemImage::create([
            'item_id' => $item->id,
            'image_path' => $path,
        ]);
    
        return back()->with('success', '画像を更新しました');
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
    
    

}
