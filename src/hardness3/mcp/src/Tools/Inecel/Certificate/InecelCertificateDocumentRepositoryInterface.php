<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

interface InecelCertificateDocumentRepositoryInterface
{
    /**
     * Returns a certificate document only when it is reachable through the
     * production trace of the connection's company.
     *
     * @return array{document_id:int,file_name:string}|null
     */
    public function findReadableDocument(int $companyId, int $documentId): ?array;
}
