<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference\Domain;

interface PartnerReferenceProductRepositoryInterface
{
    /** @return array<string,mixed>|null */
    public function findVisiblePartnerByCode(int $companyId, int $partnerCode, string $role): ?array;

    /** @return array<int,array<string,mixed>> */
    public function findProductsByPartnerReference(int $companyId, int $partnerCode, string $role, string $referenceCode): array;
}
