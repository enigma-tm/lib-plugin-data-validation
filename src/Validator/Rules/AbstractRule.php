<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Exception\IncorrectValidationRuleStructure;

abstract class AbstractRule
{
    protected string $name = '';

    /**
     * @throws IncorrectValidationRuleStructure
     */
    public function getName(): string
    {
        if (empty($this->name)) {
            throw new IncorrectValidationRuleStructure('You must reset the $name property value');
        }
        return $this->name;
    }

    abstract public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void;

    // There will be other common methods here as validation rules are added to the project
}
