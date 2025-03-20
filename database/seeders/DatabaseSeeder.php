<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            GroupsTableSeeder::class,
            GroupMembersTableSeeder::class, // ✅ ここを追加
            GroupInvitationsTableSeeder::class, // ✅ ここを追加
            InventoriesTableSeeder::class,
            InventoryCategoriesTableSeeder::class,
            InventoryItemsTableSeeder::class,
            SubscriptionsTableSeeder::class,
        ]);
    }
}
