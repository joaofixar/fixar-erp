<?php


namespace hardness;

class PaymentSetup {
    public string $type;
    public int $installments;
    public string $installmentsType;    

    public function __construct($type, $installments, $installmentsType)
    {
        $this->type = $type;
        $this->installments = $installments;
        $this->installmentsType = $installmentsType;
    }
}