<?php
namespace hardness;

class PdfTextQualityAnalyzer
{
    public function analyze($text, array $context = array())
    {
        $text = (string) $text;
        $normalized = preg_replace('/\s+/u', ' ', trim($text));
        $length = strlen($normalized);
        $lines = preg_split('/\R/u', $text);
        $lineCount = 0;
        $keywordHits = 0;
        $numericLineHits = 0;
        $keywords = isset($context['keywords']) && is_array($context['keywords']) ? $context['keywords'] : array(
            'descricao',
            'descrição',
            'desc',
            'produto',
            'quantidade',
            'qtd',
            'qtde',
            'un',
            'und',
            'unid',
        );

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $lineCount++;

            if (preg_match('/\d+/', $line) && preg_match('/[A-Za-zÀ-ÿ]/u', $line)) {
                $numericLineHits++;
            }

            $lower = strtolower($line);
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && strpos($lower, strtolower($keyword)) !== false) {
                    $keywordHits++;
                    break;
                }
            }
        }

        $score = 0;
        if ($length > 0) {
            $score += min(30, (int) floor($length / 200));
        }
        if ($lineCount > 0) {
            $score += min(20, $lineCount);
        }
        if ($keywordHits > 0) {
            $score += min(30, $keywordHits * 8);
        }
        if ($numericLineHits > 0) {
            $score += min(20, $numericLineHits * 4);
        }

        $usable = true;
        $reason = 'Texto aproveitavel.';

        if ($length === 0) {
            $usable = false;
            $reason = 'Texto vazio.';
        } elseif ($length < 80) {
            $usable = false;
            $reason = 'Texto muito curto para parser confiavel.';
        } elseif ($keywordHits === 0 && $numericLineHits === 0) {
            $usable = false;
            $reason = 'Texto sem sinais de tabela ou estrutura util.';
        } elseif ($lineCount < 4) {
            $usable = false;
            $reason = 'Poucas linhas extraidas.';
        }

        return array(
            'isUsable' => $usable,
            'score' => min(100, $score),
            'reason' => $reason,
            'metrics' => array(
                'length' => $length,
                'line_count' => $lineCount,
                'keyword_hits' => $keywordHits,
                'numeric_line_hits' => $numericLineHits,
            ),
        );
    }
}
