<?php
namespace hardness;

require_once __DIR__ . '/PdfParserUtils.php';
require_once __DIR__ . '/PdfBlock.php';
require_once __DIR__ . '/PdfTableHeaderDetectionResult.php';
require_once __DIR__ . '/PdfColumnMap.php';
require_once __DIR__ . '/PdfStructuredLine.php';
require_once __DIR__ . '/PdfFieldInferenceResult.php';
require_once __DIR__ . '/PdfConsistencyResult.php';
require_once __DIR__ . '/PdfBlockDetector.php';
require_once __DIR__ . '/PdfTableHeaderDetector.php';
require_once __DIR__ . '/PdfColumnMapper.php';
require_once __DIR__ . '/PdfProductLineBuilder.php';
require_once __DIR__ . '/PdfFieldInferer.php';
require_once __DIR__ . '/PdfConsistencyValidator.php';
require_once __DIR__ . '/PdfParseDiagnosticCollector.php';

class TabelaGenericaParser implements PdfItemParserInterface
{
    protected $blockDetector;
    protected $headerDetector;
    protected $columnMapper;
    protected $lineBuilder;
    protected $fieldInferer;
    protected $validator;
    protected $diagnostics;

    public function __construct(
        PdfBlockDetector $blockDetector = null,
        PdfTableHeaderDetector $headerDetector = null,
        PdfColumnMapper $columnMapper = null,
        PdfProductLineBuilder $lineBuilder = null,
        PdfFieldInferer $fieldInferer = null,
        PdfConsistencyValidator $validator = null,
        PdfParseDiagnosticCollector $diagnostics = null
    ) {
        $this->blockDetector = $blockDetector ?: new PdfBlockDetector();
        $this->headerDetector = $headerDetector ?: new PdfTableHeaderDetector();
        $this->columnMapper = $columnMapper ?: new PdfColumnMapper();
        $this->lineBuilder = $lineBuilder ?: new PdfProductLineBuilder();
        $this->fieldInferer = $fieldInferer ?: new PdfFieldInferer();
        $this->validator = $validator ?: new PdfConsistencyValidator();
        $this->diagnostics = $diagnostics ?: new PdfParseDiagnosticCollector();
    }

    public function parse(PdfExtractionResult $result, array $context = array())
    {
        $this->diagnostics = new PdfParseDiagnosticCollector();
        $this->diagnostics->startDocument(array(
            'text_length' => strlen((string) $result->rawText),
            'line_count' => count(preg_split('/\R/u', (string) $result->rawText)),
            'quality_score' => isset($context['quality']['score']) ? (int) $context['quality']['score'] : null,
            'quality_reason' => isset($context['quality']['reason']) ? $context['quality']['reason'] : '',
            'parser' => 'TabelaGenericaParser',
        ));

        $items = array();
        $blocks = $this->blockDetector->detect($result->rawText);
        $pendingFragments = array();
        $pendingLineNumbers = array();

        foreach ($blocks as $blockIndex => $block) {
            $this->diagnostics->addBlock($block);

            if ($block->type !== 'product_table') {
                if ($this->isProductContinuationBlock($block) && isset($blocks[$blockIndex + 1]) && $blocks[$blockIndex + 1]->type === 'product_table') {
                    foreach ($block->lines as $line) {
                        $pendingFragments[] = isset($line['rawText']) ? $line['rawText'] : $line['text'];
                        $pendingLineNumbers[] = $line['lineNumber'];
                    }
                    continue;
                }

                $this->collectIgnoredBlockLines($block);
                continue;
            }

            $headerResult = $this->headerDetector->detect($block);
            $columnMap = $this->columnMapper->map($block, $headerResult);
            $structuredLines = $this->lineBuilder->build($block, $columnMap, $headerResult, $pendingFragments, $pendingLineNumbers);
            $pendingFragments = array();
            $pendingLineNumbers = array();

            foreach ($structuredLines as $structuredLine) {
                $inference = $this->fieldInferer->infer($structuredLine, $columnMap, $context);
                $validation = $this->validator->validate($inference, $context);

                $item = $inference->item;
                $item->sourceBlock = $block->type;
                $item->sourceMethod = $inference->method;
                $item->validationScore = $validation->score;
                $item->validationReason = $validation->reason;
                $item->section = $this->resolveItemSection($item->section);

                $lineInfo = array(
                    'lineStart' => $structuredLine->lineStart,
                    'lineEnd' => $structuredLine->lineEnd,
                    'rawText' => $structuredLine->rawText,
                    'block' => $block->type,
                    'method' => $inference->method,
                    'inferenceReason' => $inference->reason,
                );

                if ($validation->ignored) {
                    $this->diagnostics->addIgnoredLine($lineInfo, $validation->reason, array(
                        'score' => $validation->score,
                        'missingFields' => $validation->missingFields,
                    ));
                    continue;
                }

                $this->diagnostics->addAcceptedLine($lineInfo, array(
                    'score' => $validation->score,
                    'hasContinuation' => count($structuredLine->lines) > 1,
                    'continuationLines' => max(0, count($structuredLine->lines) - 1),
                    'inferenceReason' => $inference->reason,
                ));
                $this->diagnostics->addItem($item, array(
                    'confidence' => $item->confidence,
                    'score' => $validation->score,
                    'missingFields' => $validation->missingFields,
                    'method' => $inference->method,
                    'inferenceReason' => $inference->reason,
                    'sourceBlock' => $block->type,
                    'hasContinuation' => count($structuredLine->lines) > 1,
                    'continuationLines' => max(0, count($structuredLine->lines) - 1),
                ));
                $items[] = $item;
            }
        }

        return $items;
    }

    protected function isProductContinuationBlock(PdfBlock $block)
    {
        if ($block->type !== 'section' || count($block->lines) !== 1) {
            return false;
        }

        return PdfParserUtils::isProductContinuationLine($block->lines[0]['text']);
    }

    public function getDiagnostics()
    {
        return $this->diagnostics ? $this->diagnostics->toArray() : array();
    }

    protected function collectIgnoredBlockLines(PdfBlock $block)
    {
        foreach ($block->lines as $line) {
            if (trim((string) $line['text']) === '') {
                continue;
            }

            if (PdfParserUtils::isEmailLine($line['text'])) {
                $reason = 'Linha de email fora da tabela de produtos.';
            } elseif (PdfParserUtils::isPhoneLine($line['text'])) {
                $reason = 'Linha de telefone fora da tabela de produtos.';
            } elseif (PdfParserUtils::isTotalsLine($line['text'])) {
                $reason = 'Linha de totalizacao fora da tabela de produtos.';
            } else {
                $reason = 'Linha nao pertencente a produto.';
            }

            $this->diagnostics->addIgnoredLine(array(
                'lineStart' => $line['lineNumber'],
                'lineEnd' => $line['lineNumber'],
                'rawText' => $line['text'],
                'block' => $block->type,
                'method' => 'block_skip',
            ), $reason);
        }
    }

    protected function resolveItemSection($section)
    {
        $section = trim((string) $section);
        if ($section === '') {
            return '';
        }

        $lower = strtolower($section);
        if (PdfParserUtils::containsAny($lower, array(
            'cidade', 'uf', 'endereco', 'endereço', 'bairro', 'cep', 'cliente', 'cnpj', 'contato',
            'fone', 'telefone', 'email', 'e-mail', 'emissao', 'emissão', 'proposta', 'entrega',
        ))) {
            return '';
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $section, $mLetters);
        $upper = preg_match_all('/[A-ZÀ-Ý]/u', $section, $mUpper);
        $hasDigits = (bool) preg_match('/\d/u', $section);
        $isMostlyUpper = $letters > 0 ? ($upper / $letters) >= 0.6 : false;

        if ($letters >= 10 && !$hasDigits && $isMostlyUpper) {
            return $section;
        }

        return '';
    }
}
