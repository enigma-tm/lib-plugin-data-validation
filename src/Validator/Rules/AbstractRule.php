<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;

abstract class AbstractRule
{
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void;

    // There will be other common methods here as validation rules are added to the project
}
