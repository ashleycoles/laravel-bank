<?php

use App\Models\Account;

describe('account creation', function () {
    it('creates an account', function () {
        $this->postJson(route('accounts.create'), [
            'firstname' => 'Test',
            'lastname' => 'Tester'
        ])
            ->assertStatus(201);

        $this->assertDatabaseHas('accounts', [
            'firstname' => 'Test',
            'lastname' => 'Tester',
            'balance' => 0,
            'overdraft' => 0
        ]);
    });

    it('rejects missing names', function () {
       $this->postJson(route('accounts.create'))
           ->assertStatus(422)
           ->assertInvalid(['firstname', 'lastname']);
    });

    it('rejects invalid names', function () {
        $this->postJson(route('accounts.create'), [
            'firstname' => 'a',
            'lastname' => 'b'
        ])
            ->assertStatus(422)
            ->assertInvalid(['firstname', 'lastname']);
    });
});


describe('account deposits', function () {
   it('deposits the correct amount', function () {
      $account = Account::factory()->create();

      $this->postJson(route('accounts.deposit'), [
          'uuid' => $account->uuid,
          'amount' => 100
      ])
          ->assertOk();

      $this->assertDatabaseHas('accounts', [
          'uuid' => $account->uuid,
          'balance' => 100
      ]);
   });

   it('correctly handles multiple deposits', function () {
       $account = Account::factory()->create();

       $this->postJson(route('accounts.deposit'), [
           'uuid' => $account->uuid,
           'amount' => 100
       ])
           ->assertOk();

       $this->assertDatabaseHas('accounts', [
           'uuid' => $account->uuid,
           'balance' => 100
       ]);

       $this->postJson(route('accounts.deposit'), [
           'uuid' => $account->uuid,
           'amount' => 100
       ])
           ->assertOk();

       $this->assertDatabaseHas('accounts', [
           'uuid' => $account->uuid,
           'balance' => 200
       ]);
   });

   it('rejects missing data', function () {
       $this->postJson(route('accounts.deposit'))
           ->assertInvalid(['uuid', 'amount']);
   });

    it('rejects invalid data', function () {
        $this->postJson(route('accounts.deposit'), [
            'uuid' => 'not a uuid',
            'amount' => -10
        ])
            ->assertInvalid(['uuid', 'amount']);
    });
});

describe('account withdraws', function () {
   it('withdraws the correct amount', function () {
        $account = Account::factory()->create();
        $account->deposit(100);

        $this->postJson(route('accounts.withdraw'), [
            'uuid' => $account->uuid,
            'amount' => 10
        ])
            ->assertOk();

        $this->assertDatabaseHas('accounts' , [
            'uuid' => $account->uuid,
            'balance' => 90
        ]);
   });

   it ('correctly handles multiple withdrawals', function () {
       $account = Account::factory()->create();
       $account->deposit(100);

       $this->postJson(route('accounts.withdraw'), [
           'uuid' => $account->uuid,
           'amount' => 10
       ])
           ->assertOk();

       $this->assertDatabaseHas('accounts' , [
           'uuid' => $account->uuid,
           'balance' => 90
       ]);

       $this->postJson(route('accounts.withdraw'), [
           'uuid' => $account->uuid,
           'amount' => 10
       ])
           ->assertOk();

       $this->assertDatabaseHas('accounts' , [
           'uuid' => $account->uuid,
           'balance' => 80
       ]);
   });

   it('rejects withdrawal past the overdraft limit', function () {
       $account = Account::factory()->create();
       $account->updateOverdraftLimit(-100);

       $this->postJson(route('accounts.withdraw'), [
           'uuid' => $account->uuid,
           'amount' => 1000
       ])
           ->assertForbidden();

       $this->assertDatabaseHas('accounts', [
           'uuid' => $account->uuid,
           'balance' => 0
       ]);
   });
});

describe('account overdraft limits', function () {
    it('correctly updates the overdraft limit', function () {
       $account = Account::factory()->create();

       $this->postJson(route('accounts.overdraft.update'), [
           'uuid' => $account->uuid,
           'limit' => -100
       ])
           ->assertOk();

       $this->assertDatabaseHas('accounts', [
           'uuid' => $account->uuid,
           'overdraft' => -100
       ]);
    });

    it('rejects updates to overdraft limit higher than balance', function () {
        $account = Account::factory()->create();
        $account->updateOverdraftLimit(-100);
        $account->withdraw(100);

        $this->postJson(route('accounts.overdraft.update'), [
            'uuid' => $account->uuid,
            'limit' => -10
        ])
            ->assertForbidden();

        $this->assertDatabaseHas('accounts', [
            'uuid' => $account->uuid,
            'overdraft' => -100
        ]);
    });
});
