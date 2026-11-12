<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\Quote\GetById\QuoteGetByIdHandler;
use HardnessMcp\Tools\Quote\GetById\QuoteGetByIdTool;
use HardnessMcp\Tools\Quote\Infrastructure\PdoQuoteRepository;
use HardnessMcp\Tools\Quote\Search\QuoteSearchHandler;
use HardnessMcp\Tools\Quote\Search\QuoteSearchTool;
use HardnessMcp\Tools\Quote\Create\QuoteCreateGatewayInterface;
use HardnessMcp\Tools\Quote\Create\QuoteCreateTool;
use HardnessMcp\Tools\Quote\Pdf\QuotePdfGatewayInterface;
use HardnessMcp\Tools\Quote\Pdf\QuotePdfLinkTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;
use HardnessMcp\Tools\Quote\Item\QuoteItemGatewayInterface;
use HardnessMcp\Tools\Quote\Item\QuoteItemUpdateTool;
use HardnessMcp\Tools\Quote\Item\QuoteItemDeleteTool;
use HardnessMcp\Tools\Quote\Item\QuoteItemAddTool;
use HardnessMcp\Tools\Quote\Item\UnavailableQuoteItemGateway;
use HardnessMcp\Tools\Quote\Summary\Domain\QuoteSummaryRepositoryInterface;
use HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler;
use HardnessMcp\Tools\Quote\Summary\QuoteSummaryTool;

final class QuoteToolsModule implements ToolModuleInterface
{
    private QuoteRepositoryInterface $repository;
    private ?QuoteCreateGatewayInterface $createGateway;
    private ?QuotePdfGatewayInterface $pdfGateway;
    private ?QuoteItemGatewayInterface $itemGateway;
    private ?QuoteSummaryRepositoryInterface $summaryRepository;
    private QuoteRepositoryInterface $pdfRepository;
    public function __construct(QuoteRepositoryInterface $repository, ?QuoteCreateGatewayInterface $createGateway = null, ?QuotePdfGatewayInterface $pdfGateway = null, ?QuoteItemGatewayInterface $itemGateway = null, ?QuoteSummaryRepositoryInterface $summaryRepository = null, ?QuoteRepositoryInterface $pdfRepository = null) { $this->repository = $repository; $this->createGateway = $createGateway; $this->pdfGateway = $pdfGateway; $this->itemGateway=$itemGateway??new UnavailableQuoteItemGateway(); $this->summaryRepository=$summaryRepository; $this->pdfRepository=$pdfRepository??$repository; }
    public static function fromPdo(PDO $pdo): self { return new self(new PdoQuoteRepository($pdo)); }
    /** @return ToolInterface[] */
    public function tools(): array { $tools=[new QuoteSearchTool(new QuoteSearchHandler($this->repository)), new QuoteGetByIdTool(new QuoteGetByIdHandler($this->repository))]; if($this->summaryRepository!==null)$tools[]=new QuoteSummaryTool(new QuoteSummaryHandler($this->summaryRepository)); if($this->createGateway!==null)$tools[]=new QuoteCreateTool($this->createGateway); if($this->pdfGateway!==null)$tools[]=new QuotePdfLinkTool($this->pdfRepository, $this->pdfGateway); $tools[]=new QuoteItemAddTool($this->itemGateway);$tools[]=new QuoteItemUpdateTool($this->itemGateway);$tools[]=new QuoteItemDeleteTool($this->itemGateway); return $tools; }
}
