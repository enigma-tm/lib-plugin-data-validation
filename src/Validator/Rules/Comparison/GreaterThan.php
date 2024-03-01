<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules\Comparison;

class GreaterThan extends AbstractComparison
{
    protected string $name = 'greater-than';

    protected function compare($value, $lowerBound): bool
    {
        return (float) $value > (float) $lowerBound;
    }
}
