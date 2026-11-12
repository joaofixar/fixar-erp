<?php
namespace hardness;

use League\Fractal;

require_once (__DIR__ . "./../Request/FecharPedido.php");

class FecharPedidoTransformer extends Fractal\TransformerAbstract
{
	public function transform(FecharPedido $pedido)
	{
		return [
			'status' => $pedido->status
		];
	}
}