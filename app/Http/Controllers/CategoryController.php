<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
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
            'name' => 'required|string|max:255',
            'inventory_id' => 'required|exists:inventories,id',
        ]);

        InventoryCategory::create([
            'name' => $request->name,
            'inventory_id' => $request->inventory_id,
        ]);

        return redirect()->back()->with('success', 'カテゴリを追加しました');
    }

    public function showItems($id)
    {
        $user = Auth::user();
        $category = InventoryCategory::with(['items.owner'])->findOrFail($id);
    
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
            'currentGroup' => $currentGroup, // ← 追加
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
    
        // ソフトデリートされたカテゴリとその中のアイテム（ソフトデリートされたもの）を取得
        $deletedCategories = InventoryCategory::onlyTrashed()
            ->where('inventory_id', $inventory->id)
            ->with(['items' => function ($query) {
                $query->onlyTrashed();
            }])
            ->get();
    
        return view('category.history', compact('deletedCategories'));
    }
    

    public function restore($id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);

        // カテゴリ復元
        $category->restore();

        // 紐づくアイテムも復元
        InventoryItem::onlyTrashed()
            ->where('category_id', $category->id)
            ->restore();

        return redirect()->route('category.history')->with('success', 'カテゴリとアイテムを復元しました');
    }


}
