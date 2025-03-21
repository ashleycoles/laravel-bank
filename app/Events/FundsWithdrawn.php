<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FundsWithdrawn extends \Spatie\EventSourcing\StoredEvents\ShouldBeStored
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $amount) {}
}
