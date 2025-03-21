<?php

namespace App\Models;

use App\Aggregates\AccountAggregate;
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
     * @throws CouldNotUpdateOverdraftLimit
     */
    public function updateOverdraftLimit(int $limit): void
    {
        AccountAggregate::retrieve($this->uuid)
            ->updateOverdraftLimit($limit)
            ->persist();
    }

    public static function uuid(string $uuid): ?Account
    {
        return static::where('uuid', $uuid)->first();
    }
}
