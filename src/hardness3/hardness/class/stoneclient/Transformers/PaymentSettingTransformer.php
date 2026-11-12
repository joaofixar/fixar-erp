<?php
namespace hardness;;

use League\Fractal;

require_once (__DIR__ . "./../Models/PaymentSetting.php");
require_once ("PaymentSetupTransformer.php");

class PaymentSettingTransformer extends Fractal\TransformerAbstract
{	
    protected array $defaultIncludes = [
        'paymentSetup'
    ];

	public function transform(PaymentSetting $paymentSetting)
	{
        if(empty($paymentSetting->devices) || $paymentSetting->devices == [""]){
            // Vai enviar o pagamento para todas POS com o stone code do cliente
            return [			
				'visible' => $paymentSetting->visible,
				'display_name' => $paymentSetting->displayName,
                'print_order_receipt' => $paymentSetting->printOrderReceipt,
		    ];
            
        } else {
            // Vai enviar o pagamento para POS específica
            return [			
				'visible' => $paymentSetting->visible,
				'devices_serial_number' => $paymentSetting->devices,
				'display_name' => $paymentSetting->displayName,
                'print_order_receipt' => $paymentSetting->printOrderReceipt,
		    ];
        }
	}	

    public function includePaymentSetup(PaymentSetting $paymentSetting)
    {
        $paymentSetup = $paymentSetting->paymentSetup;		
        
        return $paymentSetup ? $this->item($paymentSetup, new PaymentSetupTransformer) : null;
    }
}