<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Contract\RepositoryInterface;
use Paysera\DataValidator\Validator\AbstractValidator;

class EntityExists extends AbstractRule
{
    protected string $name = 'entity-exists';

    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function validate(AbstractValidator $validator, $data, $pattern, $parameters): void
    {
        foreach ($validator->getValues($data, $pattern) as $attribute => $value) {
            if (!empty($value)) {
                if ($this->repository->find((int) $value)) {
                    break;
                }
            }

            $validator->addError($attribute, $this->getName(), [':id' => $value]);
        }
    }
}
