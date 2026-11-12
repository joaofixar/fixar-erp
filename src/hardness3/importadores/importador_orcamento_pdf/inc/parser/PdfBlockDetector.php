<?php
namespace hardness;

class PdfBlockDetector
{
    public function detect($text)
    {
        $lines = preg_split('/\R/u', (string) $text);
        $normalizedLines = array();
        foreach ($lines as $index => $line) {
            $rawText = PdfParserUtils::normalizeLayoutLine($line);
            $normalizedLines[] = array(
                'index' => $index + 1,
                'text' => PdfParserUtils::normalizeLine($rawText),
                'rawText' => $rawText,
            );
        }

        $productStart = $this->findProductStart($normalizedLines);
        $totalsStart = $this->findTotalsStart($normalizedLines, $productStart);
        $footerStart = $this->findFooterStart($normalizedLines, $totalsStart);

        $blocks = array();
        $buffer = array();
        $bufferType = null;
        $bufferStart = null;
        $blockId = 1;
        $lastSection = '';

        foreach ($normalizedLines as $row) {
            $line = $row['text'];
            if ($line === '') {
                continue;
            }

            $lineType = $this->classifyLine($line, $row['index'], $productStart, $totalsStart, $footerStart);
            if ($lineType === 'section' && $this->isProductContinuationLine($line) && $this->nextUsefulLineIsProductRow($normalizedLines, $row['index'])) {
                $lineType = 'product_row';
            }
            $blockType = $this->resolveBlockType($lineType, $row['index'], $productStart, $totalsStart, $footerStart);

            if ($lineType === 'section' && $productStart !== null && $row['index'] < $productStart) {
                $lastSection = $line;
            }

            if ($bufferType !== null && $blockType !== $bufferType) {
                $blocks[] = $this->makeBlock($blockId++, $bufferType, $buffer, $bufferStart, $lastSection);
                $buffer = array();
                $bufferType = null;
                $bufferStart = null;
            }

            if ($bufferType === null) {
                $bufferType = $blockType;
                $bufferStart = $row['index'];
            }

            $buffer[] = array(
                'lineNumber' => $row['index'],
                'text' => $line,
                'rawText' => isset($row['rawText']) ? $row['rawText'] : $line,
                'type' => $lineType,
            );
        }

        if ($bufferType !== null && $buffer) {
            $blocks[] = $this->makeBlock($blockId++, $bufferType, $buffer, $bufferStart, $lastSection);
        }

        return $blocks;
    }

    protected function findProductStart(array $lines)
    {
        $firstHeader = null;
        $firstRow = null;

        foreach ($lines as $row) {
            if ($row['text'] === '') {
                continue;
            }

            if ($firstHeader === null && PdfParserUtils::isProductHeaderLine($row['text'])) {
                $firstHeader = $row['index'];
            }

            if ($firstRow === null && PdfParserUtils::isProductRowCandidate($row['text'])) {
                $firstRow = $row['index'];
            }
        }

        if ($firstHeader !== null) {
            return $firstHeader;
        }

        return $firstRow;
    }

    protected function findTotalsStart(array $lines, $productStart)
    {
        if ($productStart === null) {
            return null;
        }

        $seenProductRow = false;

        foreach ($lines as $row) {
            if ($row['index'] < $productStart) {
                continue;
            }

            if (PdfParserUtils::isProductRowCandidate($row['text'])) {
                $seenProductRow = true;
            }

            if ($seenProductRow && PdfParserUtils::isTotalsLine($row['text']) && !PdfParserUtils::isProductHeaderLine($row['text'])) {
                return $row['index'];
            }
        }

        return null;
    }

    protected function findFooterStart(array $lines, $totalsStart)
    {
        if ($totalsStart === null) {
            return null;
        }

        foreach ($lines as $row) {
            if ($row['index'] <= $totalsStart) {
                continue;
            }

            if (PdfParserUtils::isEmailLine($row['text']) || PdfParserUtils::isPhoneLine($row['text'])) {
                return $row['index'];
            }
        }

        return null;
    }

    protected function classifyLine($line, $lineNumber, $productStart, $totalsStart, $footerStart)
    {
        if ($line === '') {
            return 'other';
        }

        if (PdfParserUtils::isProductHeaderLine($line)) {
            return 'product_header';
        }

        if (PdfParserUtils::isEmailLine($line) || PdfParserUtils::isPhoneLine($line)) {
            return 'footer';
        }

        if (PdfParserUtils::isTotalsLine($line)) {
            return 'totals';
        }

        if (PdfParserUtils::isProductRowCandidate($line)) {
            return 'product_row';
        }

        if (PdfParserUtils::isHeaderOrClientLine($line)) {
            return 'client_data';
        }

        if (PdfParserUtils::isSectionLine($line)) {
            return 'section';
        }

        if ($footerStart !== null && $lineNumber >= $footerStart) {
            return 'footer';
        }

        if ($totalsStart !== null && $lineNumber >= $totalsStart) {
            return 'totals';
        }

        if ($productStart !== null && $lineNumber >= $productStart) {
            return 'product_table';
        }

        return 'header';
    }

    protected function resolveBlockType($lineType, $lineNumber, $productStart, $totalsStart, $footerStart)
    {
        if ($lineType === 'product_header' || $lineType === 'product_row' || $lineType === 'product_table') {
            return 'product_table';
        }

        if ($lineType === 'totals') {
            return 'totals';
        }

        if ($lineType === 'footer') {
            return 'footer';
        }

        if ($lineType === 'client_data' || $lineType === 'section') {
            return $lineType;
        }

        if ($productStart !== null && $lineNumber < $productStart) {
            return $lineType === 'other' ? 'header' : $lineType;
        }

        if ($totalsStart !== null && $lineNumber >= $totalsStart) {
            return 'totals';
        }

        if ($footerStart !== null && $lineNumber >= $footerStart) {
            return 'footer';
        }

        return $lineType === 'other' ? 'header' : $lineType;
    }

    protected function nextUsefulLineIsProductRow(array $lines, $currentIndex)
    {
        foreach ($lines as $row) {
            if ($row['index'] <= $currentIndex) {
                continue;
            }

            if (trim((string) $row['text']) === '') {
                continue;
            }

            if (PdfParserUtils::isStandaloneNumberLine($row['text']) || $this->isLooseCodeFragmentLine($row['text'])) {
                continue;
            }

            return PdfParserUtils::isProductRowCandidate($row['text']);
        }

        return false;
    }

    protected function isProductContinuationLine($line)
    {
        $line = PdfParserUtils::normalizeLine($line);
        if ($line === '' || preg_match('/^\d{1,4}\s+/u', $line)) {
            return false;
        }

        if (PdfParserUtils::isHeaderOrClientLine($line) || PdfParserUtils::isEmailLine($line) || PdfParserUtils::isPhoneLine($line) || PdfParserUtils::isTotalsLine($line) || PdfParserUtils::isProductHeaderLine($line) || PdfParserUtils::isProductRowCandidate($line)) {
            return false;
        }

        if (strlen($line) < 12) {
            return false;
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $line, $mLetters);
        $digits = preg_match('/\d/u', $line);
        $hyphen = strpos($line, '-') !== false;

        return $letters >= 6 && ($digits || $hyphen);
    }

    protected function isLooseCodeFragmentLine($text)
    {
        $text = trim((string) $text);
        if ($text === '' || preg_match('/\s/u', $text)) {
            return false;
        }

        if (PdfParserUtils::isStandaloneNumberLine($text)) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9.\-\/_]{2,20}$/u', $text)
            && preg_match('/[A-Za-z]/u', $text)
            && preg_match('/\d/u', $text);
    }

    protected function makeBlock($id, $type, array $lines, $startLine, $section)
    {
        $block = new PdfBlock();
        $block->id = $id;
        $block->type = $type;
        $block->startLine = $startLine;
        $block->endLine = $startLine + count($lines) - 1;
        $block->lineCount = count($lines);
        $block->lines = $lines;
        $block->text = trim(implode("\n", array_map(function ($row) {
            return $row['text'];
        }, $lines)));
        $block->metadata = array(
            'section' => $section,
        );
        $block->score = $this->scoreBlock($block);
        $block->reason = $this->reasonForBlock($block);
        return $block;
    }

    protected function scoreBlock(PdfBlock $block)
    {
        if ($block->type === 'product_table') {
            $productRows = 0;
            $headers = 0;
            foreach ($block->lines as $line) {
                if ($line['type'] === 'product_row') {
                    $productRows++;
                }
                if ($line['type'] === 'product_header') {
                    $headers++;
                }
            }
            return min(100, 30 + ($productRows * 4) + ($headers * 20));
        }

        if ($block->type === 'client_data') {
            $kv = 0;
            foreach ($block->lines as $line) {
                if (PdfParserUtils::containsAny($line['text'], array(':', 'cliente', 'empresa', 'local', 'data', 'vendedor', 'telefone', 'celular', 'email', 'e-mail'))) {
                    $kv++;
                }
            }
            return min(100, 20 + ($kv * 8));
        }

        if ($block->type === 'header') {
            return min(100, 25 + count($block->lines) * 5);
        }

        if ($block->type === 'totals') {
            return min(100, 40 + count($block->lines) * 4);
        }

        if ($block->type === 'footer') {
            return min(100, 20 + count($block->lines) * 3);
        }

        return min(100, 10 + count($block->lines) * 3);
    }

    protected function reasonForBlock(PdfBlock $block)
    {
        switch ($block->type) {
            case 'product_table':
                return 'Linha(s) com cara de tabela de produtos.';
            case 'client_data':
                return 'Dados de cliente/contato detectados.';
            case 'header':
                return 'Cabecalho do documento.';
            case 'totals':
                return 'Totalizacao/rodape financeiro.';
            case 'footer':
                return 'Rodape ou informacao de contato.';
            default:
                return 'Bloco generico.';
        }
    }
}
