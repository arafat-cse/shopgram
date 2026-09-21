<?php
namespace App\Enums;

enum VariantColor: string
{
    case Black = 'Black';
    case White = 'White';
    case Grey = 'Grey';
    case Navy = 'Navy';
    case Blue = 'Blue';
    case Red = 'Red';
    case Green = 'Green';
    case Yellow = 'Yellow';
    case Orange = 'Orange';
    case Pink = 'Pink';
    case Purple = 'Purple';
    case Brown = 'Brown';
    case Multicolor = 'Multicolor';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
