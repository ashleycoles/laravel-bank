<?php

namespace App\Projectors;

use App\Events\AccountCreated;
use App\Events\FundsDeposited;
use App\Events\FundsWithdrawn;
use App\Events\OverdraftLimitUpdated;
use App\Events\TransactionReceived;
use App\Events\TransactionSent;
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

    public function onFundsDeposited(FundsDeposited $event): void
    {
        $this->handleBalanceAdd($event->aggregateRootUuid(), $event->amount);
    }

    public function onFundsWithdrawn(FundsWithdrawn $event): void
    {
        $this->handleBalanceSubtract($event->aggregateRootUuid(), $event->amount);
    }

    public function onTransactionSent(TransactionSent $event): void
    {
        $this->handleBalanceSubtract($event->aggregateRootUuid(), $event->amount);
    }

    public function onTransactionReceived(TransactionReceived $event): void
    {
        $this->handleBalanceAdd($event->aggregateRootUuid(), $event->amount);
    }

    public function onOverdraftLimitUpdated(OverdraftLimitUpdated $event): void
    {
        $account = Account::uuid($event->aggregateRootUuid());

        if (! $account) {
            throw new \RuntimeException('Account not found for UUID: '.$event->aggregateRootUuid());
        }

        $account->overdraft = $event->limit;
        $account->save();
    }

    private function handleBalanceSubtract(?string $uuid, int $amount): void
    {
        $account = Account::uuid($uuid);

        if (! $account) {
            throw new \RuntimeException('Account not found for UUID: '.$uuid);
        }

        $account->balance -= $amount;
        $account->save();
    }

    private function handleBalanceAdd(?string $uuid, int $amount): void
    {
        $account = Account::uuid($uuid);

        if (! $account) {
            throw new \RuntimeException('Account not found for UUID: '.$uuid);
        }

        $account->balance += $amount;
        $account->save();
    }
}
