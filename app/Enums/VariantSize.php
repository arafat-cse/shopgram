<?php
namespace App\Enums;

enum VariantSize: string
{
    case XS = 'XS';
    case S = 'S';
    case M = 'M';
    case L = 'L';
    case XL = 'XL';
    case XXL = 'XXL';
    case EU36 = '36';
    case EU37 = '37';
    case EU38 = '38';
    case EU39 = '39';
    case EU40 = '40';
    case EU41 = '41';
    case EU42 = '42';
    case EU43 = '43';
    case EU44 = '44';
    case EU45 = '45';
    case EU46 = '46';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
