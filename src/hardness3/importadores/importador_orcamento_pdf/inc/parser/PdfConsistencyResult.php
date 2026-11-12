<?php
namespace hardness;

class PdfConsistencyResult
{
    public $accepted = false;
    public $ignored = false;
    public $score = 0;
    public $reason = '';
    public $missingFields = array();

    public function toArray()
    {
        return array(
            'accepted' => $this->accepted,
            'ignored' => $this->ignored,
            'score' => $this->score,
            'reason' => $this->reason,
            'missingFields' => $this->missingFields,
        );
    }
}
