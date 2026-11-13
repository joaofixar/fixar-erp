<?php
namespace hardness;

use League\Fractal;

require_once (__DIR__ . "./../Models/PaymentSetup.php");

class PaymentSetupTransformer extends Fractal\TransformerAbstract
{	
	public function transform(PaymentSetup $paymentSetup)
	{        
		return [
            'type' => $paymentSetup->type,
            'installments' => $paymentSetup->installments,
            'installment_type' => $paymentSetup->installmentsType
        ];
	}	
}