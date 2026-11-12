<?php


namespace hardness;

require_once('PaymentSetup.php');

class PaymentSetting {
    public bool $visible;
    public array $devices;
    public string $displayName;
    public bool $printOrderReceipt;
    public ?PaymentSetup $paymentSetup;

    public function __construct($visible, array $devices, string $displayName, ?PaymentSetup $paymentSetup = null, bool $printOrderReceipt = false)
    {
        $this->visible = $visible;
        $this->devices = $devices;
        $this->displayName = $displayName;
        $this->paymentSetup = $paymentSetup;        
        $this->printOrderReceipt = $printOrderReceipt;
    }
}