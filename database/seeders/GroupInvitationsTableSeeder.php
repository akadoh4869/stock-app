<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupInvitationsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 招待データの準備
        $invitations = [
            // KAT-TUN の招待
            ['group_name' => 'KAT-TUN', 'inviter' => 'ほだか', 'invitee' => '赤西'],
            ['group_name' => 'KAT-TUN', 'inviter' => 'ほだか', 'invitee' => '亀梨'],

            // 内山家 の招待
            ['group_name' => '内山家', 'inviter' => 'ほだか', 'invitee' => 'きみえ'],
            ['group_name' => '内山家', 'inviter' => 'ほだか', 'invitee' => 'まゆ'],

            // H&Y の招待
            ['group_name' => 'H&Y', 'inviter' => 'ほだか', 'invitee' => 'ゆちょ']
        ];

        foreach ($invitations as $invitation) {
            $group = DB::table('groups')->where('name', $invitation['group_name'])->first();
            $inviter = DB::table('users')->where('user_name', $invitation['inviter'])->first();
            $invitee = DB::table('users')->where('user_name', $invitation['invitee'])->first();

            if ($group && $inviter && $invitee) {
                DB::table('group_invitations')->insert([
                    'group_id' => $group->id,
                    'inviter_id' => $inviter->id,
                    'invitee_id' => $invitee->id,
                    'status' => 'accepted', // すでに承認済みの状態
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
