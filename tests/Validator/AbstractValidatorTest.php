<?php

namespace Paysera\Payment\Tests\Validator;

use Paysera\DataValidator\Validator\AbstractValidator;
use PHPUnit\Framework\TestCase;

class AbstractValidatorTest extends TestCase
{
    public function testNoValidationRules()
    {
        $validator = new class extends AbstractValidator { };

        $this->assertTrue($validator->validate([], []));
    }
}
