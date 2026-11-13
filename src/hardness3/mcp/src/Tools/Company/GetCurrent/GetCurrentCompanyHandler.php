<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\GetCurrent;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Company\Application\CompanyProfileService;

final class GetCurrentCompanyHandler
{
    private CompanyProfileService $service;
    public function __construct(CompanyProfileService $service) { $this->service = $service; }

    /** @return array<string, mixed>|null */
    public function handle(GetCurrentCompanyInput $input, ExecutionContext $executionContext): ?array
    {
        $company = $this->service->getCurrentCompany($executionContext->getCompanyId());
        return $company === null ? null : GetCurrentCompanyOutput::fromCompany($company);
    }
}
