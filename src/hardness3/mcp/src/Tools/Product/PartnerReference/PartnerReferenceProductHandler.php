<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Product\PartnerReference\Domain\PartnerReferenceProductRepositoryInterface;

final class PartnerReferenceProductHandler
{
    private PartnerReferenceProductRepositoryInterface $repository;

    public function __construct(PartnerReferenceProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /** @return array{partner:array<string,mixed>|null,result:array<string,mixed>} */
    public function handle(PartnerReferenceProductInput $input, ExecutionContext $context): array
    {
        $partner = $this->repository->findVisiblePartnerByCode($context->getCompanyId(), $input->getPartnerCode(), $input->getRole());
        if ($partner === null) {
            return ['partner' => null, 'result' => []];
        }
        $products = $this->repository->findProductsByPartnerReference($context->getCompanyId(), $input->getPartnerCode(), $input->getRole(), $input->getReferenceCode());
        return ['partner' => $partner, 'result' => PartnerReferenceProductOutput::fromProducts($products, $input->getPartnerCode(), $input->getRole(), $input->getReferenceCode())];
    }
}
