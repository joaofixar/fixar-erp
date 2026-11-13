<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Domain;

interface PartnerRepositoryInterface
{
    /** @return array<int,array<string,mixed>> */
    public function searchByDocument(int $companyId, string $document, string $role, int $limit): array;
    /** @return array<int,array<string,mixed>> */
    public function searchByName(int $companyId, string $query, string $role, int $limit): array;
    /** @return array<string,mixed>|null */ public function findVisibleById(int $companyId, int $partnerId): ?array;
}
