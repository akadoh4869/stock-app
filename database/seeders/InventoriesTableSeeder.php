<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 個人用在庫スペースの作成
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            DB::table('inventories')->insert([
                'name' => "{$user->user_name}の個人在庫",
                'owner_id' => $user->id,
                'group_id' => null, // 個人用なので group_id は NULL
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 🚀 グループ用在庫スペースの作成
        $groups = DB::table('groups')->get();

        foreach ($groups as $group) {
            DB::table('inventories')->insert([
                'name' => "{$group->name}の共有在庫",
                'owner_id' => null, // グループ用なので owner_id は NULL
                'group_id' => $group->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
