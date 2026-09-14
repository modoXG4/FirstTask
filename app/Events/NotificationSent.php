<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // أو ShouldBroadcast
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $taskId;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $message, $taskId=null)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->taskId =$taskId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // بث التنبيه على قناة خاصة بالمسخدم المشتغل حالياً
        return [
            new PrivateChannel('App.Models.User.' . $this->userId),
        ];
    }

    /**
     * اسم الحدث الذي سيستمع له الفرونت إند
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }
}