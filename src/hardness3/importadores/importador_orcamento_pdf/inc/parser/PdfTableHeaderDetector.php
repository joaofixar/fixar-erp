<?php
namespace hardness;

class PdfTableHeaderDetector
{
    public function detect(PdfBlock $block)
    {
        $result = new PdfTableHeaderDetectionResult();

        foreach ($block->lines as $index => $line) {
            $text = $line['text'];
            if (!PdfParserUtils::isProductHeaderLine($text)) {
                continue;
            }

            $rawText = isset($line['rawText']) ? $line['rawText'] : $text;
            $anchors = PdfParserUtils::detectHeaderAnchors($rawText);
            if (count($anchors) < 2) {
                continue;
            }

            // Complementa ancoras com linhas vizinhas de cabecalho (ex.: "Quantidade" em linha acima)
            $anchors = $this->mergeNeighborAnchors($anchors, $block->lines, $index);
            $columns = PdfParserUtils::splitColumns($rawText);
            $result->exists = true;
            $result->score = $this->scoreHeaderLine($text, $columns, $anchors);
            $result->lineIndex = $line['lineNumber'] - $block->startLine;
            $result->lineNumber = $line['lineNumber'];
            $result->lineText = $text;
            $result->lineRawText = $rawText;
            $result->columns = $columns;
            $result->columnAnchors = $anchors;
            $result->reason = 'Cabecalho de tabela detectado.';
            return $result;
        }

        $result->reason = 'Cabecalho de tabela nao encontrado.';
        return $result;
    }

    protected function scoreHeaderLine($text, array $columns, array $anchors = array())
    {
        $lower = strtolower($text);
        $score = 0;
        foreach (array_values(array_unique(array_merge(
            PdfParserUtils::headerAliasesFor('description'),
            PdfParserUtils::headerAliasesFor('quantity'),
            PdfParserUtils::headerAliasesFor('unit'),
            PdfParserUtils::headerAliasesFor('code')
        ))) as $alias) {
            if (strpos($lower, $alias) !== false) {
                $score += 12;
            }
        }

        if (isset($anchors['description'])) {
            $score += 12;
        }
        if (isset($anchors['quantity'])) {
            $score += 10;
        }
        if (isset($anchors['unit'])) {
            $score += 8;
        }
        if (isset($anchors['code'])) {
            $score += 6;
        }

        $score += min(30, count($columns) * 3);
        return min(100, $score);
    }

    protected function mergeNeighborAnchors(array $anchors, array $lines, $currentIndex)
    {
        $start = max(0, (int) $currentIndex - 2);
        $end = min(count($lines) - 1, (int) $currentIndex + 2);

        for ($i = $start; $i <= $end; $i++) {
            if ($i === (int) $currentIndex || !isset($lines[$i])) {
                continue;
            }

            $row = $lines[$i];
            $rowText = isset($row['text']) ? (string) $row['text'] : '';
            if ($rowText === '' || PdfParserUtils::isProductRowCandidate($rowText)) {
                continue;
            }

            $raw = isset($row['rawText']) ? $row['rawText'] : $rowText;
            $neighborAnchors = PdfParserUtils::detectHeaderAnchors($raw);
            foreach ($neighborAnchors as $field => $anchor) {
                if (!isset($anchors[$field])) {
                    $anchors[$field] = $anchor;
                }
            }
        }

        uasort($anchors, function ($a, $b) {
            if ($a['start'] === $b['start']) {
                return strcmp($a['field'], $b['field']);
            }
            return $a['start'] < $b['start'] ? -1 : 1;
        });

        return $anchors;
    }
}
