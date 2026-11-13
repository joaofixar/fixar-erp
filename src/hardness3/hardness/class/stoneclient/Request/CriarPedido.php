<?php


namespace hardness;

require_once (__DIR__ . "./../Models/Customer.php");
require_once (__DIR__ . "/../Models/PaymentSetting.php");
require_once (__DIR__ . "/../Models/Item.php");

class CriarPedido {
    public ?Customer $customer;
    public array $items;
    public bool $closed;
    public PaymentSetting $paymentSetting;

    public function __construct(Customer $customer, PaymentSetting $paymentSetting,bool $closed = false)
    {
        $this->customer = $customer;
        $this->items = [];
        $this->closed = $closed;
        $this->paymentSetting = $paymentSetting;
    }

    public function addItem(Item $item)   {
        $this->items[] = $item;
        return $this;
    }
}