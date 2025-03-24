<?php

namespace App\Models;

use App\Aggregates\AccountAggregate;
use App\Exceptions\CouldNotSendTransaction;
use App\Exceptions\CouldNotUpdateOverdraftLimit;
use App\Exceptions\CouldNotWithdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = ['firstname', 'lastname', 'uuid'];

    /**
     * @param  array<string, string>  $attributes
     */
    public static function createWithAttributes(array $attributes): ?Account
    {
        $uuid = (string) Uuid::uuid4();

        AccountAggregate::retrieve($uuid)
            ->createAccount($attributes['firstname'], $attributes['lastname'])
            ->persist();

        return static::uuid($uuid);
    }

    public function deposit(int $amount): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->deposit($amount)
            ->persist();
    }

    /**
     * @throws CouldNotWithdraw
     */
    public function withdraw(int $amount): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->withdraw($amount)
            ->persist();
    }

    /**
     * @throws CouldNotSendTransaction
     */
    public function sendTransaction(int $amount, Account $recipient): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->sendTransaction($amount, $this, $recipient)
            ->persist();
    }

    public function receiveTransaction(int $amount): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->receiveTransaction($amount)
            ->persist();
    }

    /**
     * @throws CouldNotUpdateOverdraftLimit
     */
    public function updateOverdraftLimit(int $limit): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->updateOverdraftLimit($limit)
            ->persist();
    }

    public static function uuid(?string $uuid): ?Account
    {
        if (! $uuid) {
            return null;
        }

        return static::where('uuid', $uuid)->first();
    }
}
