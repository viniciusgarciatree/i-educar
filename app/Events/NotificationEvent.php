<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * @var string
     */
    private $tenant;

    /**
     * Create a new event instance.
     *
     * @param Notification $notification
     * @param string       $tenant
     */
    public function __construct(Notification $notification, $tenant)
    {
        $this->notification = $notification;
        $this->tenant = $tenant;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->tenant . '-notification-' . md5($this->notification->user_id));
    }

    public function tags()
    {
        return [
            $this->tenant,
            'notification',
            $this->notification->type->name,
        ];
    }
}
