<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\Application;

use HardnessMcp\Tools\Company\Domain\CompanyRepositoryInterface;

final class CompanyProfileService
{
    private CompanyRepositoryInterface $repository;
    public function __construct(CompanyRepositoryInterface $repository) { $this->repository = $repository; }

    /** @return array<string, mixed>|null */
    public function getCurrentCompany(int $companyId): ?array
    {
        return $this->repository->findBasicById($companyId);
    }
}
