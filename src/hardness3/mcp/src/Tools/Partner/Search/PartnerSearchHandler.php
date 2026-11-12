<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;

final class PartnerSearchHandler
{
    private PartnerRepositoryInterface $repository; private PartnerCandidateReference $references;
    public function __construct(PartnerRepositoryInterface $repository, PartnerCandidateReference $references) { $this->repository = $repository; $this->references=$references; }
    /** @return array<string,mixed> */
    public function handle(PartnerSearchInput $input, ExecutionContext $context): array
    {
        $partners = $input->getCriterion() === 'document'
            ? $this->repository->searchByDocument($context->getCompanyId(), $input->getQuery(), $input->getRole(), 6)
            : $this->repository->searchByName($context->getCompanyId(), $input->getQuery(), $input->getRole(), 6);
        return PartnerSearchOutput::fromPartners($partners, $context, $this->references, $input->getCriterion() === 'document');
    }
}
