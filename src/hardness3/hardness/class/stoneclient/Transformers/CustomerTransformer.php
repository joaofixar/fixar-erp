<?php
namespace hardness;

use League\Fractal;

require_once (__DIR__ . "./../Models/Customer.php");

class CustomerTransformer extends Fractal\TransformerAbstract
{	
	public function transform(Customer $customer =null)
	{
		if (!$customer) return [];
		
		$data =  [			
				'name' => $customer->name,
				'email' => $customer->email,
		];

		if(empty($customer->email)) unset($data['email']);

		return $data;
	}	
}