<?php
declare(strict_types=1);
require dirname(__DIR__, 2).'/autoload.php';

use HardnessMcp\Tools\Product\Prices\{PriceBatchLimits, PreparePricesTool};
use HardnessMcp\Tools\Validation\ToolArgumentsValidator;

function limitCheck(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
    echo "PASS $message\n";
}
$previous = getenv('MCP_PRODUCT_PRICE_MAX_ITEMS');
try {
    putenv('MCP_PRODUCT_PRICE_MAX_ITEMS');
    $definition=(new PreparePricesTool())->getDefinition();
    $input=['request_key'=>'limit-test','origin'=>'spreadsheet','items'=>[]];
    for($n=1;$n<=1000;$n++)$input['items'][]=['line'=>$n,'code'=>str_repeat('X',255).'', 'prices'=>['price_1'=>'9999999999.1234567890','price_5'=>'9999999999.1234567890']];
    $raw=json_decode(json_encode($input));
    limitCheck($definition->getInputSchema()['properties']['items']['maxItems']===1000,'default schema allows 1000');
    limitCheck(strlen(json_encode($input))>65536 && $definition->getArgumentsValidator()->isValid($input,$raw,$definition->getInputSchema()),'large input exceeds 64 KiB and is valid for price tool');
    limitCheck(!(new ToolArgumentsValidator())->isValid($input,$raw,$definition->getInputSchema()),'ordinary validator keeps its original limits');
    putenv('MCP_PRODUCT_PRICE_MAX_ITEMS=1500');
    $expanded=(new PreparePricesTool())->getDefinition();
    for($n=1001;$n<=1500;$n++)$input['items'][]=['line'=>$n,'code'=>'P-'.$n,'prices'=>['price_1'=>'10']];
    limitCheck($expanded->getInputSchema()['properties']['items']['maxItems']===1500 && $expanded->getArgumentsValidator()->isValid($input,json_decode(json_encode($input)),$expanded->getInputSchema()),'one setting increases schema and validator together');
    $input['items'][]=['line'=>1501,'code'=>'EXTRA','prices'=>['price_1'=>'10']];
    limitCheck(!$expanded->getArgumentsValidator()->isValid($input,json_decode(json_encode($input)),$expanded->getInputSchema()),'configured limit plus one rejected');
    limitCheck(PriceBatchLimits::payloadBytes()>PriceBatchLimits::argumentBytes(),'HTTP envelope includes argument budget plus overhead');
    putenv('MCP_PRODUCT_PRICE_MAX_ITEMS=10');
    $small=(new PreparePricesTool())->getDefinition();
    limitCheck($small->getInputSchema()['properties']['items']['maxItems']===10,'limit can also be lowered');
    foreach(['0','-1','abc','10001'] as $invalid) {
        putenv('MCP_PRODUCT_PRICE_MAX_ITEMS='.$invalid);
        $rejected=false;
        try { PriceBatchLimits::maxItems(); } catch (RuntimeException $e) { $rejected=true; }
        limitCheck($rejected,'invalid setting rejected: '.$invalid);
    }
} finally {
    putenv($previous===false?'MCP_PRODUCT_PRICE_MAX_ITEMS':'MCP_PRODUCT_PRICE_MAX_ITEMS='.$previous);
}
