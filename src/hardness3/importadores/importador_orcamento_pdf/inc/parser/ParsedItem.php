<?php
namespace hardness;

class ParsedItem
{
    public $sequence = null;
    public $page = null;
    public $rawText = '';
    public $description = '';
    public $quantity = null;
    public $unit = '';
    public $codeClient = '';
    public $alternativeCodes = array();
    public $ncm = '';
    public $unitPrice = null;
    public $total = null;
    public $notes = '';
    public $section = '';
    public $confidence = 0;
    public $sourceBlock = '';
    public $sourceLineStart = null;
    public $sourceLineEnd = null;
    public $sourceMethod = '';
    public $validationReason = '';
    public $validationScore = 0;

    public function toArray()
    {
        return array(
            'sequence' => $this->sequence,
            'page' => $this->page,
            'rawText' => $this->rawText,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'codeClient' => $this->codeClient,
            'alternativeCodes' => $this->alternativeCodes,
            'ncm' => $this->ncm,
            'unitPrice' => $this->unitPrice,
            'total' => $this->total,
            'notes' => $this->notes,
            'section' => $this->section,
            'confidence' => $this->confidence,
            'sourceBlock' => $this->sourceBlock,
            'sourceLineStart' => $this->sourceLineStart,
            'sourceLineEnd' => $this->sourceLineEnd,
            'sourceMethod' => $this->sourceMethod,
            'validationReason' => $this->validationReason,
            'validationScore' => $this->validationScore,
        );
    }
}
