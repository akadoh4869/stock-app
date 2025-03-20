<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryCategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 グループごとのカテゴリ
        $groupCategories = [
            'KAT-TUN' => ['衣装', 'CD'],
            '内山家' => ['食品', '日用品'],
            'H&Y' => ['カメラ', 'お酒', 'アクセサリ']
        ];

        // グループ用のカテゴリを追加
        foreach ($groupCategories as $groupName => $categories) {
            $inventory = DB::table('inventories')->where('name', "{$groupName}の共有在庫")->first();

            if ($inventory) {
                foreach ($categories as $categoryName) {
                    DB::table('inventory_categories')->insert([
                        'name' => $categoryName,
                        'inventory_id' => $inventory->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

    }
}
