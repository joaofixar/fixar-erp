<?php
namespace hardness;

class PdfTableHeaderDetectionResult
{
    public $exists = false;
    public $score = 0;
    public $lineIndex = null;
    public $lineNumber = null;
    public $lineText = '';
    public $lineRawText = '';
    public $columns = array();
    public $columnAnchors = array();
    public $reason = '';

    public function toArray()
    {
        return array(
            'exists' => $this->exists,
            'score' => $this->score,
            'lineIndex' => $this->lineIndex,
            'lineNumber' => $this->lineNumber,
            'lineText' => $this->lineText,
            'lineRawText' => $this->lineRawText,
            'columns' => $this->columns,
            'columnAnchors' => $this->columnAnchors,
            'reason' => $this->reason,
        );
    }
}
