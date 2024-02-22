<?php

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Exception\IncorrectValidationRuleStructure;
use PHPUnit\Framework\TestCase;

class AbstractRuleTest extends TestCase
{

    public function testRuleStructure()
    {
        $someRule = new class extends AbstractRule {
            public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void
            {
            }
        };

        $this->expectException(IncorrectValidationRuleStructure::class);
        $this->expectExceptionMessage('You must reset the $name property value');
        $someRule->getName();
    }
}
