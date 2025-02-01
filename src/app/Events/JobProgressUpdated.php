<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobProgressUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $progress;
    public $path;
    public $nameFile;

    /**
     * Create a new event instance.
     */
    public function __construct($progress, $path = null, $nameFile = null)
    {
        $this->progress = $progress;
        if ($path) {
            $this->path = $path;
        }
        if ($nameFile) {
            $this->nameFile = $nameFile;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('channel-name'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'progress' => $this->progress,
            'path' => $this->path,
            'nameFile' => $this->nameFile,
        ];
    }
}
