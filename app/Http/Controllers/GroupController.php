<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('group.invitation', compact('results', 'group'));
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
    
        // すでにメンバーならスキップ
        $alreadyMember = DB::table('group_members')
            ->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();
    
        if ($alreadyMember) {
            return redirect()->route('group.invite')->with('info', 'すでにメンバーです');
        }
    
        // すでに招待されているかチェック（グループ×ユーザーで1件のみ取得）
        $existingInvite = GroupInvitation::where('group_id', $groupId)
            ->where('invitee_id', $userId)
            ->first();
    
        if ($existingInvite) {
            if ($existingInvite->status === 'pending' && is_null($existingInvite->responded_at)) {
                // ✅ 保留中 → updated_atだけ更新
                $existingInvite->touch();
            } else {
                // ✅ 辞退済みまたは以前に保留して非表示状態 → 再招待（responded_atリセット）
                $existingInvite->update([
                    'status' => 'pending',
                    'inviter_id' => $inviterId,
                    'responded_at' => null,
                ]);
            }
        } else {
            // ✅ 新規招待
            GroupInvitation::create([
                'group_id' => $groupId,
                'invitee_id' => $userId,
                'inviter_id' => $inviterId,
                'status' => 'pending',
            ]);
        }
    
        return redirect()->route('group.invite')->with('success', 'ユーザーを招待しました');
    }
    

    
    
    public function respond(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|exists:group_invitations,id',
            'response' => 'required|in:accept,decline',
        ]);
    
        $invitation = GroupInvitation::findOrFail($request->invitation_id);
    
        if ($invitation->status !== 'pending') {
            return redirect()->route('stock.index');
        }
    
        if ($request->response === 'accept') {
            DB::table('group_members')->insert([
                'group_id' => $invitation->group_id,
                'user_id' => $invitation->invitee_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $invitation->update(['status' => 'accepted', 'responded_at' => now()]);
        } else {
            // ❗辞退時にレコード削除
            $invitation->delete();
        }
    
        return redirect()->route('stock.index')->with('success', '対応しました');
    }
    
    
    // public function markViewed(Request $request)
    // {
    //     $request->validate([
    //         'invitation_ids' => 'required|array',
    //         'invitation_ids.*' => 'exists:group_invitations,id',
    //     ]);
    
    //     GroupInvitation::whereIn('id', $request->invitation_ids)
    //         ->where('invitee_id', Auth::id())
    //         ->update(['responded_at' => now()]);
    
    //     return redirect()->route('stock.index');
    // }
    public function markViewed(Request $request)
{
    $request->validate([
        'invitation_ids' => 'required|array',
        'invitation_ids.*' => 'exists:group_invitations,id',
    ]);

    $count = GroupInvitation::whereIn('id', $request->invitation_ids)
        ->where('invitee_id', Auth::id())
        ->update(['responded_at' => now()]);

    \Log::info("【保留処理】{$count} 件の responded_at を更新", $request->invitation_ids);

    // fetch対応 or redirect両方対応
    if ($request->expectsJson()) {
        return response()->json(['status' => 'ok', 'updated' => $count]);
    }

    return redirect()->route('stock.index');
}

    

    public function leave($groupId)
    {
        $user = Auth::user();
        $group = Group::findOrFail($groupId);
    
        // 🔄 ユーザーのスペースセッションが現在このグループだった場合は、後で切り替える
        $isCurrentGroup = session('current_type') === 'group' && session('current_group_id') == $groupId;
    
        // グループ内アイテムの owner_id を null に
        $inventory = Inventory::where('group_id', $group->id)->first();
        if ($inventory) {
            InventoryItem::whereHas('category', function ($query) use ($inventory) {
                $query->where('inventory_id', $inventory->id);
            })
            ->where('owner_id', $user->id)
            ->update(['owner_id' => null]);
        }
    
        // グループ脱退
        $group->users()->detach($user->id);
    
        // ✅ セッションがこのグループを指していたら、次のスペースに切り替え
        if ($isCurrentGroup) {
            // 他の個人スペース優先で取得
            $nextPersonal = Inventory::where('owner_id', $user->id)->orderBy('id')->first();
    
            if ($nextPersonal) {
                session([
                    'current_type' => 'personal',
                    'current_inventory_id' => $nextPersonal->id,
                    'current_group_id' => null,
                ]);
            } else {
                // 所属中の別のグループがあれば切り替え
                $otherGroupIds = $user->groups()->pluck('groups.id')->toArray();
                $nextGroupInventory = Inventory::whereIn('group_id', $otherGroupIds)->orderBy('id')->first();
    
                if ($nextGroupInventory) {
                    session([
                        'current_type' => 'group',
                        'current_group_id' => $nextGroupInventory->group_id,
                        'current_inventory_id' => null,
                    ]);
                } else {
                    // 他にスペースがない場合はセッション初期化
                    session()->forget(['current_inventory_id', 'current_group_id', 'current_type']);
                }
            }
        }
    
        return redirect()->route('stock.index')->with('success', 'グループから脱退しました');
    }
    
    
    public function create()
    {
        return view('group.create'); // フォーム表示用
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:personal,group',
            'name' => 'required|string|max:255',
        ]);
    
        $user = auth()->user();
    
        // ✅ 合計スペース数チェック（個人 + グループ）
        $totalSpaces = Inventory::where('owner_id', $user->id)->count() + $user->groups()->count();
        if ($totalSpaces >= 5) {
            return redirect()->route('stock.index')->with('error', 'スペースは最大5つまでです。');
        }
    
        if ($request->type === 'personal') {
            \Log::info('個人スペース作成開始');
    
            $inventory = Inventory::create([
                'owner_id' => $user->id,
                'group_id' => null,
                'name' => $request->name,
            ]);
    
            // ✅ 作成した個人スペースをセッションに設定
            session([
                'current_type' => 'personal',
                'current_inventory_id' => $inventory->id,
                'current_group_id' => null
            ]);
    
            \Log::info('個人スペース作成完了');
    
        } elseif ($request->type === 'group') {
            $group = Group::create([
                'name' => $request->name,
                'description' => $request->name . 'の在庫管理グループ',
                'invite_only' => true,
                'max_members' => 30,
            ]);
    
            $group->users()->attach($user->id);
    
            $inventory = Inventory::create([
                'group_id' => $group->id,
                'owner_id' => null,
                'name' => $request->name . 'の在庫',
            ]);
    
            // ✅ 作成したグループスペースをセッションに設定
            session([
                'current_type' => 'group',
                'current_group_id' => $group->id,
                'current_inventory_id' => null
            ]);
        }
    
        return redirect()->route('stock.index')->with('success', 'スペースを作成しました');
    }
    

    public function destroy(Inventory $inventory)
    {
        $user = auth()->user();
    
        // オーナー確認
        if ($inventory->owner_id !== $user->id) {
            abort(403, 'このスペースを削除する権限がありません');
        }
    
        $inventoryId = $inventory->id;
    
        // 削除実行
        $inventory->delete();
    
        // セッションのスペースが今削除したスペースと一致するなら切り替え
        if (session('current_inventory_id') == $inventoryId) {
    
            // 1. 他の個人スペース取得
            $nextPersonal = Inventory::where('owner_id', $user->id)->orderBy('id')->first();
    
            // 2. なければ所属グループの在庫を探す
            if (!$nextPersonal) {
                $groupIds = $user->groups()->pluck('groups.id')->toArray();
    
                $nextGroupInventory = Inventory::whereIn('group_id', $groupIds)->orderBy('id')->first();
    
                if ($nextGroupInventory) {
                    session([
                        'current_type' => 'group',
                        'current_group_id' => $nextGroupInventory->group_id,
                        'current_inventory_id' => null
                    ]);
                } else {
                    // スペースがもう何もない場合
                    session()->forget(['current_inventory_id', 'current_group_id', 'current_type']);
                }
    
            } else {
                // 次の個人スペースに切り替え
                session([
                    'current_type' => 'personal',
                    'current_inventory_id' => $nextPersonal->id,
                    'current_group_id' => null
                ]);
            }
        }
    
        return redirect()->route('stock.index')->with('success', 'スペースを削除しました');
    }
    




}
