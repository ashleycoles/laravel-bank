<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FundsWithdrawn extends \Spatie\EventSourcing\StoredEvents\ShouldBeStored
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $uuid, public int $amount)
    {}
}
