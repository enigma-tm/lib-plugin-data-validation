<?php

namespace Paysera\Payment\Tests\Validator\Rules;

use Paysera\DataValidator\Contract\RepositoryInterface;
use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Rules\EntityExists;
use PHPUnit\Framework\TestCase;

class EntityExistsTest extends TestCase
{
    public function getTestedData(): iterable
    {
        $orderStatusId = 2;
        $data = [
            'payment_paysera_new_order_status_id' => $orderStatusId,
        ];
        $pattern = 'payment_paysera_new_order_status_id';
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->never())
            ->method('addError');
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);

        $orderStatusRepositoryMock = $this->createMock(RepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->once())
            ->method('find')
            ->with($orderStatusId)
            ->willReturn(['some not empty array that means that specified entity exists']);

        yield 'value is correct' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
            $orderStatusRepositoryMock,
        ];

        $data = [
            'payment_paysera_new_order_status_id' => null,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->once())
            ->method('addError')
            ->with($pattern, 'entity-exists', [':id' => null])
            ->willReturnCallback(function($arg) {
                $this->assertTrue(true);
            });
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);
        $orderStatusRepositoryMock = $this->createMock(RepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->never())
            ->method('find');

        yield 'value is null' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
            $orderStatusRepositoryMock,
        ];

        $data = [
            'payment_paysera_new_order_status_id' => '',
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->once())
            ->method('addError')
            ->with($pattern, 'entity-exists', [':id' => ''])
            ->willReturnCallback(function($arg) {
                $this->assertTrue(true);
            });
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);
        $orderStatusRepositoryMock = $this->createMock(RepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->never())
            ->method('find');

        yield 'value is empty' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
            $orderStatusRepositoryMock,
        ];

        $data = [
            'payment_paysera_new_order_status_id' => false,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->once())
            ->method('addError')
            ->with($pattern, 'entity-exists', [':id' => false])
            ->willReturnCallback(function($arg) {
                $this->assertTrue(true);
            });
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);
        $orderStatusRepositoryMock = $this->createMock(RepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->never())
            ->method('find');

        yield 'value is false' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
            $orderStatusRepositoryMock,
        ];

        $orderStatusId = 555;
        $data = [
            'payment_paysera_new_order_status_id' => $orderStatusId,
        ];
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->once())
            ->method('addError')
            ->with($pattern, 'entity-exists', [':id' => $orderStatusId])
            ->willReturnCallback(function($arg) {
                $this->assertTrue(true);
            });
        $validatorMock->expects($this->once())
            ->method('getValues')
            ->with($data, $pattern)
            ->willReturn($data);
        $orderStatusRepositoryMock = $this->createMock(RepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->once())
            ->method('find')
            ->with($orderStatusId)
            ->willReturn([]);

        yield 'value correct but does not exists' => [
            $validatorMock,
            $data,
            $pattern,
            'parameters' => [
                '',
            ],
            $orderStatusRepositoryMock,
        ];
    }

    /**
     * @dataProvider getTestedData
     */
    public function testValidate($validatorMock, $data, $pattern, $parameters, $orderRepositoryMock)
    {
        $entityExistsRule = new EntityExists($orderRepositoryMock);

        $entityExistsRule->validate($validatorMock, $data, $pattern, $parameters);
    }
}
