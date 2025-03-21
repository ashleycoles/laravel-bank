<?php

namespace App\Exceptions;

final class CouldNotWithdraw extends \Exception
{
    public static function insufficientFunds(int $amount): self
    {
        return new CouldNotWithdraw("Insufficient funds to withdraw $amount.");
    }
}
