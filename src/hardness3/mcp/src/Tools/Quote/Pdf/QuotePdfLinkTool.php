<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Pdf;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class QuotePdfLinkTool implements ToolInterface
{
    private QuoteRepositoryInterface $repository;
    private QuotePdfGatewayInterface $gateway;
    private ToolResultPresenter $presenter;

    public function __construct(QuoteRepositoryInterface $repository, QuotePdfGatewayInterface $gateway, ?ToolResultPresenter $presenter = null)
    {
        $this->repository = $repository;
        $this->gateway = $gateway;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.quote.generate_pdf_link', 'Generates a shareable public PDF link for an existing quote. Use only after the user explicitly asks for the PDF or a link; this tool does not send WhatsApp messages.', [
            'type' => 'object',
            'properties' => ['quote_id' => ['type' => 'integer', 'minimum' => 1]],
            'required' => ['quote_id'],
            'additionalProperties' => false,
        ], ['quote:write'], true);
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        $quoteId = (int) ($arguments['quote_id'] ?? 0);
        try {
            if ($quoteId <= 0 || $this->repository->findById($context->getCompanyId(), $quoteId) === null) {
                return $this->presenter->functionalError('quote_not_found', 'O orçamento solicitado não foi encontrado para a empresa vinculada.', 'tools_call_quote_pdf_not_found');
            }
            $result = $this->gateway->generate($quoteId, $context);
        } catch (\Throwable $exception) {
            // Keep the MCP response generic, but retain a server-side marker
            // for diagnosing renderer failures without logging quote data.
            error_log(json_encode([
                'event' => 'hardness_mcp_quote_pdf_generation_failure',
                'exception_class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
            ], JSON_UNESCAPED_SLASHES));
            return $this->presenter->functionalError('quote_pdf_generation_failed', 'Não foi possível gerar o PDF deste orçamento agora. Tente novamente.', 'tools_call_quote_pdf_generation_failed');
        }
        $url = $result['pdf_url'] ?? '';
        if (!is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return $this->presenter->functionalError('quote_pdf_generation_failed', 'Não foi possível gerar o PDF deste orçamento agora. Tente novamente.', 'tools_call_quote_pdf_generation_failed');
        }
        return $this->presenter->record(['quote_id' => $quoteId, 'pdf_url' => $url], 'Link do PDF gerado', [['path' => 'quote_id', 'label' => 'Orçamento'], ['path' => 'pdf_url', 'label' => 'Link do PDF']]);
    }
}
