<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Company\Application\CompanyProfileService;
use HardnessMcp\Tools\Company\Domain\CompanyRepositoryInterface;
use HardnessMcp\Tools\Company\GetCurrent\GetCurrentCompanyHandler;
use HardnessMcp\Tools\Company\GetCurrent\GetCurrentCompanyTool;
use HardnessMcp\Tools\Company\Infrastructure\PdoCompanyRepository;
use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;
use HardnessMcp\Tools\Partner\Infrastructure\PdoPartnerRepository;
use HardnessMcp\Tools\Partner\Search\PartnerSearchHandler;
use HardnessMcp\Tools\Partner\Search\PartnerSearchTool;
use HardnessMcp\Tools\Partner\GetByDocument\PartnerGetByDocumentTool;
use HardnessMcp\Tools\Partner\Search\PartnerCandidateReference;
use HardnessMcp\Tools\Partner\Get\PartnerGetTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;

/** Explicit composition root for the Company domain. */
final class CompanyToolsModule implements ToolModuleInterface
{
    private CompanyRepositoryInterface $repository;
    private PartnerRepositoryInterface $partnerRepository;
    private PartnerCandidateReference $references;

    public function __construct(CompanyRepositoryInterface $repository, PartnerRepositoryInterface $partnerRepository, ?PartnerCandidateReference $references = null)
    {
        $this->repository = $repository;
        $this->partnerRepository = $partnerRepository;
        $this->references = $references ?? new PartnerCandidateReference('test-only-candidate-reference-key');
    }

    public static function fromPdo(PDO $pdo, string $pepper = '', int $ttl = 600): self
    {
        return new self(new PdoCompanyRepository($pdo), new PdoPartnerRepository($pdo), new PartnerCandidateReference($pepper, $ttl));
    }

    /** @return ToolInterface[] */
    public function tools(): array
    {
        $service = new CompanyProfileService($this->repository);
        $handler = new GetCurrentCompanyHandler($service);

        return [new GetCurrentCompanyTool($handler), new PartnerSearchTool(new PartnerSearchHandler($this->partnerRepository, $this->references)), new PartnerGetTool($this->partnerRepository, $this->references), new PartnerGetByDocumentTool($this->partnerRepository, $this->references)];
    }
}
