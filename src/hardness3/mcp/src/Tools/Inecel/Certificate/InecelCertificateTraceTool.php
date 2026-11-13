<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class InecelCertificateTraceTool implements ToolInterface
{
    public const SCOPE = 'inecel.certificate:read';
    private InecelCertificateTraceRepositoryInterface $repository;
    private PdfTextExtractorInterface $pdfText;
    private string $attachmentsDirectory;
    private ToolResultPresenter $presenter;
    private ?CertificateDownloadLinkService $downloads;

    public function __construct(InecelCertificateTraceRepositoryInterface $repository, PdfTextExtractorInterface $pdfText, string $attachmentsDirectory, ?CertificateDownloadLinkService $downloads = null)
    {
        $this->repository = $repository;
        $this->pdfText = $pdfText;
        $this->attachmentsDirectory = rtrim($attachmentsDirectory, '/');
        $this->presenter = new ToolResultPresenter();
        $this->downloads = $downloads;
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.inecel.certificate.trace', 'Rastreia, de forma somente leitura, os lotes de uma NF de saída até a OP, o consumo de matéria-prima, a NF de entrada e o certificado anexado. Informe o número da NF de saída quando disponível; o ID interno T007_Id também é aceito. A NF de saída é sempre limitada à empresa vinculada à conexão MCP; quando sua OP vinculada pertencer a outra empresa do ERP, a rastreabilidade de produção é retornada com a identificação dessa empresa. Quando a entrada da matéria-prima for uma transferência sem anexo, também procura a entrada original pela chave da NF-e, pelo item de entrada vinculado à emissão e pelo mesmo lote nas três movimentações. O resultado identifica a transferência e a empresa da entrada original e procura no PDF o trecho do lote posterior ao primeiro hífen.', ['type' => 'object', 'properties' => ['invoice_id' => ['type' => 'integer', 'minimum' => 1], 'invoice_number' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 100]], 'additionalProperties' => false], [self::SCOPE]);
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        $invoiceId = (int) ($arguments['invoice_id'] ?? 0);
        $invoiceNumber = trim((string) ($arguments['invoice_number'] ?? ''));
        if (($invoiceId < 1 && $invoiceNumber === '') || ($invoiceId > 0 && $invoiceNumber !== '')) return $this->presenter->functionalError('inecel_certificate_invalid_invoice', 'Informe exatamente um identificador: o número da NF de saída ou o ID interno T007_Id.', 'tools_call_inecel_certificate_invalid_invoice');
        try { $rows = $invoiceId > 0 ? $this->repository->trace($context->getCompanyId(), $invoiceId) : $this->repository->traceByInvoiceNumber($context->getCompanyId(), $invoiceNumber); }
        catch (\Throwable $exception) { return $this->presenter->functionalError('inecel_certificate_unavailable', 'Não foi possível rastrear o certificado agora. Tente novamente.', 'tools_call_inecel_certificate_unavailable'); }
        if ($rows === []) {
            try { $invoice = $invoiceId > 0 ? $this->repository->findOutgoingInvoiceById($context->getCompanyId(), $invoiceId) : $this->repository->findOutgoingInvoiceByNumber($context->getCompanyId(), $invoiceNumber); }
            catch (\Throwable $exception) { return $this->presenter->functionalError('inecel_certificate_unavailable', 'Não foi possível rastrear o certificado agora. Tente novamente.', 'tools_call_inecel_certificate_unavailable'); }
            if ($invoice === null) return $this->presenter->functionalError('inecel_certificate_not_found', 'A NF de saída não foi encontrada nesta empresa.', 'tools_call_inecel_certificate_not_found');
            return $this->presenter->functionalError('inecel_certificate_trace_pending', 'A NF de saída foi encontrada, mas a trilha do certificado ainda não está disponível. Verifique se a OP foi finalizada e se a baixa dos materiais e lotes foi registrada.', 'tools_call_inecel_certificate_trace_pending', ['outgoing_invoice_id' => (int) $invoice['outgoing_invoice_id'], 'outgoing_invoice_number' => $invoice['outgoing_invoice_number']]);
        }

        $matches = [];
        foreach (array_slice($rows, 0, 100) as $row) $matches[] = $this->present($row, $context);
        $structured = ['outgoing_invoice_id' => (int) $rows[0]['outgoing_invoice_id'], 'outgoing_invoice_number' => $rows[0]['outgoing_invoice_number'], 'matches' => $matches, 'has_more' => count($rows) > 100];
        return $this->presenter->collection($structured, 'matches', 'trilha de certificado', 'trilhas de certificado', [
            ['path' => 'production_order_id', 'label' => 'OP'], ['path' => 'production_company_id', 'label' => 'Empresa da OP'], ['path' => 'outgoing_lot', 'label' => 'Lote de saída'], ['path' => 'certificate_lot', 'label' => 'Lote procurado'], ['path' => 'transfer.invoice_number', 'label' => 'NF de transferência'], ['path' => 'transfer.company_id', 'label' => 'Empresa da transferência'], ['path' => 'inbound_invoice_number', 'label' => 'NF de entrada'], ['path' => 'inbound_company_id', 'label' => 'Empresa da entrada'], ['path' => 'document.certificate_status', 'label' => 'Certificado', 'format' => 'status', 'map' => ['available' => 'Disponível', 'not_found' => 'Não encontrado']], ['path' => 'document.file_name', 'label' => 'Documento'], ['path' => 'document.download_url', 'label' => 'PDF'], ['path' => 'document.download_available_for', 'label' => 'PDF disponível por'], ['path' => 'document.lot_found_in_text', 'label' => 'Lote no PDF'],
        ], 'tools_call_inecel_certificate_trace');
    }

    /** @param array<string,mixed> $row @return array<string,mixed> */
    private function present(array $row, ExecutionContext $context): array
    {
        $certificateLot = $this->certificateLot((string) ($row['inbound_lot'] ?? ''));
        $documentId = (int) ($row['document_id'] ?? 0);
        $fileName = trim((string) ($row['document_file_name'] ?? ''));
        $document = ['id' => $documentId ?: null, 'file_name' => $fileName === '' ? null : $fileName, 'certificate_status' => $documentId > 0 && $fileName !== '' ? 'available' : 'not_found', 'download_available_for' => null, 'text_extractable' => null, 'lot_found_in_text' => null];
        if ($documentId > 0 && $fileName !== '') {
            $document['download_url'] = $this->downloads === null ? null : $this->downloads->issue($documentId, $fileName, $context, (int) ($row['inbound_company_id'] ?? $context->getCompanyId()));
            if ($document['download_url'] !== null) $document['download_available_for'] = CertificateDownloadLinkService::DOWNLOAD_TTL_MINUTES . ' minutos';
            $extension = (string) pathinfo($fileName, PATHINFO_EXTENSION);
            if (preg_match('/\A[a-zA-Z0-9]{1,10}\z/', $extension) === 1 && strtolower($extension) === 'pdf') {
                $text = $this->pdfText->extract($this->attachmentsDirectory . '/' . $documentId . '.' . $extension);
                $document['text_extractable'] = $text !== null;
                $document['lot_found_in_text'] = $text === null ? null : ($certificateLot !== '' && stripos($text, $certificateLot) !== false);
            }
        }
        $transfer = null;
        if ((int) ($row['transfer_invoice_id'] ?? 0) > 0) {
            $transfer = [
                'invoice_id' => (int) $row['transfer_invoice_id'],
                'invoice_number' => $row['transfer_invoice_number'],
                'company_id' => (int) $row['transfer_company_id'],
                'item_id' => (int) $row['transfer_item_id'],
                'received_invoice_id' => (int) $row['material_inbound_invoice_id'],
                'received_invoice_number' => $row['material_inbound_invoice_number'],
                'received_company_id' => (int) $row['material_inbound_company_id'],
            ];
        }
        return ['outgoing_invoice_id' => (int) $row['outgoing_invoice_id'], 'outgoing_invoice_number' => $row['outgoing_invoice_number'], 'outgoing_item_id' => (int) $row['outgoing_item_id'], 'outgoing_product_id' => $row['outgoing_product_id'], 'outgoing_lot' => $row['outgoing_lot'], 'outgoing_lots' => $row['outgoing_lots'] ?? $row['outgoing_lot'], 'production_order_id' => (int) $row['production_order_id'], 'production_company_id' => (int) ($row['production_company_id'] ?? $context->getCompanyId()), 'consumption_id' => (int) $row['consumption_id'], 'inbound_lot' => $row['inbound_lot'], 'certificate_lot' => $certificateLot, 'inbound_invoice_id' => (int) $row['inbound_invoice_id'], 'inbound_invoice_number' => $row['inbound_invoice_number'], 'inbound_company_id' => (int) ($row['inbound_company_id'] ?? $context->getCompanyId()), 'inbound_at' => $row['inbound_at'], 'inbound_product_code' => $row['inbound_product_code'], 'inbound_product_description' => $row['inbound_product_description'], 'transfer' => $transfer, 'document' => $document];
    }

    private function certificateLot(string $lot): string
    {
        $parts = explode('-', $lot, 2);
        return trim(count($parts) === 2 ? $parts[1] : $lot);
    }
}
