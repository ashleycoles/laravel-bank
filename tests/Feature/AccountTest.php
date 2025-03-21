<?php

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

