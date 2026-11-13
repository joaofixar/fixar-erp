<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateTraceTool;
use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateReadTool;
use HardnessMcp\Tools\Inecel\Certificate\PdftotextPdfTextExtractor;
use HardnessMcp\Tools\Inecel\Certificate\PdoInecelCertificateTraceRepository;
use HardnessMcp\Tools\Inecel\Certificate\CertificateDownloadLinkService;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class InecelCertificateToolsModule implements ToolModuleInterface
{
    private InecelCertificateTraceTool $traceTool;
    private InecelCertificateReadTool $readTool;
    public function __construct(InecelCertificateTraceTool $traceTool, InecelCertificateReadTool $readTool) { $this->traceTool = $traceTool; $this->readTool = $readTool; }
    public static function fromRuntime(PDO $pdo, string $applicationRoot, string $database, string $rootUrl, string $downloadSecret): self
    {
        $repository = new PdoInecelCertificateTraceRepository($pdo);
        $attachmentsDirectory = rtrim($applicationRoot, '/') . '/dados_usuarios/' . $database . '/atividades';
        $tracePdfText = new PdftotextPdfTextExtractor();
        $readPdfText = new PdftotextPdfTextExtractor(true, $database);
        return new self(new InecelCertificateTraceTool($repository, $tracePdfText, $attachmentsDirectory, new CertificateDownloadLinkService($rootUrl, $downloadSecret)), new InecelCertificateReadTool($repository, $readPdfText, $attachmentsDirectory));
    }
    /** @return ToolInterface[] */
    public function tools(): array { return [$this->traceTool, $this->readTool]; }
}
