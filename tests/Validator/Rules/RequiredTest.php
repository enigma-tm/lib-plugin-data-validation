<?php

namespace Paysera\DataValidator\Tests\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Rules\Required;
use PHPUnit\Framework\TestCase;

class RequiredTest extends TestCase
{
    public function testGetName()
    {
        $requiredRule = new Required();

        $this->assertEquals(
            'required',
            $requiredRule->getName()
        );
    }

    public function getDataset(): iterable
    {
        $value = 'some value';
        $pattern = 'paysera_project';

        $data = [
            $pattern => $value,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->never())
            ->method('addError');
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);

        yield 'value is correct simple value' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
        ];

        $value = [
            'some array value',
        ];
        $data = [
            $pattern => $value,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->never())
            ->method('addError');
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);

        yield 'value is correct array value' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
        ];

        $value = '';
        $data = [
            $pattern => $value,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock
            ->method('addError')
            ->with($pattern, 'required')
            ->willReturnCallback(function($field, $ruleName) use ($pattern) {
                $this->assertEquals($pattern, $field);
                $this->assertEquals('required', $ruleName);
            });
        $validatorMock
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);

        yield 'value is empty' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
        ];

        $value = null;
        $data = [
            $pattern => $value,
        ];

        yield 'value is null' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
        ];

        $value = false;
        $data = [
            $pattern => $value,
        ];

        yield 'value is false' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
        ];
    }

    /**
     * @dataProvider getDataset
     */
    public function testValidate($validatorMock, $data, $pattern, $parameters)
    {
        $entityExistsRule = new Required();

        $entityExistsRule->validate($validatorMock, $data, $pattern, $parameters);
    }
}
