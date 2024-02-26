<?php

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;

class Required extends AbstractRule
{
    protected string $name = 'required';

    public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void
    {
        // Check value is not null
        foreach ($validator->getValues($data, $pattern) as $attribute => $value) {
            // not allowed: null, '', [], empty instance Countable
            if ($this->isFilled($value)) {
                continue;
            }

            $validator->addError($attribute, $this->getName());
        }
    }
}
