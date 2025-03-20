<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('groups')->insert([
            [
                'name' => 'KAT-TUN',
                'description' => 'KAT-TUN の在庫管理グループ',
                'invite_only' => true,
                'max_members' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '内山家',
                'description' => '内山家の共有在庫',
                'invite_only' => true,
                'max_members' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'H&Y',
                'description' => 'H&Y の在庫管理グループ',
                'invite_only' => true,
                'max_members' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
