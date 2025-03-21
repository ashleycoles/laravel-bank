<?php

namespace App\Exceptions;

final class CouldNotUpdateOverdraftLimit extends \Exception
{
    public static function limitBreach(int $limit): self
    {
        return new CouldNotUpdateOverdraftLimit("Current overdraft exceeds $limit");
    }
}
