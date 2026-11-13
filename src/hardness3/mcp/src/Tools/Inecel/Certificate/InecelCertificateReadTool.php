<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

/** Reads the text layer of a certificate already authorized by its production trace. */
final class InecelCertificateReadTool implements ToolInterface
{
    public const TEXT_CHUNK_CHARACTERS = 12000;

    private InecelCertificateDocumentRepositoryInterface $repository;
    private PdfTextExtractorInterface $pdfText;
    private string $attachmentsDirectory;
    private ToolResultPresenter $presenter;

    public function __construct(InecelCertificateDocumentRepositoryInterface $repository, PdfTextExtractorInterface $pdfText, string $attachmentsDirectory)
    {
        $this->repository = $repository;
        $this->pdfText = $pdfText;
        $this->attachmentsDirectory = rtrim($attachmentsDirectory, '/');
        $this->presenter = new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.inecel.certificate.read', 'Lê em partes o texto extraído do PDF de um certificado localizado pela rastreabilidade. Use o document_id retornado por hardness.inecel.certificate.trace. Quando has_more for verdadeiro, chame novamente com next_cursor até terminar. PDFs escaneados usam OCR em inglês e chinês simplificado quando não possuem camada de texto.', [
            'type' => 'object',
            'properties' => [
                'document_id' => ['type' => 'integer', 'minimum' => 1],
                'cursor' => ['type' => 'integer', 'minimum' => 0],
            ],
            'required' => ['document_id'],
            'additionalProperties' => false,
        ], [InecelCertificateTraceTool::SCOPE]);
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        $documentId = (int) ($arguments['document_id'] ?? 0);
        $cursor = (int) ($arguments['cursor'] ?? 0);
        if ($documentId < 1 || $cursor < 0) {
            return $this->presenter->functionalError('inecel_certificate_read_invalid_input', 'Informe um document_id válido e, quando necessário, um cursor maior ou igual a zero.', 'tools_call_inecel_certificate_read_invalid_input');
        }

        try {
            $document = $this->repository->findReadableDocument($context->getCompanyId(), $documentId);
        } catch (\Throwable $exception) {
            return $this->presenter->functionalError('inecel_certificate_read_unavailable', 'Não foi possível ler o certificado agora. Tente novamente.', 'tools_call_inecel_certificate_read_unavailable');
        }
        if ($document === null) {
            return $this->presenter->functionalError('inecel_certificate_document_not_found', 'O certificado solicitado não foi encontrado na rastreabilidade desta empresa.', 'tools_call_inecel_certificate_read_not_found');
        }

        $fileName = trim($document['file_name']);
        $extension = (string) pathinfo($fileName, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'pdf' || preg_match('/\A[a-zA-Z0-9]{1,10}\z/', $extension) !== 1) {
            return $this->presenter->functionalError('inecel_certificate_document_unsupported', 'O documento encontrado não é um PDF legível por esta ferramenta.', 'tools_call_inecel_certificate_read_unsupported');
        }

        try {
            $text = $this->pdfText->extract($this->attachmentsDirectory . '/' . $documentId . '.' . $extension);
        } catch (CertificateOcrBusyException $exception) {
            return $this->presenter->functionalError('inecel_certificate_ocr_busy', 'A leitura de certificado está temporariamente ocupada. Tente novamente em instantes.', 'tools_call_inecel_certificate_ocr_busy');
        }
        if ($text === null) {
            return $this->presenter->functionalError('inecel_certificate_text_unavailable', 'Não foi possível extrair texto deste PDF. Ele pode ser um documento escaneado sem camada de texto.', 'tools_call_inecel_certificate_read_text_unavailable');
        }

        $length = $this->length($text);
        if ($cursor > $length) {
            return $this->presenter->functionalError('inecel_certificate_read_invalid_cursor', 'O cursor informado está além do conteúdo disponível do certificado.', 'tools_call_inecel_certificate_read_invalid_cursor');
        }
        $chunk = $this->slice($text, $cursor, self::TEXT_CHUNK_CHARACTERS);
        $nextCursor = $cursor + $this->length($chunk);
        $hasMore = $nextCursor < $length;
        $structured = [
            'document_id' => $documentId,
            'file_name' => $fileName,
            'cursor' => $cursor,
            'next_cursor' => $hasMore ? $nextCursor : null,
            'has_more' => $hasMore,
            'total_characters' => $length,
            'text' => $chunk,
        ];
        $summary = 'Certificado ' . $fileName . ', trecho ' . ($cursor + 1) . '-' . ($cursor + $this->length($chunk)) . ' de ' . $length . ' caracteres.';
        if ($hasMore) $summary .= ' Há mais conteúdo; use next_cursor ' . $nextCursor . '.';
        return new ToolExecutionResult(['content' => [['type' => 'text', 'text' => $summary . "\n\n" . $chunk]], 'structuredContent' => $structured], 'tools_call_inecel_certificate_read');
    }

    private function length(string $text): int
    {
        return function_exists('mb_strlen') ? (int) mb_strlen($text, 'UTF-8') : strlen($text);
    }

    private function slice(string $text, int $offset, int $length): string
    {
        return function_exists('mb_substr') ? (string) mb_substr($text, $offset, $length, 'UTF-8') : substr($text, $offset, $length);
    }
}
