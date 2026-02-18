<?php

namespace App\Auth\Domain\Enum;

enum SecurityTokenType: string
{
    case CONFIRMATION = 'confirmation';
    case PASSWORD_RESET = 'password_reset';
    case TYPE_LOGIN = 'login';
}
