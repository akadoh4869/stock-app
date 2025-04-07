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


class CategoryController extends Controller
{
    //

    public function create()
    {
        $user = Auth::user();
        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');

        $currentGroup = null;
        $groupUsers = [];

        if ($currentType === 'group' && $currentGroupId) {
            $currentGroup = Group::with('users')->find($currentGroupId);
            $groupUsers = $currentGroup ? $currentGroup->users : [];
        }

        return view('category.create', [
            'currentType' => $currentType,
            'currentGroup' => $currentGroup,
        ]);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255', // ← 修正ここ
            'inventory_id' => 'required|integer|exists:inventories,id',
        ]);
    
        $user = Auth::user();
        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');
    
        // ✅ inventory_id を取得
        if ($currentType === 'group' && $currentGroupId) {
            $inventory = Inventory::where('group_id', $currentGroupId)->first();
        } else {
            $inventory = Inventory::where('owner_id', $user->id)->whereNull('group_id')->first();
        }
    
        if (!$inventory) {
            return back()->with('error', '在庫が見つかりませんでした。');
        }
    
        // ✅ カテゴリ作成
        $category = InventoryCategory::create([
            'name' => $request->category_name, // ← 修正ここ
            'inventory_id' => $inventory->id,
        ]);
    
        if ($request->has('items')) {
            foreach ($request->items as $index => $itemData) {
                if (empty($itemData['name'])) {
                    continue;
                }
    
                $item = InventoryItem::create([
                    'name' => $itemData['name'],
                    'expiration_date' => !empty($itemData['has_expiration']) ? $itemData['expiration_date'] ?? null : null,
                    'purchase_date' => !empty($itemData['has_purchase']) ? $itemData['purchase_date'] ?? null : null,
                    'quantity' => $itemData['quantity'] ?? 1,
                    'description' => $itemData['memo'] ?? '',
                    'owner_id' => $itemData['owner_id'] ?? null,
                    'category_id' => $category->id,
                ]);
    
                // ✅ 画像保存
                $imageFile = $request->file("items.$index.image");
                if ($imageFile && $imageFile->isValid()) {
                    $path = $imageFile->store('item_images', 'public');
    
                    InventoryItemImage::create([
                        'item_id' => $item->id,
                        'image_path' => $path,
                    ]);
                }
            }
        }
    
        return redirect()->route('stock.index')->with('success', 'カテゴリを作成しました');
        
    }
    
    public function showItems($id)
    {
        $user = Auth::user();
        
        // ✅ images を eager load
        $category = InventoryCategory::with(['items.owner', 'items.images'])->findOrFail($id);

        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');
        $currentGroup = null;

        if ($currentType === 'group' && $currentGroupId) {
            $currentGroup = $user->groups()->where('groups.id', $currentGroupId)->first();
        }

        return view('category.items', [
            'category' => $category,
            'items' => $category->items,
            'currentType' => $currentType,
            'currentGroup' => $currentGroup,
        ]);
    }


    public function destroy($id)
    {
        $category = InventoryCategory::with('items')->findOrFail($id);

        // 関連するアイテムもソフトデリート
        foreach ($category->items as $item) {
            $item->delete();
        }

        // カテゴリもソフトデリート
        $category->delete();

        return redirect()->route('stock.index')->with('success', 'カテゴリとそのアイテムを削除しました');
    }

    public function history()
    {
        $user = Auth::user();

        // セッションから現在のスペース情報を取得
        $currentType = session('current_type');
        $currentGroupId = session('current_group_id');

        // 在庫情報を取得（個人 or グループ）
        if ($currentType === 'personal') {
            $inventory = Inventory::where('owner_id', $user->id)
                ->whereNull('group_id')
                ->first();
        } elseif ($currentType === 'group' && $currentGroupId) {
            $group = $user->groups()->where('groups.id', $currentGroupId)->first();
            $inventory = $group?->inventory;
        } else {
            $inventory = null;
        }

        // 在庫が見つからなければ戻る
        if (!$inventory) {
            return back()->with('error', '在庫情報が見つかりません');
        }

        // ソフトデリートされたカテゴリとその中のアイテム
        $deletedCategories = InventoryCategory::onlyTrashed()
            ->where('inventory_id', $inventory->id)
            ->with(['items' => function ($query) {
                $query->onlyTrashed();
            }])
            ->get();

        // 現在のカテゴリ数（復元されていないカテゴリ）
        $currentCategoryCount = InventoryCategory::where('inventory_id', $inventory->id)->count();

        return view('category.history', compact('deletedCategories', 'currentCategoryCount'));
    }

    public function restore($id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);

        // 復元対象のカテゴリが属する inventory_id を取得
        $inventoryId = $category->inventory_id;

        // 現在のカテゴリ数を取得（削除されていないものだけ）
        $currentCategoryCount = InventoryCategory::where('inventory_id', $inventoryId)->count();

        if ($currentCategoryCount >= 5) {
            return redirect()->route('category.history')->with('error', 'カテゴリは最大5つまでです。1つ削除してから復元してください。');
        }

        // カテゴリ復元
        $category->restore();

        // 紐づくアイテムも復元
        InventoryItem::onlyTrashed()
            ->where('category_id', $category->id)
            ->restore();

        return redirect()->route('category.history')->with('success', 'カテゴリとアイテムを復元しました');
    }

    public function forceDelete($id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);

        // 紐づくアイテムも物理削除
        InventoryItem::onlyTrashed()
            ->where('category_id', $category->id)
            ->forceDelete();

        // カテゴリを物理削除
        $category->forceDelete();

        return redirect()->route('category.history')->with('success', 'カテゴリとアイテムを完全に削除しました');
    }

    public function historyByCategory($categoryId)
    {
        $category = InventoryCategory::withTrashed()->findOrFail($categoryId); // ← 修正点

        $deletedItems = InventoryItem::onlyTrashed()
            ->where('category_id', $categoryId)
            ->get();

        return view('category.history', [
            'deletedCategories' => collect([$category]),
            'currentCategoryCount' => 0,
            'filteredItemOnly' => true,
            'deletedItems' => $deletedItems,
        ]);
    }

    // CategoryController.php
    public function updateName(Request $request, $id)
    {
        $category = InventoryCategory::findOrFail($id);
        $category->name = $request->input('name');
        $category->save();

        return response()->json(['success' => true]);
    }





}
