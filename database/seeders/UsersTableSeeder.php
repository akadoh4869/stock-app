<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            ['name' => 'ほだか', 'user_name' => 'ほだか', 'email' => 'hodaka@admin.com', 'password' => Hash::make('hodaka'), 'is_admin' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ゆちょ', 'user_name' => 'ゆちょ', 'email' => 'yucho@user.com', 'password' => Hash::make('yucho'), 'is_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '亀梨', 'user_name' => '亀梨', 'email' => 'kamenashi@user.com', 'password' => Hash::make('kamenashi'), 'is_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '赤西', 'user_name' => '赤西', 'email' => 'akanishi@user.com', 'password' => Hash::make('akanishi'), 'is_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'きみえ', 'user_name' => 'きみえ', 'email' => 'kimie@user.com', 'password' => Hash::make('kimie'), 'is_admin' => false, 'created_at' => now(), 'updated_at' => now()],  
            ['name' => 'まゆ', 'user_name' => 'まゆ', 'email' => 'mayu@user.com', 'password' => Hash::make('mayu'), 'is_admin' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

    }
}
