<?php

namespace App\Auth\Domain\Service;

class LoginCodeGenerator
{
    private const CODE_LENGTH = 5;

    public function generate(): string
    {
        $min = 10 ** (self::CODE_LENGTH - 1); // 10000
        $max = (10 ** self::CODE_LENGTH) - 1; // 99999

        return (string) random_int($min, $max);
    }
}
