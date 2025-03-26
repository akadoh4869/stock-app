<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryCategory;


class CategoryController extends Controller
{
    //
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
        $category = InventoryCategory::with('items')->findOrFail($id);

        return view('category.items', [
            'category' => $category,
            'items' => $category->items,
        ]);
    }


}
