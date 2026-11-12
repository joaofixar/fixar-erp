<?php
namespace hardness;

class PdfProductLineBuilder
{
    public function build(PdfBlock $block, PdfColumnMap $columnMap = null, PdfTableHeaderDetectionResult $headerResult = null, array $prefixFragments = array(), array $prefixLineNumbers = array())
    {
        $lines = $block->lines;
        $items = array();
        $current = null;
        $pendingFragments = $prefixFragments;
        $pendingLineNumbers = $prefixLineNumbers;
        $pendingCodes = array();
        $startIndex = 0;

        if ($headerResult && $headerResult->exists && isset($lines[$headerResult->lineIndex])) {
            $startIndex = $headerResult->lineIndex + 1;
        }

        for ($i = $startIndex; $i < count($lines); $i++) {
            $line = $lines[$i];
            $text = isset($line['text']) ? PdfParserUtils::normalizeLine($line['text']) : '';
            $rawText = isset($line['rawText']) ? PdfParserUtils::normalizeLayoutLine($line['rawText']) : $text;

            if ($text === '' || PdfParserUtils::isTotalsLine($text) || PdfParserUtils::isEmailLine($text) || PdfParserUtils::isPhoneLine($text)) {
                if ($current) {
                    $items[] = $current;
                    $current = null;
                }
                continue;
            }

            if (PdfParserUtils::isProductHeaderLine($text)) {
                continue;
            }

            if (PdfParserUtils::isStandaloneNumberLine($text) || $this->isLooseCodeFragmentLine($text)) {
                $code = trim((string) $text);
                if ($code !== '') {
                    if ($current) {
                        if ($this->nextUsefulLineLooksLikeNewItemStart($lines, $i)
                            && preg_match('/[A-Za-z]/u', $code)
                            && $this->currentHasExplicitCodeToken($current->rawText)) {
                            $items[] = $current;
                            $current = null;
                            $pendingCodes[] = $code;
                        } else {
                            $current->standaloneCodes[] = $code;
                            $current->lineEnd = $line['lineNumber'];
                            $current->lines[] = $line['lineNumber'];
                        }
                    } else {
                        $pendingCodes[] = $code;
                    }
                }
                continue;
            }

            if (preg_match('/^\s*\d{1,4}\s+/u', $text) && $this->isStrongNewItemStart($text, $lines, $i)) {
                if ($current) {
                    $items[] = $current;
                }
                $lineForRaw = $line;
                $lineForRaw['rawText'] = $rawText;
                $current = $this->newStructuredLine($lineForRaw, $block->metadata, $pendingFragments, $pendingLineNumbers, $pendingCodes);
                $pendingFragments = array();
                $pendingLineNumbers = array();
                $pendingCodes = array();
                continue;
            }

            if (preg_match('/^\s*\d{1,4}\s+/u', $text) && $current) {
                $current->rawText .= "\n" . $rawText;
                $current->lineEnd = $line['lineNumber'];
                $current->lines[] = $line['lineNumber'];
                $current->confidence = max(0, $current->confidence - 3);
                continue;
            }

            if ($current) {
                if ($this->looksLikeForwardContinuationFragment($text)) {
                    $pendingFragments[] = $rawText;
                    $pendingLineNumbers[] = $line['lineNumber'];
                    continue;
                }

                $current->rawText .= "\n" . $rawText;
                $current->lineEnd = $line['lineNumber'];
                $current->lines[] = $line['lineNumber'];
                $current->confidence = max(0, $current->confidence - 2);
                continue;
            }

            if ($this->looksLikeContinuationFragment($text)) {
                $pendingFragments[] = $rawText;
                $pendingLineNumbers[] = $line['lineNumber'];
                continue;
            }
        }

        if ($current) {
            $items[] = $current;
        }

        return $items;
    }

    protected function looksLikeNewItemStart($text)
    {
        $normalized = PdfParserUtils::normalizeLine($text);
        return (bool) preg_match('/^\s*\d{1,4}\s+/u', $normalized) && $this->isStrongNewItemStart($normalized, array(), -1);
    }

    protected function nextUsefulLineLooksLikeNewItemStart(array $lines, $currentIndex)
    {
        $start = (int) $currentIndex + 1;
        for ($j = $start; $j < count($lines); $j++) {
            $text = isset($lines[$j]['text']) ? $lines[$j]['text'] : '';
            $text = PdfParserUtils::normalizeLine($text);
            if ($text === '') {
                continue;
            }

            if (PdfParserUtils::isStandaloneNumberLine($text) || $this->isLooseCodeFragmentLine($text)) {
                continue;
            }

            return $this->looksLikeNewItemStart($text);
        }

        return false;
    }

    protected function isStrongNewItemStart($text, array $lines = array(), $index = -1)
    {
        $normalized = PdfParserUtils::normalizeLine($text);
        if (!preg_match('/^\s*\d{1,4}\s+(.+)$/u', $normalized, $m)) {
            return false;
        }

        if (PdfParserUtils::isProductRowCandidate($normalized)) {
            return true;
        }

        $rest = trim((string) $m[1]);
        if ($rest === '') {
            return false;
        }

        if (strlen($rest) < 12) {
            return false;
        }

        $words = preg_split('/\s+/u', $rest, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) >= 4) {
            return true;
        }

        if ($index >= 0 && $this->nextUsefulLineHasUnitOrQuantity($lines, $index)) {
            return true;
        }

        return false;
    }

    protected function nextUsefulLineHasUnitOrQuantity(array $lines, $currentIndex)
    {
        for ($j = (int) $currentIndex + 1; $j < count($lines); $j++) {
            $text = isset($lines[$j]['text']) ? PdfParserUtils::normalizeLine($lines[$j]['text']) : '';
            if ($text === '') {
                continue;
            }

            if (PdfParserUtils::isEmailLine($text) || PdfParserUtils::isPhoneLine($text) || PdfParserUtils::isTotalsLine($text)) {
                return false;
            }

            if (preg_match('/\b(un|und|unid|unidade|um|pc|ct|kg|m|mm|cm)\b/iu', $text)) {
                return true;
            }

            if (preg_match('/\b\d+(?:[.,]\d+)?\b/u', $text)) {
                return true;
            }

            return false;
        }

        return false;
    }

    protected function currentHasExplicitCodeToken($rawText)
    {
        $lines = preg_split('/\R/u', (string) $rawText);
        if (!$lines) {
            return false;
        }

        $main = PdfParserUtils::normalizeLine($lines[count($lines) - 1]);
        if ($main === '') {
            return false;
        }

        if (!preg_match('/^\d{1,4}\s+([A-Za-z0-9.\-\/_]{2,20})\s+/u', $main, $m)) {
            return false;
        }

        $token = isset($m[1]) ? $m[1] : '';
        return PdfParserUtils::looksLikeCode($token);
    }

    protected function newStructuredLine(array $line, array $metadata = array(), array $pendingFragments = array(), array $pendingLineNumbers = array(), array $pendingCodes = array())
    {
        $structured = new PdfStructuredLine();
        $structured->lineStart = !empty($pendingLineNumbers) ? $pendingLineNumbers[0] : $line['lineNumber'];
        $structured->lineEnd = $line['lineNumber'];
        $mainRaw = isset($line['rawText']) ? PdfParserUtils::normalizeLayoutLine($line['rawText']) : (isset($line['text']) ? PdfParserUtils::normalizeLine($line['text']) : '');
        $structured->sequence = $this->extractSequence(isset($line['text']) ? $line['text'] : $mainRaw);
        $structured->rawText = !empty($pendingFragments) ? implode("\n", $pendingFragments) . "\n" . $mainRaw : $mainRaw;
        $structured->lines = !empty($pendingLineNumbers) ? array_merge($pendingLineNumbers, array($line['lineNumber'])) : array($line['lineNumber']);
        $structured->blockType = 'product_table';
        $structured->confidence = 60;
        $structured->notes = isset($metadata['section']) ? (string) $metadata['section'] : '';
        $structured->standaloneCodes = array_values(array_unique(array_filter($pendingCodes, function ($value) {
            return trim((string) $value) !== '';
        })));
        return $structured;
    }

    protected function extractSequence($text)
    {
        if (preg_match('/^\s*(\d{1,4})\s+/u', $text, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    protected function isLooseCodeFragmentLine($text)
    {
        $text = trim((string) $text);
        if ($text === '' || preg_match('/\s/u', $text)) {
            return false;
        }

        if (!preg_match('/[A-Za-z]/u', $text) || !preg_match('/\d/u', $text)) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9.\-\/_]{2,20}$/u', $text);
    }

    protected function looksLikeForwardContinuationFragment($text)
    {
        $text = trim((string) $text);
        if ($text === '' || strlen($text) < 35) {
            return false;
        }

        if (!PdfParserUtils::isProductContinuationLine($text)) {
            return false;
        }

        return true;
    }

    protected function looksLikeContinuationFragment($text)
    {
        $text = trim((string) $text);
        if ($text === '' || PdfParserUtils::isProductHeaderLine($text) || PdfParserUtils::isTotalsLine($text) || PdfParserUtils::isEmailLine($text) || PdfParserUtils::isPhoneLine($text)) {
            return false;
        }

        if (preg_match('/^\d{1,4}\s+/u', $text)) {
            return false;
        }

        if (PdfParserUtils::isStandaloneNumberLine($text) || $this->isLooseCodeFragmentLine($text)) {
            return false;
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $text, $mLetters);
        $hasDigits = (bool) preg_match('/\d/u', $text);

        if (!$hasDigits && $letters < 4) {
            return false;
        }

        return $hasDigits || $letters >= 4;
    }
}
