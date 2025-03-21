<?php

namespace App\Aggregates;

use App\Events\AccountCreated;
use App\Events\FundsDeposited;
use App\Events\FundsWithdrawn;
use App\Events\OverdraftLimitUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AccountAggregate extends AggregateRoot
{
    private int $balance = 0;
    private int $overdraftLimit = 0;

    public function createAccount(string $firstname, string $lastname): static
    {
        $this->recordThat(new AccountCreated($firstname, $lastname));

        return $this;
    }

    public function deposit(int $amount): static
    {
        $this->recordThat(new FundsDeposited($this->uuid(), $amount));

        return $this;
    }

    public function withdraw(int $amount): static
    {
        $this->recordThat(new FundsWithdrawn($this->uuid(), $amount));

        return $this;
    }

    public function updateOverdraftLimit(int $limit): static
    {
        $this->recordThat(new OverdraftLimitUpdated($this->uuid(), $limit));

        return $this;
    }

    public function applyFundsDeposited(FundsDeposited $event): void
    {
        $this->balance += $event->amount;
    }

    public function applyFundsWithdrawn(FundsWithdrawn $event): void
    {
        $this->balance -= $event->amount;
    }

    public function applyOverdraftLimitUpdated(OverdraftLimitUpdated $event): void
    {
        $this->overdraftLimit = $event->limit;
    }
}
