<?php

namespace App\Exceptions;

final class CouldNotSendTransaction extends \Exception
{
    public static function insufficientFunds(int $amount): self
    {
        return new CouldNotSendTransaction("Insufficient funds to send $amount.");
    }
}
