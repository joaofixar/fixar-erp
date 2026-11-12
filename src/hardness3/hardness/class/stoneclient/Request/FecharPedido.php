<?php


namespace hardness;

class FecharPedido {    

    public string $orderId;
    public string $status;

    public function __construct(string $orderId, string $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }
}