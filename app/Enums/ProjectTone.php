<?php

namespace App\Enums;

enum ProjectTone: string
{
    case Mint = 'mint';
    case Blue = 'blue';
    case Lavender = 'lavender';
    case Peach = 'peach';

    /**
     * Get the name shown in the admin panel.
     */
    public function label(): string
    {
        return match ($this) {
            self::Mint => 'Hijau mint',
            self::Blue => 'Biru',
            self::Lavender => 'Lavender',
            self::Peach => 'Peach',
        };
    }

    /**
     * Get the Tailwind classes for the tinted background and accent colour.
     */
    public function classes(): string
    {
        return match ($this) {
            self::Mint => 'bg-[#e2f3ed] text-[#25836d]',
            self::Blue => 'bg-[#e5f0fd] text-[#287dc1]',
            self::Lavender => 'bg-[#eeebfa] text-[#7961b8]',
            self::Peach => 'bg-[#fbefe4] text-[#b77549]',
        };
    }
}
