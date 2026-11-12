<?php
namespace hardness;

class PdfParseEvaluationResult
{
    public $status = 'failed';
    public $confidence = 0;
    public $failureReasons = array();
    public $shouldTryPositionalParser = false;
    public $metrics = array();

    public function toArray()
    {
        return array(
            'parse_status' => $this->status,
            'parse_confidence' => $this->confidence,
            'parse_failure_reasons' => $this->failureReasons,
            'should_try_positional_parser' => $this->shouldTryPositionalParser,
            'metrics' => $this->metrics,
        );
    }
}
