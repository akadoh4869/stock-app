<?php

namespace App\Events;

use App\Models\GroupInvitation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // ←即時ブロードキャスト推奨
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;

    public function __construct(GroupInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function broadcastOn()
    {
        // 招待されたユーザーの個別チャンネル
        return new PrivateChannel('user.' . $this->invitation->invitee_id);
    }

    public function broadcastWith()
    {
        return [
            'invitation_id' => $this->invitation->id, // ✅ 追加！
            'group_name' => $this->invitation->group->name,
            'message' => 'グループに招待されました',
        ];
    }
    

    public function broadcastAs()
    {
        return 'InvitationSent';
    }
}
