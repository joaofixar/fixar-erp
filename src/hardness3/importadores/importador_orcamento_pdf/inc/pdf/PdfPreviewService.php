<?php
namespace hardness;

require_once __DIR__ . '/PdfTextExtractorInterface.php';
require_once __DIR__ . '/PdfExtractionResult.php';
require_once __DIR__ . '/PdfTextQualityAnalyzer.php';
require_once __DIR__ . '/PdfTextCoverageSnapshotBuilder.php';
require_once __DIR__ . '/PopplerPdfTextExtractor.php';

require_once dirname(__DIR__) . '/parser/PdfItemParserInterface.php';
require_once dirname(__DIR__) . '/parser/ParsedItem.php';
require_once dirname(__DIR__) . '/parser/TabelaGenericaParser.php';
require_once dirname(__DIR__) . '/parser/PdfParseEvaluator.php';
require_once dirname(__DIR__) . '/parser/PdfPositionalParser.php';
require_once dirname(__DIR__) . '/parser/OpenAiPdfParser.php';
require_once dirname(__DIR__) . '/parser/PdfAiCoverageValidator.php';

class PdfPreviewService
{
    protected $extractor;
    protected $qualityAnalyzer;
    protected $coverageSnapshotBuilder;
    protected $parser;
    protected $parseEvaluator;
    protected $positionalParser;
    protected $aiParser;
    protected $aiCoverageValidator;

    public function __construct(
        PdfTextExtractorInterface $extractor = null,
        PdfTextQualityAnalyzer $qualityAnalyzer = null,
        PdfTextCoverageSnapshotBuilder $coverageSnapshotBuilder = null,
        PdfItemParserInterface $parser = null,
        PdfParseEvaluator $parseEvaluator = null,
        PdfItemParserInterface $positionalParser = null,
        PdfItemParserInterface $aiParser = null,
        PdfAiCoverageValidator $aiCoverageValidator = null
    ) {
        $this->extractor = $extractor ?: new PopplerPdfTextExtractor();
        $this->qualityAnalyzer = $qualityAnalyzer ?: new PdfTextQualityAnalyzer();
        $this->coverageSnapshotBuilder = $coverageSnapshotBuilder ?: new PdfTextCoverageSnapshotBuilder();
        $this->parser = $parser ?: new TabelaGenericaParser();
        $this->parseEvaluator = $parseEvaluator ?: new PdfParseEvaluator();
        $this->positionalParser = $positionalParser ?: new PdfPositionalParser();
        $this->aiParser = $aiParser ?: new OpenAiPdfParser();
        $this->aiCoverageValidator = $aiCoverageValidator ?: new PdfAiCoverageValidator();
    }

    public function preview($pdfPath, array $context = array())
    {
        $result = $this->extractor->extract($pdfPath, $context);
        $preview = array(
            'extraction' => $result->toArray(),
            'quality' => array(
                'isUsable' => false,
                'score' => 0,
                'reason' => 'Extracao nao executada.',
                'metrics' => array(),
            ),
            'items' => array(),
            'diag' => array(),
        );

        $allowAiPdfFallback = !$result->success && $this->shouldAttemptAiPdfFallbackWithoutExtractedText($result, $context);
        if (!$result->success && !$allowAiPdfFallback) {
            $preview['quality']['reason'] = $result->message !== '' ? $result->message : 'Falha ao extrair texto do PDF.';
            $this->saveDiag($result, $preview, $context);
            return $preview;
        }

        if ($result->success) {
            $quality = $this->qualityAnalyzer->analyze($result->rawText, $context);
            $coverageSnapshot = $this->coverageSnapshotBuilder->build($result->rawText);
        } else {
            $quality = array(
                'isUsable' => false,
                'score' => 0,
                'reason' => $result->message !== '' ? $result->message : 'Falha ao extrair texto do PDF.',
                'metrics' => array(),
            );
            $coverageSnapshot = array();
        }
        $parseContext = $context;
        $parseContext['quality'] = $quality;
        $parseContext['coverage_snapshot'] = $coverageSnapshot;
        $attempts = array();
        $selected = null;

        if ($this->shouldPreferAiParser($parseContext)) {
            $aiItems = $this->aiParser->parse($result, $parseContext);
            $aiPreviewItems = $this->normalizeItems($aiItems);
            $aiDiag = $this->aiParser->getDiagnostics();
            $aiCoverageValidation = $this->aiCoverageValidator->validate(
                $coverageSnapshot,
                array('items' => $aiPreviewItems),
                array(
                    'total_items_detected' => isset($aiDiag['item_count']) ? $aiDiag['item_count'] : null,
                    'extraction_complete' => isset($aiDiag['status']) ? $aiDiag['status'] === 'ok' : null,
                )
            );
            $aiDiag['coverage_snapshot'] = $coverageSnapshot;
            $aiDiag['ai_coverage_validation'] = $aiCoverageValidation;
            $aiEvaluation = $this->parseEvaluator->evaluate($aiPreviewItems, $aiDiag, $result, $parseContext);
            $attempts[] = array(
                'parser' => 'OpenAiPdfParser',
                'items' => $aiPreviewItems,
                'diag' => $aiDiag,
                'evaluation' => $aiEvaluation->toArray(),
            );

            if ($aiCoverageValidation['decision'] === 'accept' && $aiEvaluation->status !== 'failed') {
                $selected = $attempts[count($attempts) - 1];
            }
        }

        if (!$result->success) {
            if ($selected === null && !empty($attempts)) {
                $selected = $attempts[count($attempts) - 1];
            }

            $preview['quality'] = $quality;
            $preview['coverage_snapshot'] = $coverageSnapshot;
            if ($selected !== null) {
                $preview['items'] = isset($selected['items']) ? $selected['items'] : array();
                $preview['diag'] = isset($selected['diag']) ? $selected['diag'] : array();
                $preview['selected_parser'] = isset($selected['parser']) ? $selected['parser'] : '';
                if (isset($selected['parser']) && $selected['parser'] !== '') {
                    $preview['diag']['selected_parser'] = $selected['parser'];
                }
                $preview['diag']['all_attempts'] = array_combine(
                    array_map(function ($attempt) {
                        return $attempt['parser'];
                    }, $attempts),
                    array_map(function ($attempt) {
                        return $attempt['diag'];
                    }, $attempts)
                );
                $preview['diag']['attempts'] = array_map(function ($attempt) {
                    return array(
                        'parser' => $attempt['parser'],
                        'evaluation' => $attempt['evaluation'],
                        'item_count' => count($attempt['items']),
                    );
                }, $attempts);
                $evaluation = isset($selected['evaluation']) && is_array($selected['evaluation']) ? $selected['evaluation'] : array();
                $preview['parse_status'] = isset($evaluation['parse_status']) ? $evaluation['parse_status'] : 'failed';
                $preview['parse_confidence'] = isset($evaluation['parse_confidence']) ? $evaluation['parse_confidence'] : 0;
                $preview['parse_failure_reasons'] = isset($evaluation['parse_failure_reasons']) ? $evaluation['parse_failure_reasons'] : array();
                $preview['should_try_positional_parser'] = false;
                $preview['summary'] = $this->buildSummary($preview, $evaluation);
            }

            $this->saveDiag($result, $preview, $context);
            return $preview;
        }

        $primaryItems = $this->parser->parse($result, $parseContext);
        $primaryPreviewItems = $this->normalizeItems($primaryItems);
        $primaryDiag = $this->parser->getDiagnostics();
        $primaryEvaluation = $this->parseEvaluator->evaluate($primaryPreviewItems, $primaryDiag, $result, $parseContext);
        $legacyAttempts = array();
        $legacyAttempts[] = array(
            'parser' => 'TabelaGenericaParser',
            'items' => $primaryPreviewItems,
            'diag' => $primaryDiag,
            'evaluation' => $primaryEvaluation->toArray(),
        );
        $attempts[] = $legacyAttempts[0];

        if ($selected === null && $primaryEvaluation->shouldTryPositionalParser) {
            $fallbackItems = $this->positionalParser->parse($result, $parseContext);
            $fallbackPreviewItems = $this->normalizeItems($fallbackItems);
            $fallbackDiag = $this->positionalParser->getDiagnostics();
            $fallbackEvaluation = $this->parseEvaluator->evaluate($fallbackPreviewItems, $fallbackDiag, $result, $parseContext);
            $legacyAttempts[] = array(
                'parser' => 'PdfPositionalParser',
                'items' => $fallbackPreviewItems,
                'diag' => $fallbackDiag,
                'evaluation' => $fallbackEvaluation->toArray(),
            );
            $attempts[] = $legacyAttempts[count($legacyAttempts) - 1];
        }

        if ($selected === null) {
            $selected = $this->chooseBestAttempt($legacyAttempts);
        }

        $preview['quality'] = $quality;
        $preview['coverage_snapshot'] = $coverageSnapshot;
        $preview['items'] = $selected['items'];
        $preview['diag'] = $selected['diag'];
        $preview['selected_parser'] = $selected['parser'];
        $preview['diag']['selected_parser'] = $selected['parser'];
        $preview['diag']['all_attempts'] = array_combine(
            array_map(function ($attempt) {
                return $attempt['parser'];
            }, $attempts),
            array_map(function ($attempt) {
                return $attempt['diag'];
            }, $attempts)
        );
        $preview['diag']['attempts'] = array_map(function ($attempt) {
            return array(
                'parser' => $attempt['parser'],
                'evaluation' => $attempt['evaluation'],
                'item_count' => count($attempt['items']),
            );
        }, $attempts);

        $preview['parse_status'] = $selected['evaluation']['parse_status'];
        $preview['parse_confidence'] = $selected['evaluation']['parse_confidence'];
        $preview['parse_failure_reasons'] = $selected['evaluation']['parse_failure_reasons'];
        $preview['should_try_positional_parser'] = $selected['evaluation']['should_try_positional_parser'];
        if (isset($selected['diag']['fallback_parser_used']) && $selected['diag']['fallback_parser_used'] !== '') {
            $preview['fallback_parser_used'] = $selected['diag']['fallback_parser_used'];
        }
        if (isset($selected['diag']['fallback_input_file']) && $selected['diag']['fallback_input_file'] !== '') {
            $preview['fallback_input_file'] = $selected['diag']['fallback_input_file'];
        }
        if (isset($selected['diag']['fallback_input_format']) && $selected['diag']['fallback_input_format'] !== '') {
            $preview['fallback_input_format'] = $selected['diag']['fallback_input_format'];
        }
        $preview['summary'] = $this->buildSummary($preview, $selected['evaluation']);

        $this->saveDiag($result, $preview, $context);

        return $preview;
    }

    protected function saveDiag(PdfExtractionResult $result, array $preview, array $context = array())
    {
        if ($result->workDir === '') {
            return;
        }

        $diagPath = rtrim($result->workDir, '/') . '/diag.json';
        $diagData = array(
            'extraction' => $result->toArray(),
            'quality' => isset($preview['quality']) ? $preview['quality'] : array(),
            'coverage_snapshot' => isset($preview['coverage_snapshot']) ? $preview['coverage_snapshot'] : array(),
            'diag' => isset($preview['diag']) ? $preview['diag'] : array(),
            'summary' => isset($preview['summary']) ? $preview['summary'] : array(),
        );

        if (isset($preview['selected_parser']) && $preview['selected_parser'] !== '') {
            $diagData['selected_parser'] = $preview['selected_parser'];
        }

        if (isset($preview['fallback_parser_used'])) {
            $diagData['fallback_parser_used'] = $preview['fallback_parser_used'];
        }
        if (isset($preview['fallback_input_file'])) {
            $diagData['fallback_input_file'] = $preview['fallback_input_file'];
        }
        if (isset($preview['fallback_input_format'])) {
            $diagData['fallback_input_format'] = $preview['fallback_input_format'];
        }

        if (isset($preview['items']) && is_array($preview['items'])) {
            $diagData['items_preview'] = array_slice($preview['items'], 0, 10);
        }

        @file_put_contents($diagPath, json_encode($diagData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $finalPath = rtrim($result->workDir, '/') . '/import_preview.json';
        $finalData = array(
            'summary' => isset($preview['summary']) ? $preview['summary'] : array(),
            'items' => isset($preview['items']) && is_array($preview['items']) ? $preview['items'] : array(),
        );
        @file_put_contents($finalPath, json_encode($finalData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $metadataPath = rtrim($result->workDir, '/') . '/metadata.json';
        $metadataData = array(
            'D024_Id' => isset($context['D024_Id']) ? (int) $context['D024_Id'] : null,
            'T003_Id' => isset($context['T003_Id']) ? (int) $context['T003_Id'] : null,
            'work_hash' => $result->workHash,
            'data_processamento' => date('c'),
            'caminho_pasta_trabalho' => $result->workDir,
            'caminho_pdf_original' => $result->pdfPath,
            'caminho_raw' => $result->rawTextPath,
            'caminho_diag' => $diagPath,
            'caminho_import_preview' => $finalPath,
        );
        if (isset($preview['coverage_snapshot']) && is_array($preview['coverage_snapshot'])) {
            $metadataData['coverage_snapshot'] = $preview['coverage_snapshot'];
        }
        if (isset($preview['diag']['ai_coverage_validation']) && is_array($preview['diag']['ai_coverage_validation'])) {
            $metadataData['ai_coverage_validation'] = $preview['diag']['ai_coverage_validation'];
        }
        if (isset($preview['selected_parser']) && $preview['selected_parser'] !== '') {
            $metadataData['selected_parser'] = $preview['selected_parser'];
        }
        if (isset($preview['fallback_parser_used'])) {
            $metadataData['fallback_parser_used'] = $preview['fallback_parser_used'];
        }
        if (isset($preview['fallback_input_file']) && $preview['fallback_input_file'] !== '') {
            $metadataData['fallback_input_file'] = $preview['fallback_input_file'];
            $metadataData['caminho_fallback_input'] = rtrim($result->workDir, '/') . '/' . $preview['fallback_input_file'];
        }
        if (isset($preview['fallback_input_format']) && $preview['fallback_input_format'] !== '') {
            $metadataData['fallback_input_format'] = $preview['fallback_input_format'];
        }
        @file_put_contents($metadataPath, json_encode($metadataData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    protected function buildSummary(array $preview, array $evaluation = array())
    {
        $items = isset($preview['items']) && is_array($preview['items']) ? $preview['items'] : array();
        $diag = isset($preview['diag']) && is_array($preview['diag']) ? $preview['diag'] : array();
        $lines = isset($diag['lines']) && is_array($diag['lines']) ? $diag['lines'] : array();
        $ignoredSummary = isset($lines['ignored_summary']) && is_array($lines['ignored_summary']) ? $lines['ignored_summary'] : array();

        $confidence = array(
            'lt_70' => 0,
            '70_84' => 0,
            '85_94' => 0,
            'ge_95' => 0,
        );

        foreach ($items as $item) {
            $score = 0;
            if (isset($item['confidence']) && (int) $item['confidence'] > 0) {
                $score = (int) $item['confidence'];
            } elseif (isset($item['validationScore'])) {
                $score = (int) $item['validationScore'];
            }

            if ($score < 70) {
                $confidence['lt_70']++;
            } elseif ($score < 85) {
                $confidence['70_84']++;
            } elseif ($score < 95) {
                $confidence['85_94']++;
            } else {
                $confidence['ge_95']++;
            }
        }

        return array(
            'total_items' => count($items),
            'total_ignored_lines' => isset($diag['summary']['ignored_count']) ? (int) $diag['summary']['ignored_count'] : 0,
            'main_ignored_reasons' => $ignoredSummary,
            'confidence_distribution' => $confidence,
            'parse_status' => isset($evaluation['parse_status']) ? $evaluation['parse_status'] : 'failed',
            'parse_confidence' => isset($evaluation['parse_confidence']) ? (int) $evaluation['parse_confidence'] : 0,
            'parse_failure_reasons' => isset($evaluation['parse_failure_reasons']) ? $evaluation['parse_failure_reasons'] : array(),
            'should_try_positional_parser' => isset($evaluation['should_try_positional_parser']) ? (bool) $evaluation['should_try_positional_parser'] : false,
            'parse_metrics' => isset($evaluation['metrics']) ? $evaluation['metrics'] : array(),
        );
    }

    protected function normalizeItems(array $items)
    {
        return array_map(function ($item) {
            return $item instanceof ParsedItem ? $item->toArray() : $item;
        }, $items);
    }

    protected function shouldPreferAiParser(array $context)
    {
        if (isset($context['prefer_ai_parser'])) {
            return (bool) $context['prefer_ai_parser'];
        }

        return false;
    }

    protected function shouldAttemptAiPdfFallbackWithoutExtractedText(PdfExtractionResult $result, array $context)
    {
        if (!$this->shouldPreferAiParser($context)) {
            return false;
        }

        $inputMode = isset($context['ai_input_mode']) ? (string) $context['ai_input_mode'] : 'pdf';
        if ($inputMode !== 'pdf') {
            return false;
        }

        if (!is_file($result->pdfPath) || !is_readable($result->pdfPath)) {
            return false;
        }

        return true;
    }

    protected function chooseBestAttempt(array $attempts)
    {
        $best = $attempts[0];
        foreach ($attempts as $attempt) {
            $bestRank = $this->statusRank(isset($best['evaluation']['parse_status']) ? $best['evaluation']['parse_status'] : 'failed');
            $attemptRank = $this->statusRank(isset($attempt['evaluation']['parse_status']) ? $attempt['evaluation']['parse_status'] : 'failed');

            if ($attemptRank > $bestRank) {
                $best = $attempt;
                continue;
            }

            if ($attemptRank === $bestRank
            ) {
                $bestInvalid = isset($best['evaluation']['metrics']['invalid_items']) ? (int) $best['evaluation']['metrics']['invalid_items'] : PHP_INT_MAX;
                $attemptInvalid = isset($attempt['evaluation']['metrics']['invalid_items']) ? (int) $attempt['evaluation']['metrics']['invalid_items'] : PHP_INT_MAX;
                if ($attemptInvalid < $bestInvalid) {
                    $best = $attempt;
                    continue;
                }

                if ($attemptInvalid === $bestInvalid
                    && (int) $attempt['evaluation']['parse_confidence'] > (int) $best['evaluation']['parse_confidence']
                ) {
                    $best = $attempt;
                }

                continue;
            }
        }

        return $best;
    }

    protected function statusRank($status)
    {
        if ($status === 'ok') {
            return 3;
        }
        if ($status === 'warning') {
            return 2;
        }

        return 1;
    }
}
