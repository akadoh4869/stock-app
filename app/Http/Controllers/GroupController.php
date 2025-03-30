<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupInvitation;
use App\Events\InvitationSent;
use App\Models\Inventory;
use App\Models\InventoryItem;

class GroupController extends Controller
{
    //
    public function invite(Request $request)
    {
        $keyword = $request->input('keyword');

        $results = [];

        if ($keyword) {
            $results = User::where('user_name', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->get();
        }

        // 招待対象のグループ（セッションなどから取得する例）
        $groupId = session('current_group_id');
        $group = Group::findOrFail($groupId);

        return view('users.invitation', compact('results', 'group'));
    }

    public function sendInvite(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
        ]);
    
        $userId = $request->input('user_id');
        $groupId = $request->input('group_id');
        $inviterId = Auth::id();
    
        $alreadyMember = \DB::table('group_members')
            ->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();
    
        if ($alreadyMember) {
            return redirect()->route('group.invite')->with('info', 'すでにメンバーです');
        }
    
        $existingInvitation = GroupInvitation::where('group_id', $groupId)
            ->where('invitee_id', $userId)
            ->first();
    
        if ($existingInvitation) {
            $existingInvitation->update([
                'status' => 'pending',
                'inviter_id' => $inviterId,
            ]);
    
            // ✅ 再送でもイベント発火
            // event(new InvitationSent($existingInvitation));
        } else {
            $invitation = GroupInvitation::create([
                'group_id' => $groupId,
                'invitee_id' => $userId,
                'inviter_id' => $inviterId,
                'status' => 'pending',
            ]);
    
            // ✅ 新規招待でもイベント発火
            // event(new InvitationSent($invitation));
        }
    
        return redirect()->route('group.invite')->with('success', 'ユーザーを招待しました');
    }
    
    public function respond(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|exists:group_invitations,id',
            'response' => 'required|in:accept,decline',
        ]);

        $invitation = \App\Models\GroupInvitation::findOrFail($request->invitation_id);

        // すでに対応済みならスルー
        if ($invitation->status !== 'pending') { // ← 修正点
            return redirect()->route('stock.index');
        }

        if ($request->response === 'accept') {
            // グループ参加処理
            \DB::table('group_members')->insert([
                'group_id' => $invitation->group_id,
                'user_id' => $invitation->invitee_id, // ← user_id → invitee_id に修正！
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $invitation->update(['status' => 'accepted']); // ← 修正点
        } else {
            $invitation->update(['status' => 'declined']); // ← 修正点
        }

        return redirect()->route('stock.index')->with('success', '対応しました');
    }

    public function leave($groupId)
    {
        $user = Auth::user();
        $group = Group::findOrFail($groupId);
    
        // 脱退前にそのユーザーが所有しているグループ内アイテムの owner_id を null に変更
        $inventory = Inventory::where('group_id', $group->id)->first();
        if ($inventory) {
            InventoryItem::whereHas('category', function ($query) use ($inventory) {
                $query->where('inventory_id', $inventory->id);
            })
            ->where('owner_id', $user->id)
            ->update(['owner_id' => null]);
        }
    
        // グループとの関連を解除
        $group->users()->detach($user->id);
    
        return redirect()->route('stock.index')->with('success', 'グループから脱退しました');
    }
    



}
