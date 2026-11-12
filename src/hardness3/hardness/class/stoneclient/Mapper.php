<?php

namespace hardness;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;

require_once("Request/CriarPedido.php");
require_once("Request/FecharPedido.php");
require_once("Request/ImprimirNotaFiscal.php");

require_once("Transformers/CriarPedidoTransformer.php");
require_once("Transformers/FecharPedidoTransformer.php");
require_once("Transformers/ImprimirNotaFiscalTransformer.php");


class Mapper {

    private Manager $manager;
    public function __construct()
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new ArraySerializer);
    } 

    public function mapCriarPedido(CriarPedido $pedido){

        $resource = new Item($pedido, new CriarPedidoTransformer);

        return $this->manager->createData($resource)->toArray();
    }

    public function mapFecharPedido(FecharPedido $pedido) {
        $resource = new Item($pedido, new FecharPedidoTransformer);        
        return $this->manager->createData($resource)->toArray();
    }

    public function mapImprimirNota(ImprimirNotaFiscal $nota) {
        $resource = new Item($nota, new ImprimirNotaFiscalTransformer);        
        return $this->manager->createData($resource)->toArray();
    }
}