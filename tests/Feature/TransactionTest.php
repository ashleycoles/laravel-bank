<?php

use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

describe('account transactions', function () {
    it('correctly updates account balances', function () {
        $sender = Account::factory()->create();
        $sender->deposit(10);

        $recipient = Account::factory()->create();

        $this->postJson(route('transactions.send'), [
            'sender_uuid' => $sender->uuid,
            'recipient_uuid' => $recipient->uuid,
            'amount' => 1,
        ])
            ->assertOk();

        $this->assertDatabaseHas('accounts', [
            'uuid' => $sender->uuid,
            'balance' => 9,
        ]);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $recipient->uuid,
            'balance' => 1,
        ]);
    });

    it('rejects transaction if sender exceeds overdraft limit', function () {
        $sender = Account::factory()->create();
        $sender->deposit(10);

        $recipient = Account::factory()->create();

        $this->postJson(route('transactions.send'), [
            'sender_uuid' => $sender->uuid,
            'recipient_uuid' => $recipient->uuid,
            'amount' => 100,
        ])
            ->assertForbidden();

        $this->assertDatabaseHas('accounts', [
            'uuid' => $sender->uuid,
            'balance' => 10,
        ]);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $recipient->uuid,
            'balance' => 0,
        ]);
    });

    it('rejects invalid uuids', function () {
        $this->postJson(route('transactions.send'), [
            'sender_uuid' => 'not a uuid',
            'recipient_uuid' => 'not a uuid',
            'amount' => 1,
        ])
            ->assertInvalid(['sender_uuid', 'recipient_uuid']);
    });

    it('rejects incorrect sender uuid', function () {
        $account = Account::factory()->create();

        $uuid = fake()->uuid();

        $this->postJson(route('transactions.send'), [
            'sender_uuid' => $uuid,
            'recipient_uuid' => $account->uuid,
            'amount' => 1,
        ])
            ->assertNotFound()
            ->assertJson(function (AssertableJson $json) use ($uuid) {
                $json->where('message', function ($value) use ($uuid) {
                    return Str::contains($value, $uuid);
                });
            });
    });

    it('rejects incorrect recipient uuid', function () {
        $account = Account::factory()->create();

        $uuid = fake()->uuid();

        $this->postJson(route('transactions.send'), [
            'sender_uuid' => $account->uuid,
            'recipient_uuid' => $uuid,
            'amount' => 1,
        ])
            ->assertNotFound()
            ->assertJson(function (AssertableJson $json) use ($uuid) {
                $json->where('message', function ($value) use ($uuid) {
                    return Str::contains($value, $uuid);
                });
            });
    });

});
