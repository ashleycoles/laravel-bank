<?php

namespace App\Projectors;

use App\Events\AccountCreated;
use App\Events\FundsDeposited;
use App\Events\FundsWithdrawn;
use App\Events\OverdraftLimitUpdated;
use App\Models\Account;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AccountProjector extends Projector
{
    public function onAccountCreated(AccountCreated $event): void
    {
        Account::create([
            'firstname' => $event->firstname,
            'lastname' => $event->lastname,
            'uuid' => $event->aggregateRootUuid(),
        ]);
    }

    public function onAccountDeposit(FundsDeposited $event): void
    {
        $account = Account::uuid($event->uuid);
        $account->balance += $event->amount;
        $account->save();
    }

    public function onAccountWithdraw(FundsWithdrawn $event): void
    {
        $account = Account::uuid($event->uuid);
        $account->balance -= $event->amount;
        $account->save();
    }

    public function onOverdraftLimitUpdated(OverdraftLimitUpdated $event): void
    {
        $account = Account::uuid($event->uuid);
        $account->overdraft = $event->limit;
        $account->save();
    }
}
