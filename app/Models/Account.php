<?php

namespace App\Models;

use App\Aggregates\AccountAggregate;
use App\Events\AccountCreated;
use App\Events\FundsDeposited;
use App\Events\FundsWithdrawn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = ['firstname', 'lastname', 'uuid'];

    public static function createWithAttributes(array $attributes): Account
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

    public function withdraw(int $amount): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->withdraw($amount)
            ->persist();
    }

    public static function uuid(string $uuid): ?Account
    {
        return static::where('uuid', $uuid)->first();
    }
}
