<?php
namespace hardness;

class PdfParseDiagnosticCollector
{
    protected $document = array();
    protected $blocks = array();
    protected $items = array();
    protected $lineSamples = array(
        'accepted' => array(),
        'ignored' => array(),
    );
    protected $ignoredSummary = array();
    protected $maxLineSamples = 30;
    protected $maxIgnoredSamples = 20;

    public function startDocument(array $document)
    {
        $this->document = $document;
        $this->blocks = array();
        $this->items = array();
        $this->lineSamples = array('accepted' => array(), 'ignored' => array());
        $this->ignoredSummary = array();
    }

    public function addBlock(PdfBlock $block)
    {
        $this->blocks[] = $block->toArray();
    }

    public function addAcceptedLine(array $line, array $info = array())
    {
        $info = $this->normalizeAcceptedInfo($info);
        if (count($this->lineSamples['accepted']) < $this->maxLineSamples) {
            $this->lineSamples['accepted'][] = array_merge($line, $info);
        }
    }

    public function addIgnoredLine(array $line, $reason, array $info = array())
    {
        $type = $this->resolveIgnoredType($line, $reason, $info);
        if (!isset($this->ignoredSummary[$type])) {
            $this->ignoredSummary[$type] = 0;
        }
        $this->ignoredSummary[$type]++;

        if (count($this->lineSamples['ignored']) < $this->maxIgnoredSamples) {
            $this->lineSamples['ignored'][] = array_merge($line, $info, array(
                'type' => $type,
                'reason' => $reason,
            ));
        }
    }

    public function addItem(ParsedItem $item, array $info = array())
    {
        $this->items[] = array_merge($item->toArray(), $info);
    }

    public function toArray()
    {
        $ignoredSummary = array();
        foreach ($this->ignoredSummary as $type => $count) {
            $ignoredSummary[] = array(
                'type' => $type,
                'count' => $count,
            );
        }

        return array(
            'document' => $this->document,
            'blocks' => $this->blocks,
            'lines' => array(
                'accepted' => $this->lineSamples['accepted'],
                'ignored_summary' => $ignoredSummary,
                'ignored_samples' => $this->lineSamples['ignored'],
            ),
            'items' => $this->items,
            'summary' => array(
                'block_count' => count($this->blocks),
                'item_count' => count($this->items),
                'ignored_count' => array_sum($this->ignoredSummary),
            ),
        );
    }

    protected function normalizeAcceptedInfo(array $info)
    {
        if (!isset($info['hasContinuation'])) {
            $info['hasContinuation'] = false;
        }

        if (!isset($info['continuationLines'])) {
            $info['continuationLines'] = 0;
        }

        return $info;
    }

    protected function resolveIgnoredType(array $line, $reason, array $info = array())
    {
        $text = isset($line['rawText']) ? (string) $line['rawText'] : '';
        $reason = strtolower((string) $reason);

        if ($text !== '' && PdfParserUtils::isStandaloneNumberLine($text)) {
            return 'numero_solto';
        }

        if (PdfParserUtils::containsAny($reason, array('codigo', 'code'))) {
            return 'codigo_solto';
        }

        if (PdfParserUtils::containsAny($reason, array('telefone', 'email', 'total', 'contato', 'rodape', 'ruido', 'baixa consistencia'))) {
            return 'ruido';
        }

        if ($text !== '' && PdfParserUtils::isProductContinuationLine($text)) {
            return 'ruido';
        }

        return 'ruido';
    }
}
