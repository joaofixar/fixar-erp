<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\Domain;

interface CompanyRepositoryInterface
{
    /** @return array<string, mixed>|null */
    public function findBasicById(int $companyId): ?array;
}
