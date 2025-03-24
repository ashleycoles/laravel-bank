<?php

namespace App\Aggregates;

use App\Events\AccountCreated;
use App\Events\FundsDeposited;
use App\Events\FundsWithdrawn;
use App\Events\OverdraftLimitUpdated;
use App\Events\TransactionReceived;
use App\Events\TransactionSent;
use App\Exceptions\CouldNotSendTransaction;
use App\Exceptions\CouldNotUpdateOverdraftLimit;
use App\Exceptions\CouldNotWithdraw;
use App\Models\Account;
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
        $this->recordThat(new FundsDeposited($amount));

        return $this;
    }

    /**
     * @throws CouldNotWithdraw
     */
    public function withdraw(int $amount): static
    {
        if (! $this->hasSufficientFundsToSubtract($amount)) {
            throw CouldNotWithdraw::insufficientFunds($amount);
        }

        $this->recordThat(new FundsWithdrawn($amount));

        return $this;
    }

    /**
     * @throws CouldNotSendTransaction
     */
    public function sendTransaction(int $amount, Account $sender, Account $recipient): static
    {
        if (! $this->hasSufficientFundsToSubtract($amount)) {
            throw CouldNotSendTransaction::insufficientFunds($amount);
        }

        $this->recordThat(new TransactionSent($amount, $sender, $recipient));

        return $this;
    }

    public function receiveTransaction(int $amount): static
    {
        $this->recordThat(new TransactionReceived($amount));

        return $this;
    }

    /**
     * @throws CouldNotUpdateOverdraftLimit
     */
    public function updateOverdraftLimit(int $limit): static
    {
        if (! $this->hasSufficientFundsToUpdateOverdraftLimit($limit)) {
            throw CouldNotUpdateOverdraftLimit::limitBreach($limit);
        }

        $this->recordThat(new OverdraftLimitUpdated($limit));

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

    public function applyTransactionReceived(TransactionReceived $event): void
    {
        $this->balance += $event->amount;
    }

    public function applyTransactionSent(TransactionSent $event): void
    {
        $this->balance -= $event->amount;
    }

    public function applyOverdraftLimitUpdated(OverdraftLimitUpdated $event): void
    {
        $this->overdraftLimit = $event->limit;
    }

    private function hasSufficientFundsToSubtract(int $amount): bool
    {
        return $this->balance - $amount >= $this->overdraftLimit;
    }

    private function hasSufficientFundsToUpdateOverdraftLimit(int $limit): bool
    {
        return $this->balance >= $limit;
    }
}
