<?php

namespace hardness;

class ImprimirNotaFiscal {    

    public string $orderId;
    public string $type;
    public int $sizeVertical;
    public int $sizeHorizontal;
    public string $format;
    public string $content;

    public function __construct($orderId, $type, $sizeVertical, $sizeHorizontal, $format, $content)
    {
        $this->orderId = $orderId;
        $this->type = $type;
        $this->sizeVertical = $sizeVertical;
        $this->sizeHorizontal = $sizeHorizontal;
        $this->format = $format;
        $this->content = $content;
    }
 
}