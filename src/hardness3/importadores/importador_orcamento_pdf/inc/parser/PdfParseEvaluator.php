<?php
namespace hardness;

require_once __DIR__ . '/PdfParserUtils.php';
require_once __DIR__ . '/PdfParseEvaluationResult.php';

class PdfParseEvaluator
{
    public function evaluate(array $items, array $diag = array(), PdfExtractionResult $result = null, array $context = array())
    {
        $evaluation = new PdfParseEvaluationResult();
        $confidence = 100;
        $reasons = array();

        $expectedItems = $this->extractExpectedItemCount($result ? $result->rawText : '');
        $actualItems = count($items);
        $invalidItems = 0;
        $lowScoreItems = 0;
        $contaminatedDescriptions = 0;
        $averageScore = 0;

        foreach ($items as $item) {
            $score = isset($item['validationScore']) ? (int) $item['validationScore'] : 0;
            $averageScore += $score;

            $description = trim((string) (isset($item['description']) ? $item['description'] : ''));
            $quantity = isset($item['quantity']) ? $item['quantity'] : null;
            $unit = trim((string) (isset($item['unit']) ? $item['unit'] : ''));

            if ($score < 50) {
                $lowScoreItems++;
            }

            if ($description === '' || ($quantity === null && $unit === '')) {
                $invalidItems++;
            }

            if ($this->isContaminatedDescription($description)) {
                $contaminatedDescriptions++;
            }
        }

        if ($actualItems > 0) {
            $averageScore = (int) round($averageScore / $actualItems);
        }

        if ($actualItems === 0) {
            $confidence = 0;
            $reasons[] = 'Nenhum item foi extraido.';
        }

        if ($expectedItems !== null) {
            $diff = abs($expectedItems - $actualItems);
            if ($diff > 0) {
                $penalty = min(45, 10 + ($diff * 6));
                $confidence -= $penalty;
                $reasons[] = sprintf(
                    'Quantidade de itens divergente do PDF: esperado %d, obtido %d.',
                    $expectedItems,
                    $actualItems
                );
            }
        }

        if ($actualItems > 0) {
            $invalidRatio = $invalidItems / $actualItems;
            $lowScoreRatio = $lowScoreItems / $actualItems;
            $contaminatedRatio = $contaminatedDescriptions / $actualItems;

            if ($invalidRatio >= 0.4) {
                $confidence -= 30;
                $reasons[] = sprintf('Muitos itens invalidos ou incompletos (%d de %d).', $invalidItems, $actualItems);
            } elseif ($invalidRatio > 0) {
                $confidence -= 12;
            }

            if ($contaminatedRatio >= 0.25) {
                $confidence -= 22;
                $reasons[] = sprintf('Descricoes contaminadas detectadas em %d item(ns).', $contaminatedDescriptions);
            } elseif ($contaminatedDescriptions > 0) {
                $confidence -= 8;
            }

            if ($lowScoreRatio >= 0.5) {
                $confidence -= 18;
                $reasons[] = sprintf('Muitos itens com baixa consistencia (%d de %d).', $lowScoreItems, $actualItems);
            } elseif ($lowScoreItems > 0) {
                $confidence -= 6;
            }

            if ($averageScore >= 85 && !empty($reasons)) {
                $confidence -= 15;
                $reasons[] = 'Score medio alto demais para um resultado estruturalmente inconsistente.';
            }
        }

        $confidence = max(0, min(100, $confidence));
        $evaluation->confidence = $confidence;
        $evaluation->failureReasons = array_values(array_unique($reasons));
        $evaluation->shouldTryPositionalParser = $confidence < 70 || $actualItems === 0;
        $evaluation->metrics = array(
            'expected_items' => $expectedItems,
            'actual_items' => $actualItems,
            'invalid_items' => $invalidItems,
            'low_score_items' => $lowScoreItems,
            'contaminated_descriptions' => $contaminatedDescriptions,
            'average_validation_score' => $averageScore,
        );

        if ($confidence >= 85 && empty($evaluation->failureReasons)) {
            $evaluation->status = 'ok';
            $evaluation->shouldTryPositionalParser = false;
        } elseif ($confidence >= 70) {
            $evaluation->status = 'warning';
            $evaluation->shouldTryPositionalParser = false;
        } else {
            $evaluation->status = 'failed';
            $evaluation->shouldTryPositionalParser = true;
        }

        return $evaluation;
    }

    protected function extractExpectedItemCount($rawText)
    {
        if (!is_string($rawText) || trim($rawText) === '') {
            return null;
        }

        if (preg_match('/Total\s+de\s+Itens\s*:\s*(\d+)/iu', $rawText, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function isContaminatedDescription($description)
    {
        $description = PdfParserUtils::normalizeLine($description);
        if ($description === '') {
            return false;
        }

        if (PdfParserUtils::isTotalsLine($description) || PdfParserUtils::isProductHeaderLine($description)) {
            return true;
        }

        if (preg_match('/\b(?:observa[cç][aã]o|solicit|comprador|email|frete|pagamento|icms|ipi)\b/iu', $description)) {
            return true;
        }

        if (preg_match('/\b\d{1,2}\/\d{1,2}\/\d{2,4}\b/u', $description)) {
            return true;
        }

        $words = preg_split('/\s+/u', $description, -1, PREG_SPLIT_NO_EMPTY);
        if (!$words) {
            return false;
        }

        $numericTokens = 0;
        foreach ($words as $word) {
            if (preg_match('/^\d{4,}$/u', $word) || preg_match('/^\d+[.,]\d+$/u', $word)) {
                $numericTokens++;
            }
        }

        return count($words) > 0 && ($numericTokens / count($words)) >= 0.45;
    }
}
