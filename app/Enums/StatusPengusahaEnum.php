<?php

namespace App\Enums;

enum StatusPengusahaEnum: int
{
    case PEMILIK = 1;
    case PEMILIKPENANGGUNGJAWAB = 2;

    public function label(): string
    {
        return match ($this) {
            self::PEMILIK => 'pemilik',
            self::PEMILIKPENANGGUNGJAWAB => 'pemilik dan penanggungjawab',
        };
    }
}
