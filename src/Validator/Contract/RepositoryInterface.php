<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Contract;

interface RepositoryInterface
{
    public function find(int $id): ?array;

    public function findAll(): array;
}
