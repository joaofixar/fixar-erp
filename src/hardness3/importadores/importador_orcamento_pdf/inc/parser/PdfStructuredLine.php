<?php
namespace hardness;

class PdfStructuredLine
{
    public $lineStart = 0;
    public $lineEnd = 0;
    public $sequence = null;
    public $rawText = '';
    public $lines = array();
    public $blockType = 'product_table';
    public $confidence = 0;
    public $notes = '';
    public $standaloneCodes = array();

    public function toArray()
    {
        return array(
            'lineStart' => $this->lineStart,
            'lineEnd' => $this->lineEnd,
            'sequence' => $this->sequence,
            'rawText' => $this->rawText,
            'lines' => $this->lines,
            'blockType' => $this->blockType,
            'confidence' => $this->confidence,
            'notes' => $this->notes,
            'standaloneCodes' => $this->standaloneCodes,
        );
    }
}
