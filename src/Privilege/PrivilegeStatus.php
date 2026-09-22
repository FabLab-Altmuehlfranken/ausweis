<?php

declare(strict_types=1);

namespace App\Privilege;

enum PrivilegeStatus: string
{
    case Valid = 'valid';
    case Expired = 'expired';
    case Revoked = 'revoked';

    public function icon(): string
    {
        return match ($this) {
            self::Valid => '✅',
            self::Expired => '⌛',
            self::Revoked => '🚫',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'gültig',
            self::Expired => 'abgelaufen',
            self::Revoked => 'entzogen',
        };
    }
}
