<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Helper;

class Str
{
    public static function prettifyAttributeName(string $attribute): string
    {
        return ucfirst(str_replace(['.*', '.', '_'], ['', ' ', ' '], $attribute));
    }
}
