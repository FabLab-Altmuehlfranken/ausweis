<?php

declare(strict_types=1);

namespace App\Privilege;

enum PrivilegeStatus: string
{
    case Valid = 'valid';
    case Expired = 'expired';

    public function icon(): string
    {
        return match ($this) {
            self::Valid => '✅',
            self::Expired => '⌛',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'gültig',
            self::Expired => 'abgelaufen',
        };
    }
}
