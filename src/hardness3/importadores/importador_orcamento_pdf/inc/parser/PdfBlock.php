<?php
namespace hardness;

class PdfBlock
{
    public $id = 0;
    public $type = 'other';
    public $score = 0;
    public $reason = '';
    public $startLine = 0;
    public $endLine = 0;
    public $lineCount = 0;
    public $lines = array();
    public $text = '';
    public $metadata = array();

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'type' => $this->type,
            'score' => $this->score,
            'reason' => $this->reason,
            'startLine' => $this->startLine,
            'endLine' => $this->endLine,
            'lineCount' => $this->lineCount,
            'lines' => $this->lines,
            'text' => $this->text,
            'metadata' => $this->metadata,
        );
    }
}
