<?php
namespace hardness;

class PdfColumnMap
{
    public $mode = 'heuristic';
    public $hasHeader = false;
    public $score = 0;
    public $sourceLineNumber = null;
    public $sourceLineText = '';
    public $columns = array(
        'codeClient' => null,
        'description' => null,
        'unit' => null,
        'quantity' => null,
        'notes' => null,
    );
    public $spans = array(
        'codeClient' => null,
        'description' => null,
        'unit' => null,
        'quantity' => null,
        'notes' => null,
    );

    public function toArray()
    {
        return array(
            'mode' => $this->mode,
            'hasHeader' => $this->hasHeader,
            'score' => $this->score,
            'sourceLineNumber' => $this->sourceLineNumber,
            'sourceLineText' => $this->sourceLineText,
            'columns' => $this->columns,
            'spans' => $this->spans,
        );
    }
}
