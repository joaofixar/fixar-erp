<?php
namespace hardness;

class PdfFieldInferer
{
    public function infer(PdfStructuredLine $line, PdfColumnMap $map = null, array $context = array())
    {
        $result = new PdfFieldInferenceResult();
        $result->lineInfo = $line->toArray();

        $item = new ParsedItem();
        $item->sequence = $line->sequence;
        $item->rawText = $line->rawText;
        $item->section = $line->notes;
        $item->sourceLineStart = $line->lineStart;
        $item->sourceLineEnd = $line->lineEnd;
        $item->sourceBlock = 'product_table';

        $parsed = $this->parseRowText($line, $map);
        if ($parsed !== null) {
            $item->codeClient = $parsed['codeClient'];
            $item->alternativeCodes = isset($parsed['alternativeCodes']) ? $parsed['alternativeCodes'] : array();
            $item->description = $parsed['description'];
            $item->unit = $parsed['unit'];
            $item->quantity = $parsed['quantity'];
            $item->ncm = isset($parsed['ncm']) ? $parsed['ncm'] : '';
            $item->unitPrice = isset($parsed['unitPrice']) ? $parsed['unitPrice'] : null;
            $item->total = isset($parsed['total']) ? $parsed['total'] : null;
            $item->notes = $parsed['notes'];
            $result->method = $parsed['method'];
            $result->score = $parsed['score'];
            $result->reason = $parsed['reason'];
            $result->item = $item;
            $result->missingFields = $this->missingFields($item);
            return $result;
        }

        $result->method = 'unparsed';
        $result->score = 0;
        $result->reason = 'Linha nao interpretada.';
        $result->item = $item;
        $result->missingFields = array('description', 'quantity', 'unit');
        return $result;
    }

    protected function parseRowText(PdfStructuredLine $line, PdfColumnMap $map = null)
    {
        $components = $this->extractRowComponents($line->rawText);
        if (!empty($line->standaloneCodes)) {
            $components['isolatedCodes'] = array_merge($components['isolatedCodes'], $line->standaloneCodes);
        }
        $components['isolatedCodes'] = $this->normalizeCodes($components['isolatedCodes']);
        if ($components['coreLine'] === '') {
            return null;
        }

        $structured = null;
        $layoutStructured = null;
        $multilineStructured = null;
        $layoutFallbackReason = '';
        if ($map && $map->hasHeader) {
            $layoutStructured = $this->parseLayoutColumnsLine($components, $map, $layoutFallbackReason);
            if ($layoutStructured !== null) {
                $layoutStructured['method'] = 'layout_columns';
                $layoutStructured['score'] = max(90, isset($layoutStructured['score']) ? (int) $layoutStructured['score'] : 90);
                $layoutStructured['reason'] = 'Linha interpretada por layout/colunas.';
            }
        }

        $multilineStructured = $this->parseMultilineStructured($components, $map);

        if ($layoutStructured !== null && $multilineStructured !== null) {
            $structured = $this->chooseBetterStructured($layoutStructured, $multilineStructured, $components['coreLine']);
        } elseif ($layoutStructured !== null) {
            $structured = $layoutStructured;
        } elseif ($multilineStructured !== null) {
            $structured = $multilineStructured;
        }

        if ($structured === null) {
            $structured = $this->parseGenericCoreLine($components['coreLine'], $map);
            if ($structured !== null) {
                $structured['method'] = 'generic_tail';
                if ($layoutFallbackReason !== '') {
                    $structured['reason'] = 'Fallback para generic_tail: ' . $layoutFallbackReason;
                } else {
                    $structured['reason'] = 'Fallback para generic_tail.';
                }
            }
        }

        if ($structured === null) {
            return null;
        }

        $structured['description'] = $this->mergeDescriptionFragments($structured['description'], $components['fragments']);
        $structured = $this->applyHeaderCodeFromRowPrefix($structured, $components['coreLine'], $map);
        $structured = $this->applyCodePriority($structured, $components['isolatedCodes'], $map);
        $structured = $this->enrichByTailTokens($structured, $components['coreLine']);
        $structured = $this->extractUnitQuantityFromDescription($structured, $line->rawText);
        $structured = $this->fixQuantityFromTaxTail($structured, $components['coreLine']);
        $structured['description'] = $this->cleanDescription($structured['description']);

        if ($structured['quantity'] !== null
            && (float) $structured['quantity'] === 0.0
            && preg_match('/\b\d\.\d{3}\b/u', PdfParserUtils::normalizeLine($components['coreLine']))) {
            $candidate = $this->firstPositiveQuantityToken($components['coreLine']);
            if ($candidate !== null) {
                $structured['quantity'] = $candidate;
            }
        }

        return $structured;
    }

    protected function extractRowComponents($text)
    {
        $lines = preg_split('/\R/u', (string) $text);
        $clean = array();

        foreach ($lines as $line) {
            $line = PdfParserUtils::normalizeLine($line);
            if ($line === '') {
                continue;
            }

            if (PdfParserUtils::isStandaloneNumberLine($line)) {
                continue;
            }

            if (PdfParserUtils::isEmailLine($line) || PdfParserUtils::isPhoneLine($line) || PdfParserUtils::isTotalsLine($line) || PdfParserUtils::isProductHeaderLine($line)) {
                continue;
            }

            $clean[] = $line;
        }

        if (!$clean) {
            return array(
                'coreLine' => '',
                'fragments' => array(),
                'isolatedCodes' => array(),
                'linesRaw' => array(),
                'rawText' => (string) $text,
            );
        }

        $coreIndex = $this->findCoreLineIndex($clean);
        if ($coreIndex === null) {
            $coreIndex = 0;
        }

        $coreLine = $clean[$coreIndex];
        $fragments = array();
        $isolatedCodes = array();

        foreach ($clean as $index => $line) {
            if ($index === $coreIndex) {
                continue;
            }

            if (PdfParserUtils::isStandaloneNumberLine($line) || $this->isStandaloneCodeFragmentLine($line)) {
                $isolatedCodes[] = $line;
                continue;
            }

            $fragments[] = $line;
        }

        return array(
            'coreLine' => $coreLine,
            'fragments' => $fragments,
            'isolatedCodes' => $isolatedCodes,
            'linesRaw' => $lines,
            'rawText' => (string) $text,
        );
    }

    protected function findCoreLineIndex(array $lines)
    {
        $bestIndex = null;
        $bestScore = -1;

        foreach ($lines as $index => $line) {
            $score = 0;

            if (preg_match('/^\s*\d{1,4}\s+/u', $line)) {
                $score += 30;
            }

            if (preg_match('/\b\d{6,8}\b/u', $line)) {
                $score += 35;
            }

            if (preg_match('/\b(?:un|und|unid|unidade|um|kg|g|m|mm|cm|cx|kit|pct|par|jg|ct|pc)\b/i', $line)) {
                $score += 20;
            }

            if (preg_match('/\d+(?:[.,]\d+)?\s+\d+(?:[.,]\d+)?\s+\d+(?:[.,]\d+)?\s+\d+(?:[.,]\d+)?$/u', $line)) {
                $score += 20;
            }

            if (PdfParserUtils::isProductRowCandidate($line)) {
                $score += 10;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIndex = $index;
            }
        }

        return $bestIndex;
    }

    protected function isStandaloneCodeFragmentLine($line)
    {
        $line = trim((string) $line);
        if ($line === '' || preg_match('/\s/u', $line)) {
            return false;
        }

        if (PdfParserUtils::isStandaloneNumberLine($line)) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9.\-\/_]{2,20}$/u', $line)
            && preg_match('/[A-Za-z]/u', $line)
            && preg_match('/\d/u', $line);
    }

    protected function parseLayoutColumnsLine(array $components, PdfColumnMap $map, &$fallbackReason = '')
    {
        if (!isset($map->spans) || !is_array($map->spans) || empty($map->spans['description'])) {
            $fallbackReason = 'colunas de layout indisponiveis';
            return null;
        }
        if (empty($map->spans['quantity'])) {
            $fallbackReason = 'coluna de quantidade ausente no layout';
            return null;
        }

        $rawLines = preg_split('/\R/u', (string) $components['rawText']);
        if (!$rawLines || !is_array($rawLines)) {
            $rawLines = array((string) $components['coreLine']);
        }

        $rows = array();
        foreach ($rawLines as $line) {
            $layout = PdfParserUtils::normalizeLayoutLine($line);
            $normalized = PdfParserUtils::normalizeLine($layout);
            if ($normalized === '' || PdfParserUtils::isProductHeaderLine($normalized) || PdfParserUtils::isTotalsLine($normalized) || PdfParserUtils::isEmailLine($normalized) || PdfParserUtils::isPhoneLine($normalized)) {
                continue;
            }

            $row = array(
                'raw' => $layout,
                'normalized' => $normalized,
                'code' => $this->sliceBySpan($layout, isset($map->spans['codeClient']) ? $map->spans['codeClient'] : null),
                'description' => $this->sliceBySpan($layout, isset($map->spans['description']) ? $map->spans['description'] : null),
                'unit' => $this->sliceBySpan($layout, isset($map->spans['unit']) ? $map->spans['unit'] : null),
                'quantity' => $this->sliceBySpan($layout, isset($map->spans['quantity']) ? $map->spans['quantity'] : null),
                'notes' => $this->sliceBySpan($layout, isset($map->spans['notes']) ? $map->spans['notes'] : null),
            );
            $row['codeParsed'] = $this->cleanCodeToken($row['code']);
            $row['score'] = $this->scoreLayoutRow($row);
            $rows[] = $row;
        }

        if (!$rows) {
            $fallbackReason = 'nenhuma linha compativel com layout';
            return null;
        }

        usort($rows, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return 0;
            }
            return $a['score'] > $b['score'] ? -1 : 1;
        });

        $best = $rows[0];
        $descriptionParts = array();
        $mainDescription = $this->deriveDescriptionFromLayoutRow($best, $map);
        if ($mainDescription !== '') {
            $descriptionParts[] = $mainDescription;
        }

        foreach ($rows as $row) {
            if ($row === $best) {
                continue;
            }

            if ($this->looksLikePureQuantityLine($row['normalized'])) {
                continue;
            }

            $candidateDescription = $this->deriveDescriptionFromLayoutRow($row, $map);
            if ($candidateDescription !== '') {
                $descriptionParts[] = $candidateDescription;
            }
        }

        $description = trim(implode(' ', array_unique(array_filter($descriptionParts, function ($value) {
            return trim((string) $value) !== '';
        }))));

        $quantity = $this->extractQuantityFromLayoutRow($best);
        $unit = PdfParserUtils::isUnitToken($best['unit']) ? PdfParserUtils::normalizeUnit($best['unit']) : '';

        if ($quantity === null || $unit === '') {
            foreach ($rows as $row) {
                $parsed = $this->extractUnitQtyFromLine($row['normalized']);
                if ($quantity === null && $parsed['quantity'] !== null) {
                    $quantity = $parsed['quantity'];
                }
                if ($unit === '' && $parsed['unit'] !== '') {
                    $unit = $parsed['unit'];
                }
            }
        }

        if ($description === '' || ($quantity === null && $unit === '')) {
            $fallbackReason = 'linha de layout sem campos suficientes';
            return null;
        }

        if (isset($map->spans['codeClient']) && $map->spans['codeClient'] !== null && $best['codeParsed'] === '') {
            $fallbackReason = 'coluna de codigo inconsistente';
            return null;
        }

        if (!$this->isLayoutDescriptionReliable($description, $best, $map)) {
            $fallbackReason = 'descricao de layout com baixa confianca';
            return null;
        }

        return array(
            'codeClient' => $best['codeParsed'],
            'description' => $description,
            'unit' => $unit,
            'quantity' => $quantity,
            'alternativeCodes' => array(),
            'ncm' => PdfParserUtils::isNcmToken($best['notes']) ? trim((string) $best['notes']) : '',
            'unitPrice' => null,
            'total' => null,
            'notes' => '',
            'method' => 'layout_columns',
            'score' => 93,
            'reason' => 'Linha interpretada por colunas de layout.',
        );
    }

    protected function parseMultilineStructured(array $components, PdfColumnMap $map = null)
    {
        $structured = $this->parseExplicitCoreLine($components['coreLine']);
        if ($structured !== null) {
            $structured['method'] = 'multiline';
            $structured['reason'] = 'Linha interpretada por heuristica estruturada.';
            return $structured;
        }

        $structured = $this->parseGenericCoreLine($components['coreLine'], $map);
        if ($structured === null) {
            return null;
        }

        if (count($components['fragments']) === 0) {
            return null;
        }

        $structured['method'] = 'multiline';
        $structured['score'] = max(70, (int) $structured['score']);
        $structured['reason'] = 'Linha interpretada por consolidacao multi-linha.';
        return $structured;
    }

    protected function sliceBySpan($line, $span)
    {
        if (!is_array($span) || !isset($span['start'])) {
            return '';
        }

        $start = (int) $span['start'];
        $end = isset($span['end']) ? $span['end'] : null;
        return PdfParserUtils::extractSliceByRange($line, $start, $end);
    }

    protected function scoreLayoutRow(array $row)
    {
        $score = 0;
        if (isset($row['codeParsed']) && $row['codeParsed'] !== '') {
            $score += 10;
        }
        if (trim((string) $row['description']) !== '') {
            $score += 22;
        }
        if (PdfParserUtils::isUnitToken($row['unit'])) {
            $score += 22;
        }
        if ($this->extractQuantityFromLayoutRow($row) !== null) {
            $score += 28;
        }
        if (PdfParserUtils::isNcmToken($row['notes'])) {
            $score += 8;
        }
        if (preg_match('/\b\d{1,3}(?:\.\d{3})*(?:,\d+)?\b/u', $row['normalized'])) {
            $score += 6;
        }

        return $score;
    }

    protected function looksLikePureQuantityLine($line)
    {
        $line = PdfParserUtils::normalizeLine($line);
        if ($line === '') {
            return true;
        }

        return (bool) preg_match('/^(?:[A-Za-z]{1,6}\s+)?\d+(?:[.,]\d+)?(?:\s+[A-Za-zÀ-ÿ]{1,20})?$/u', $line);
    }

    protected function looksLikeDataDominatedLine($line)
    {
        $line = PdfParserUtils::normalizeLine($line);
        if ($line === '') {
            return false;
        }

        $numericTokens = preg_match_all('/\b\d+(?:[.,]\d+)?\b/u', $line, $m1);
        $wordTokens = preg_match_all('/[A-Za-zÀ-ÿ]+/u', $line, $m2);
        if ($numericTokens >= 3 && $numericTokens >= $wordTokens) {
            return true;
        }

        return false;
    }

    protected function stripLeadingSequenceToken($text)
    {
        $text = PdfParserUtils::normalizeLine($text);
        return trim((string) preg_replace('/^\d{1,4}\s+/u', '', $text));
    }

    protected function extractUnitQtyFromLine($line)
    {
        $line = PdfParserUtils::normalizeLine($line);
        $result = array('unit' => '', 'quantity' => null);
        if ($line === '') {
            return $result;
        }

        if (preg_match('/\b(un|und|unid|unidade|um|pc|ct|kg|m|mm|cm)\b/iu', $line, $mUnit)) {
            $result['unit'] = PdfParserUtils::normalizeUnit($mUnit[1]);
        }

        if (preg_match('/\b(?:un|und|unid|unidade|um|pc|ct|kg|m|mm|cm)\b\D{0,8}\b(\d+(?:[.,]\d+)?)\b/iu', $line, $mQtyAfterUnit)) {
            $qty = PdfParserUtils::normalizeQuantity($mQtyAfterUnit[1]);
            if ($qty !== null && $qty > 0) {
                $result['quantity'] = $qty;
            }
        }

        if ($result['quantity'] === null && preg_match_all('/\b(\d+(?:[.,]\d+)?)\b/u', $line, $mQtyAll)) {
            foreach ($mQtyAll[1] as $token) {
                if (preg_match('/^\d{5,}$/', $token) || preg_match('/^\d\.\d{3}$/', $token)) {
                    continue;
                }
                $qty = PdfParserUtils::normalizeQuantity($token);
                if ($qty !== null && $qty > 0) {
                    $result['quantity'] = $qty;
                    break;
                }
            }
        }

        return $result;
    }

    protected function cleanCodeToken($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        if (PdfParserUtils::looksLikeCode($value)) {
            return $value;
        }

        if (preg_match_all('/[A-Za-z0-9.\-\/_]{2,20}/u', $value, $m)) {
            foreach ($m[0] as $token) {
                if (PdfParserUtils::looksLikeCode($token)) {
                    return $token;
                }
            }
        }

        return '';
    }

    protected function deriveDescriptionFromLayoutRow(array $row, PdfColumnMap $map = null)
    {
        $description = trim((string) $row['description']);
        $normalized = trim((string) $row['normalized']);

        if ($description !== '' && strlen($description) >= 8 && !$this->looksLikeDataDominatedLine($description)) {
            return $this->stripLeadingSequenceToken($description);
        }

        $candidate = $this->stripLeadingSequenceToken($normalized);
        if ($candidate === '') {
            return '';
        }

        if (!empty($row['codeParsed'])) {
            $candidate = preg_replace('/^\s*' . preg_quote($row['codeParsed'], '/') . '\s+/u', '', $candidate, 1);
        }

        if (isset($row['notes']) && PdfParserUtils::isNcmToken($row['notes'])) {
            $candidate = preg_replace('/\s+' . preg_quote(trim((string) $row['notes']), '/') . '\b.*$/u', '', $candidate, 1);
        }

        $candidate = preg_replace('/\b(?:un|und|unid|unidade|um|pc|ct|kg|m|mm|cm)\b\D{0,8}\d+(?:[.,]\d+)?(?:\s+R?\$?\s*\d+(?:[.,]\d+)?){0,4}\s*$/iu', '', $candidate, 1);
        return trim($candidate);
    }

    protected function extractQuantityFromLayoutRow(array $row)
    {
        $value = trim((string) $row['quantity']);
        if (PdfParserUtils::isQuantityToken($value)) {
            return PdfParserUtils::normalizeQuantity($value);
        }

        $parsed = $this->extractUnitQtyFromLine($row['normalized']);
        return $parsed['quantity'];
    }

    protected function isLayoutDescriptionReliable($description, array $bestRow, PdfColumnMap $map = null)
    {
        $description = PdfParserUtils::normalizeLine($description);
        if ($description === '') {
            return false;
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $description, $mLetters);
        $digits = preg_match_all('/\d/u', $description, $mDigits);
        if ($letters < 6) {
            return false;
        }

        if ($digits > 0 && $digits > ($letters * 0.9)) {
            return false;
        }

        if (isset($map->spans['codeClient']) && $map->spans['codeClient'] !== null) {
            $codeLen = strlen(trim((string) $bestRow['codeParsed']));
            if ($codeLen > 0 && $codeLen < 4) {
                return false;
            }
        }

        if (preg_match('/\b\d{5,8}\b/u', $description) && strlen($description) < 18) {
            return false;
        }

        return true;
    }

    protected function parseExplicitCoreLine($line)
    {
        $raw = PdfParserUtils::normalizeLine($line);
        $numberPattern = '(?:\d{1,3}(?:\.\d{3})*(?:,\d+)?|\d+(?:[.,]\d+)?)';
        $pattern = '/^\s*(\d{1,4})\s+(.+?)\s+([A-Z]{1,5})\s+(\d{6,8})\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s*$/u';

        if (!preg_match($pattern, $raw, $matches)) {
            return null;
        }

        return array(
            'codeClient' => '',
            'description' => trim($matches[2]),
            'unit' => PdfParserUtils::normalizeUnit($matches[3]),
            'quantity' => PdfParserUtils::normalizeQuantity($matches[5]),
            'alternativeCodes' => array(),
            'ncm' => trim($matches[4]),
            'unitPrice' => $this->normalizeDecimal($matches[6]),
            'total' => $this->normalizeDecimal($matches[8]),
            'notes' => '',
            'method' => 'generic_explicit',
            'score' => 92,
            'reason' => 'Linha interpretada por heuristica explicita.',
        );
    }

    protected function parseGenericCoreLine($line, PdfColumnMap $map = null)
    {
        $allowLeadingCode = !($map && $map->columns['codeClient'] !== null);
        return $this->parseGeneric($line, $allowLeadingCode);
    }

    protected function mergeDescriptionFragments($description, array $fragments)
    {
        $description = trim((string) $description);
        if (!$fragments) {
            return $description;
        }

        $extra = trim(implode(' ', $fragments));
        if ($extra === '') {
            return $description;
        }

        if ($description === '') {
            return $extra;
        }

        return trim($description . ' ' . $extra);
    }

    protected function parseStructuredColumns($text, PdfColumnMap $map = null)
    {
        $line = new PdfStructuredLine();
        $line->rawText = (string) $text;
        return $this->parseRowText($line, $map);
    }

    protected function parseGeneric($text, $allowLeadingCode = true)
    {
        $raw = PdfParserUtils::normalizeLine($text);
        $numberPattern = '(?:\d{1,3}(?:\.\d{3})*(?:,\d+)?|\d+(?:[.,]\d+)?)';
        $explicitPattern = '/^\s*(\d{1,4})\s+(?:\?\s+)?([A-Z0-9.\-\/_]{1,30})\s+(.*?)\s+(\d{6,8})\s+([A-Z]{1,5})\s+(' . $numberPattern . ')\s+(?:R?\$?\s*)?(' . $numberPattern . ')\s+(?:R?\$?\s*)?(' . $numberPattern . ')$/u';
        if (preg_match($explicitPattern, $raw, $matches)) {
            return array(
                'codeClient' => trim($matches[2]),
                'description' => trim($matches[3]),
                'unit' => PdfParserUtils::normalizeUnit($matches[5]),
                'quantity' => PdfParserUtils::normalizeQuantity($matches[6]),
                'alternativeCodes' => array(),
                'ncm' => trim($matches[4]),
                'unitPrice' => $this->normalizeDecimal($matches[7]),
                'total' => $this->normalizeDecimal($matches[8]),
                'notes' => '',
                'method' => 'generic_explicit',
                'score' => 92,
                'reason' => 'Linha interpretada por heuristica explicita.',
            );
        }

        $result = array(
            'codeClient' => '',
            'description' => '',
            'unit' => '',
            'quantity' => null,
            'alternativeCodes' => array(),
            'ncm' => '',
            'unitPrice' => null,
            'total' => null,
            'notes' => '',
            'method' => 'generic_tail',
            'score' => 45,
            'reason' => 'Linha interpretada por heuristica generica.',
        );

        $line = $raw;
        if ($line === '') {
            return $result;
        }

        $line = preg_replace('/^\s*\d{1,4}\s+/u', '', $line);
        $parts = preg_split('/\s+/u', $line, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return $result;
        }

        $quantity = null;
        $unit = '';

        while ($parts) {
            $last = $parts[count($parts) - 1];
            if ($quantity === null && PdfParserUtils::isQuantityToken($last)) {
                $quantity = PdfParserUtils::normalizeQuantity($last);
                array_pop($parts);
                continue;
            }

            if ($unit === '' && PdfParserUtils::isUnitToken($last)) {
                $unit = PdfParserUtils::normalizeUnit($last);
                array_pop($parts);
                continue;
            }

            break;
        }

        $result['quantity'] = $quantity;
        $result['unit'] = $unit;

        if ($allowLeadingCode && count($parts) >= 2 && PdfParserUtils::looksLikeCode($parts[0])) {
            $result['codeClient'] = $parts[0];
            array_shift($parts);
        }

        if ($unit === '' && $quantity !== null && !empty($parts)) {
            $lastIndex = count($parts) - 1;
            $lastToken = isset($parts[$lastIndex]) ? (string) $parts[$lastIndex] : '';
            if (preg_match('/^(.*?)(un|und|unid|unidade|um|pc|ct|kg|m|mm|cm)$/iu', $lastToken, $mUnitSuffix)) {
                $prefix = trim((string) $mUnitSuffix[1]);
                $suffixUnit = trim((string) $mUnitSuffix[2]);
                if ($suffixUnit !== '') {
                    $unit = PdfParserUtils::normalizeUnit($suffixUnit);
                    $result['unit'] = $unit;
                    if ($prefix !== '') {
                        $parts[$lastIndex] = $prefix;
                    } else {
                        array_pop($parts);
                    }
                }
            }
        }

        $result['description'] = trim(implode(' ', $parts));
        if ($result['description'] === '') {
            $result['description'] = $line;
        }

        if ($quantity !== null && $unit !== '') {
            $result['score'] = 80;
        } elseif ($quantity !== null) {
            $result['score'] = 70;
        } else {
            $result['score'] = 55;
        }

        return $result;
    }

    protected function applyCodePriority(array $structured, array $isolatedCodes, PdfColumnMap $map = null)
    {
        if (!isset($structured['alternativeCodes']) || !is_array($structured['alternativeCodes'])) {
            $structured['alternativeCodes'] = array();
        }

        $hasCodeColumn = $map && $map->columns['codeClient'] !== null;
        $codes = $this->normalizeCodes($isolatedCodes);

        if ($hasCodeColumn && !empty($codes)) {
            $structured['codeClient'] = $codes[0];
            $structured['alternativeCodes'] = array_slice($codes, 1);
            return $structured;
        }

        if ($structured['codeClient'] === '' && !empty($codes)) {
            $structured['codeClient'] = $codes[0];
            $structured['alternativeCodes'] = array_slice($codes, 1);
        }

        return $structured;
    }

    protected function applyHeaderCodeFromRowPrefix(array $structured, $coreLine, PdfColumnMap $map = null)
    {
        if (!$map || $map->columns['codeClient'] === null) {
            return $structured;
        }

        $description = trim((string) $structured['description']);
        if ($description === '') {
            return $structured;
        }

        if (trim((string) $structured['codeClient']) !== '') {
            return $structured;
        }

        $raw = PdfParserUtils::normalizeLine($coreLine);
        if (!preg_match('/^\s*\d{1,4}\s+([^\s]+)\s+(.+)$/u', $raw, $m)) {
            return $structured;
        }

        $token = trim((string) $m[1]);
        $rest = trim((string) $m[2]);
        if ($token === '' || $rest === '' || !PdfParserUtils::looksLikeCode($token)) {
            return $structured;
        }

        // Ex.: "SSI-4504 - ADAPTADOR...", "IN-3044 - EMENDA..."
        // Nesses casos o token costuma ser modelo no inicio da descricao, nao codigo da coluna.
        if (preg_match('/^\-/u', $rest)) {
            return $structured;
        }

        $descNorm = PdfParserUtils::normalizeLine($description);
        $restNorm = PdfParserUtils::normalizeLine($rest);
        if ($descNorm !== '' && strpos($descNorm, $token . ' ') === 0) {
            $descNorm = trim(substr($descNorm, strlen($token)));
        } elseif ($restNorm !== '' && strpos($restNorm, $token . ' ') === 0) {
            $restNorm = trim(substr($restNorm, strlen($token)));
        }

        $structured['codeClient'] = $token;
        if ($descNorm !== '') {
            $structured['description'] = $descNorm;
        } elseif ($restNorm !== '') {
            $structured['description'] = $restNorm;
        }

        return $structured;
    }

    protected function normalizeCodes(array $codes)
    {
        $normalized = array();
        foreach ($codes as $code) {
            $code = trim((string) $code);
            if ($code === '' || !PdfParserUtils::looksLikeCode($code)) {
                continue;
            }

            if (!in_array($code, $normalized, true)) {
                $normalized[] = $code;
            }
        }

        return $normalized;
    }

    protected function enrichByTailTokens(array $structured, $line)
    {
        $raw = PdfParserUtils::normalizeLine($line);
        if ($raw === '') {
            return $structured;
        }

        $numberPattern = '(?:\d{1,3}(?:\.\d{3})*(?:,\d+)?|\d+(?:[.,]\d+)?)';
        $pattern = '/\b([A-Z]{1,5})\s+(\d{6,8})\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s+(' . $numberPattern . ')\s*$/u';
        if (!preg_match($pattern, $raw, $m)) {
            return $structured;
        }

        if (trim((string) $structured['unit']) === '') {
            $structured['unit'] = PdfParserUtils::normalizeUnit($m[1]);
        }

        if ($structured['quantity'] === null && PdfParserUtils::isQuantityToken($m[3])) {
            $structured['quantity'] = PdfParserUtils::normalizeQuantity($m[3]);
        }

        if (!isset($structured['ncm']) || trim((string) $structured['ncm']) === '') {
            $structured['ncm'] = trim((string) $m[2]);
        }

        if (!isset($structured['unitPrice']) || $structured['unitPrice'] === null) {
            $structured['unitPrice'] = $this->normalizeDecimal($m[4]);
        }

        if (!isset($structured['total']) || $structured['total'] === null) {
            $structured['total'] = $this->normalizeDecimal($m[6]);
        }

        return $structured;
    }

    protected function normalizeDecimal($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }

    protected function extractUnitQuantityFromDescription(array $structured, $rawText)
    {
        $description = trim((string) $structured['description']);
        if ($description === '') {
            return $structured;
        }

        $unitAliases = '(?:un|und|pc|pç|kg|m|mt|barra)';
        $number = '(\d+(?:[.,]\d+)?)';

        // Caso "fogoun 5" -> "fogo un 5"
        $description = preg_replace('/([A-Za-zÀ-ÿ])(' . $unitAliases . ')\s*' . $number . '/iu', '$1 $2 $3', $description);
        // Caso "#20un" -> "#20 un"
        $description = preg_replace('/(\d{2,})(un|und|unid|unidade|um|pc|ct|kg|mm|cm)\b/iu', '$1 $2', $description);
        // Caso "virola16" -> "virola 16"
        $description = preg_replace('/([A-Za-zÀ-ÿ]{3,})(\d{1,4})\b/u', '$1 $2', $description);

        if (trim((string) $structured['unit']) === '' || $structured['quantity'] === null) {
            if (preg_match('/\b(' . $unitAliases . ')\s*' . $number . '\b/iu', $description, $m)) {
                if (trim((string) $structured['unit']) === '') {
                    $structured['unit'] = PdfParserUtils::normalizeUnit($m[1]);
                }
                if ($structured['quantity'] === null) {
                    $structured['quantity'] = PdfParserUtils::normalizeQuantity($m[2]);
                }
                $description = trim(preg_replace('/\b' . preg_quote($m[1], '/') . '\s*' . preg_quote($m[2], '/') . '\b/iu', ' ', $description, 1));
            }
        }

        // Caso quebrado: "un galvanizado ... 5 a fogo"
        if ((trim((string) $structured['unit']) === '' || $structured['quantity'] === null)
            && preg_match('/\b(' . $unitAliases . ')\b/iu', $description, $unitMatch, PREG_OFFSET_CAPTURE)) {
            $unitToken = $unitMatch[1][0];
            $unitPos = (int) $unitMatch[1][1];
            $tail = substr($description, $unitPos);
            if (preg_match('/\b' . $number . '\b/u', $tail, $qtyMatch)) {
                if (trim((string) $structured['unit']) === '') {
                    $structured['unit'] = PdfParserUtils::normalizeUnit($unitToken);
                }
                if ($structured['quantity'] === null) {
                    $structured['quantity'] = PdfParserUtils::normalizeQuantity($qtyMatch[1]);
                }
                $pattern = '/\b' . preg_quote($unitToken, '/') . '\b(?:\s+[^\d]{1,20}){0,3}\s+' . preg_quote($qtyMatch[1], '/') . '\b/iu';
                $description = trim(preg_replace($pattern, ' ', $description, 1));
            }
        }

        $structured['description'] = preg_replace('/\s{2,}/u', ' ', $description);
        return $structured;
    }

    protected function extractByColumns(array $parts, PdfColumnMap $map)
    {
        $code = $this->columnValue($parts, $map->columns['codeClient']);
        $desc = $this->columnValue($parts, $map->columns['description']);
        $unit = $this->columnValue($parts, $map->columns['unit']);
        $quantity = $this->columnValue($parts, $map->columns['quantity']);
        $notes = $this->columnValue($parts, $map->columns['notes']);

        if ($desc === '' && $unit === '' && $quantity === '') {
            return null;
        }

        return array(
            'codeClient' => $code,
            'description' => $desc,
            'unit' => PdfParserUtils::normalizeUnit($unit),
            'quantity' => PdfParserUtils::isQuantityToken($quantity) ? PdfParserUtils::normalizeQuantity($quantity) : null,
            'alternativeCodes' => array(),
            'ncm' => PdfParserUtils::isNcmToken($notes) ? trim((string) $notes) : '',
            'unitPrice' => null,
            'total' => null,
            'notes' => $notes,
            'method' => 'header_columns',
            'score' => 88,
            'reason' => 'Linha interpretada por mapeamento de colunas.',
        );
    }

    protected function columnValue(array $parts, $index)
    {
        if ($index === null || !isset($parts[$index])) {
            return '';
        }

        return trim((string) $parts[$index]);
    }

    protected function fixQuantityFromTaxTail(array $structured, $coreLine)
    {
        $raw = PdfParserUtils::normalizeLine($coreLine);
        if ($raw === '') {
            return $structured;
        }

        if (!preg_match('/\b\d\.\d{3}\b\s+(\d+(?:[.,]\d+)?)(?:\s+\d+(?:[.,]\d+)?){2,}/u', $raw, $m)) {
            return $structured;
        }

        $candidate = PdfParserUtils::normalizeQuantity($m[1]);
        if ($candidate === null) {
            return $structured;
        }

        if ($structured['quantity'] === null || (float) $structured['quantity'] === 0.0) {
            $structured['quantity'] = $candidate;
        }

        return $structured;
    }

    protected function cleanDescription($description)
    {
        $description = PdfParserUtils::normalizeLine($description);
        if ($description === '') {
            return '';
        }

        $tokens = preg_split('/\s+/u', $description, -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return '';
        }

        $filtered = array_values($tokens);

        while (!empty($filtered)) {
            $token = $filtered[0];
            if ($this->isFiscalNoiseToken($token)) {
                array_shift($filtered);
                continue;
            }
            break;
        }

        while (!empty($filtered)) {
            $token = $filtered[count($filtered) - 1];
            if ($this->isFiscalNoiseToken($token)) {
                array_pop($filtered);
                continue;
            }
            break;
        }

        if (empty($filtered)) {
            return $description;
        }

        $clean = trim(implode(' ', $filtered));
        return $clean !== '' ? $clean : $description;
    }

    protected function isFiscalNoiseToken($token)
    {
        $token = trim((string) $token);
        if ($token === '') {
            return false;
        }

        if (preg_match('/^\d\.\d{3}$/u', $token)) { // cfop ex. 5.403
            return true;
        }

        if (preg_match('/^\d{1,3}(?:\.\d{3})*(?:,\d{2,4})$/u', $token)) { // valores monetarios / aliquotas
            return true;
        }

        if (preg_match('/^(?:0|0,0+|0\.0+)$/u', $token)) {
            return true;
        }

        return false;
    }

    protected function firstPositiveQuantityToken($line)
    {
        $line = PdfParserUtils::normalizeLine($line);
        if ($line === '') {
            return null;
        }

        if (!preg_match_all('/\b\d+(?:[.,]\d+)?\b/u', $line, $m)) {
            return null;
        }

        foreach ($m[0] as $token) {
            if (preg_match('/^\d{5,}$/', $token) || preg_match('/^\d\.\d{3}$/', $token)) {
                continue;
            }
            $value = PdfParserUtils::normalizeQuantity($token);
            if ($value !== null && $value > 0) {
                return $value;
            }
        }

        return null;
    }

    protected function chooseBetterStructured(array $layout, array $multiline, $coreLine)
    {
        $layoutScore = $this->structuredQualityScore($layout, $coreLine, true);
        $multilineScore = $this->structuredQualityScore($multiline, $coreLine, false);

        if ($layoutScore >= $multilineScore + 8) {
            return $layout;
        }

        return $multiline;
    }

    protected function structuredQualityScore(array $structured, $coreLine, $isLayout)
    {
        $score = isset($structured['score']) ? (int) $structured['score'] : 0;
        $description = isset($structured['description']) ? PdfParserUtils::normalizeLine($structured['description']) : '';
        $quantity = isset($structured['quantity']) ? $structured['quantity'] : null;
        $unit = isset($structured['unit']) ? trim((string) $structured['unit']) : '';

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $description, $mLetters);
        $digits = preg_match_all('/\d/u', $description, $mDigits);
        if ($description !== '' && $letters > 6) {
            $score += 10;
        }
        if ($digits > 0 && $digits > ($letters * 0.8)) {
            $score -= 14;
        }
        if (preg_match('/^\d(?:[.,]\d+)?(?:\s+\d(?:[.,]\d+)?){2,}/u', $description)) {
            $score -= 28;
        }

        if ($quantity !== null && is_numeric($quantity)) {
            $q = (float) $quantity;
            if ($q > 0) {
                $score += 10;
            }
            if ($q >= 100000) {
                $score -= 40;
            }
        }

        if ($unit !== '') {
            $score += 6;
        }

        if ($isLayout) {
            if (trim((string) $structured['codeClient']) === '' && strpos(PdfParserUtils::normalizeLine($coreLine), '?') !== false) {
                $score -= 20;
            }
        } else {
            $score += 4;
        }

        return $score;
    }

    protected function missingFields(ParsedItem $item)
    {
        $missing = array();
        if (trim($item->description) === '') {
            $missing[] = 'description';
        }
        if ($item->quantity === null) {
            $missing[] = 'quantity';
        }
        if (trim($item->unit) === '') {
            $missing[] = 'unit';
        }
        return $missing;
    }
}
