<?php

namespace App\Projectors;

use App\Events\AccountCreated;
use App\Models\Account;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AccountProjector extends Projector
{
    public function onAccountCreated(AccountCreated $event): void
    {
        Account::create($event->accountAttributes);
    }
}
