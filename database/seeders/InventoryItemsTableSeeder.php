<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 グループごとのアイテムリスト
        $groupItems = [
            '衣装' => ['赤いジャケット', '黒い帽子'],
            'CD' => ['アルバムX', 'シングルY'],
            '食品' => ['米', 'パン'],
            '日用品' => ['洗剤', 'ティッシュ'],
            'カメラ' => ['Canon EOS R5', 'Sony A7 IV'],
            'お酒' => ['ウイスキー', 'ビール'],
            'アクセサリ' => ['ネックレス', '腕時計']
        ];

        // カテゴリごとにアイテムを追加
        foreach ($groupItems as $categoryName => $items) {
            $category = DB::table('inventory_categories')->where('name', $categoryName)->first();

            if ($category) {
                foreach ($items as $itemName) {
                    DB::table('inventory_items')->insert([
                        'name' => $itemName,
                        'category_id' => $category->id,
                        'quantity' => rand(1, 10),
                        'description' => "{$itemName} の説明",
                        'purchase_date' => now()->subDays(rand(1, 30)),
                        'expiration_date' => now()->addDays(rand(10, 90)),
                        'owner_id' => null, // グループ共有アイテムなので owner_id は NULL
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
