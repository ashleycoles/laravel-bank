<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TransactionSent extends ShouldBeStored
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $amount) {}
}
