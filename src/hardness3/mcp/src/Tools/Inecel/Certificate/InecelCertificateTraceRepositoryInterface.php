<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

interface InecelCertificateTraceRepositoryInterface
{
    /** @return array<int,array<string,mixed>> */
    public function trace(int $companyId, int $outgoingInvoiceId): array;

    /** @return array<int,array<string,mixed>> */
    public function traceByInvoiceNumber(int $companyId, string $outgoingInvoiceNumber): array;

    /** @return array<string,mixed>|null */
    public function findOutgoingInvoiceById(int $companyId, int $outgoingInvoiceId): ?array;

    /** @return array<string,mixed>|null */
    public function findOutgoingInvoiceByNumber(int $companyId, string $outgoingInvoiceNumber): ?array;
}
