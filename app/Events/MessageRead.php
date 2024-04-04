<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation_id;
    public $receiverId;
    public function __construct($conversation_id, $receiverId)
    {
        //
        $this->conversation_id=$conversation_id;
        $this->receiverId = $receiverId;
    }

    public function broadcastWith(){
        return [
            'conversation_id' => $this->conversation_id,
        ];
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->receiverId),
        ];
    }
}
