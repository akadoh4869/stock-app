<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\User;

class GroupMembersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 各グループに所属するメンバーリスト
        $groupMembers = [
            'KAT-TUN' => ['ほだか', '赤西', '亀梨'],
            '内山家' => ['ほだか', 'きみえ', 'まゆ'],
            'H&Y' => ['ほだか', 'ゆちょ']
        ];

        foreach ($groupMembers as $groupName => $members) {
            // グループを取得
            $group = Group::where('name', $groupName)->first();

            foreach ($members as $memberName) {
                // ユーザーを取得
                $user = User::where('user_name', $memberName)->first();

                // グループとユーザーが存在する場合のみ追加
                if ($group && $user) {
                    DB::table('group_members')->insert([
                        'group_id' => $group->id,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
