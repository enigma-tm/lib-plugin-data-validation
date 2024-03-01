<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Exception\IncorrectValidationRuleStructure;

class GreaterThan extends AbstractRule
{
    protected string $name = 'greater-than';

    /**
     * @throws IncorrectValidationRuleStructure
     */
    public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void
    {
        $fieldToCompare = $parameters[0];
        $lowerBound = $validator->getValue($data, $fieldToCompare);

        $values = $validator->getValues($data, $pattern);
        if (empty($values)) {
            $validator->addError($pattern, $this->getName(), [
                ':fieldToCompare' => $fieldToCompare,
                ':valueToCompare' => $lowerBound,
            ]);
            return;
        }

        foreach ($values as $attribute => $value) {
            if (is_numeric($value) && is_numeric($lowerBound) && $value > $lowerBound) {
                continue;
            }

            $validator->addError($attribute, $this->getName(), [
                ':fieldToCompare' => $fieldToCompare,
                ':valueToCompare' => $lowerBound,
            ]);
        }
    }
}
