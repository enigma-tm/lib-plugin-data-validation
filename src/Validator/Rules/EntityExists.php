<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator\Rules;

use Paysera\DataValidator\Validator\AbstractValidator;
use Paysera\DataValidator\Validator\Contract\RepositoryInterface;

class EntityExists extends AbstractRule
{
    protected string $name = 'entity-exists';

    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
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
