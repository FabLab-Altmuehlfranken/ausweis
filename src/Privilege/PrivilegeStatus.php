<?php

declare(strict_types=1);

namespace App\Privilege;

enum PrivilegeStatus: string
{
    case Valid = 'valid';
    case Expired = 'expired';
    case Revoked = 'revoked';
    case Banned = 'banned';

    public function icon(): string
    {
        return match ($this) {
            self::Valid => '✅',
            self::Expired => '⌛',
            self::Revoked => '🚫',
            self::Banned => '⛔',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'gültig',
            self::Expired => 'abgelaufen',
            self::Revoked => 'entzogen',
            self::Banned => 'gesperrt',
        };
    }
}
