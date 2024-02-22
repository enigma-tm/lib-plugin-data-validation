<?php

namespace Paysera\DataValidator\Tests\Validator;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Contract\RepositoryInterface;
use Paysera\DataValidator\Validator\Rules\EntityExists;
use PHPUnit\Framework\TestCase;

class AbstractValidatorTest extends TestCase
{
    public function testNoValidationRules()
    {
        $validator = new class extends AbstractValidator { };

        $this->assertTrue($validator->validate([], []));
    }

    public function getTestedData(): iterable
    {
        $entityId = "3";
        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('find')
            ->with($entityId)
            ->willReturn([
                2 => 'some value that means that the entity exists',
            ]);

        yield 'entity exists' => [
            $entityId,
            $repositoryMock,
            true,
            [],
        ];

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('find')
            ->with($entityId)
            ->willReturn([]);

        yield 'entity does not exist' => [
            $entityId,
            $repositoryMock,
            false,
            [
                'field' => [
                    'entity-exists' => 'error message for entity-exists rule',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getTestedData
     */
    public function testRealValidator(
        string $entityId,
        RepositoryInterface $repositoryMock,
        bool $validationResult,
        array $errors
    ): void
    {
        $realValidator = new class($repositoryMock) extends AbstractValidator {
            public function __construct(RepositoryInterface $repository)
            {
                parent::__construct();

                $rule = new EntityExists($repository);
                $this->addRule($rule);
                $this->setRuleMessage($rule->getName(), 'error message for entity-exists rule');
            }
        };

        $rules = [
            'field' => 'entity-exists',
        ];

        $values = [
            'field' => "3",
        ];

        $this->assertEquals(
            $validationResult,
            $realValidator->validate($values, $rules)
        );

        $this->assertEquals(
            $errors,
            $realValidator->getProcessedErrors()
        );
    }
}
