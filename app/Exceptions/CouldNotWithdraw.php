<?php

namespace App\Exceptions;

class CouldNotWithdraw extends \Exception
{
    public static function insufficientFunds(int $amount): self
    {
        return new static("Insufficient funds to withdraw $amount.");
    }
}
