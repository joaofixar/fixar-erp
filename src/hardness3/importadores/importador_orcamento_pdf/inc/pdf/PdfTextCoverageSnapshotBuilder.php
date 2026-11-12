<?php
namespace hardness;

class PdfTextCoverageSnapshotBuilder
{
    public function build($rawText)
    {
        $rawText = (string) $rawText;
        $normalizedText = preg_replace('/\r\n?/', "\n", $rawText);
        $trimmedText = trim((string) $normalizedText);
        $lines = preg_split('/\R/u', $normalizedText);

        $lineCount = 0;
        $candidateLines = 0;
        $enumerationMatches = array();
        $enumerationPattern = 'none';
        $longUsefulLines = 0;

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $lineCount++;

            if ($this->looksLikeCandidateItemLine($line)) {
                $candidateLines++;
            }

            $marker = $this->extractEnumerationMarker($line);
            if ($marker !== null) {
                $enumerationMatches[$marker['value']] = true;
                $enumerationPattern = $this->mergeEnumerationPattern($enumerationPattern, $marker['pattern']);
            }

            if (preg_match('/[A-Za-zÀ-ÿ]/u', $line) && strlen($line) >= 40) {
                $longUsefulLines++;
            }
        }

        $textLength = strlen(preg_replace('/\s+/u', ' ', $trimmedText));
        $hasUsefulText = $trimmedText !== ''
            && $textLength >= 80
            && ($candidateLines > 0 || $longUsefulLines >= 3);

        return array(
            'has_useful_text' => $hasUsefulText,
            'text_length' => $textLength,
            'line_count' => $lineCount,
            'candidate_lines_estimate' => $candidateLines,
            'enumeration_estimate' => !empty($enumerationMatches) ? count($enumerationMatches) : null,
            'enumeration_pattern' => $enumerationPattern,
            'raw_hash' => hash('sha256', $normalizedText),
        );
    }

    protected function looksLikeCandidateItemLine($line)
    {
        if (!preg_match('/[A-Za-zÀ-ÿ]/u', $line) || !preg_match('/\d/u', $line)) {
            return false;
        }

        $line = preg_replace('/\s+/u', ' ', trim((string) $line));
        if (strlen($line) < 20) {
            return false;
        }

        $lower = strtolower($line);
        if (preg_match('/^(c[oó]digo|revis[aã]o|emiss[aã]o|p[aá]ginas?|subtotal|valor\s+un|total|observa[cç][aã]o)\b/u', $lower)) {
            return false;
        }

        if (preg_match('/^\d+(?:\.\d+){0,2}\s+/u', $line)) {
            return true;
        }

        if (preg_match('/\b(?:un|und|unid|pc|pç|pct|kg|g|m|mm|cm|m2|m3|cx|kit|par|jg|ct)\b/iu', $line)) {
            return true;
        }

        if (preg_match('/\d+(?:[.,]\d+)?\s*$/u', $line)) {
            return true;
        }

        return preg_match('/[A-Za-zÀ-ÿ].*[A-Za-zÀ-ÿ]/u', $line) && preg_match('/\d/u', $line);
    }

    protected function extractEnumerationMarker($line)
    {
        if (preg_match('/^\s*(\d+\.\d+\.\d+)\s+/u', $line, $matches)) {
            return array(
                'value' => $matches[1],
                'pattern' => 'hierarchical',
            );
        }

        if (preg_match('/^\s*(\d+\.\d+)\s+/u', $line, $matches)) {
            return array(
                'value' => $matches[1],
                'pattern' => 'decimal_subitem',
            );
        }

        if (preg_match('/^\s*(\d+)\s+/u', $line, $matches)) {
            return array(
                'value' => ltrim($matches[1], '0') !== '' ? ltrim($matches[1], '0') : '0',
                'pattern' => 'integer',
            );
        }

        return null;
    }

    protected function mergeEnumerationPattern($current, $candidate)
    {
        $rank = array(
            'none' => 0,
            'integer' => 1,
            'decimal_subitem' => 2,
            'hierarchical' => 3,
        );

        if (!isset($rank[$candidate]) || !isset($rank[$current])) {
            return $current;
        }

        return $rank[$candidate] > $rank[$current] ? $candidate : $current;
    }
}
