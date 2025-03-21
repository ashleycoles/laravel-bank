<?php

namespace App\Exceptions;

class CouldNotUpdateOverdraftLimit extends \Exception
{
    public static function limitBreach(int $limit): self
    {
        return new static("Current overdraft exceeds $limit");
    }
}
