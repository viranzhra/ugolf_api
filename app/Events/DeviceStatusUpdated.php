<?php

namespace App\Events;

use App\Models\Terminal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $terminal;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('terminal.' . $this->terminal->id);
    }
}
