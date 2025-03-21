<?php

namespace App\Aggregates;

use App\Events\AccountCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AccountAggregate extends AggregateRoot
{
    public function createAccount(string $firstname, string $lastname): static
    {
        $this->recordThat(new AccountCreated($firstname, $lastname));

        return $this;
    }
}
