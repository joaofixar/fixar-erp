<?php
namespace hardness;

class PdfFieldInferenceResult
{
    public $item;
    public $method = 'heuristic';
    public $score = 0;
    public $reason = '';
    public $missingFields = array();
    public $lineInfo = array();

    public function __construct()
    {
        $this->item = new ParsedItem();
    }

    public function toArray()
    {
        return array(
            'method' => $this->method,
            'score' => $this->score,
            'reason' => $this->reason,
            'missingFields' => $this->missingFields,
            'lineInfo' => $this->lineInfo,
            'item' => $this->item instanceof ParsedItem ? $this->item->toArray() : $this->item,
        );
    }
}
