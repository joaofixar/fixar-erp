<?php
declare(strict_types=1);
require_once __DIR__.'/Municipality.php';

/** General/documented data checks. It deliberately does not duplicate XSD or TConfigUf. */
final class GnreSemanticValidator
{
    public function validate(array $rules,array $emitter,array $items): array
    {
        $errors=[];
        if ($this->emitterRequired($rules,$items) && !preg_match('/^\d{5}$/',(string)($emitter['city']??'')))
            $errors[]='Pré-validação cadastral: o município do emitente deve conter o código GNRE de 5 dígitos vindo de D020; revise C004 → D020 → D018.';
        foreach($items as $n=>$item){
            $receipt=GnreConfigUfRules::receipt($rules,(string)($item['GNRE003_Codigo_Receita']??''));
            if(!$receipt || (($receipt['fields']['recipient']??'N')==='N')) continue;
            $id=trim((string)($item['GNRE003_Destinatario_Identificacao']??''));
            if($id==='') continue;
            $prefix='Item '.($n+1).': pré-validação cadastral do destinatário: ';
            if(trim((string)($item['GNRE003_Destinatario_Razao_Social']??''))==='')$errors[]=$prefix.'razão social é necessária quando há identificação.';
            $ibge=GnreMunicipality::storedIbge($item);
            if($ibge==='')$errors[]=$prefix.'compatibilidade: o município legado não possui IBGE de 7 dígitos persistido; selecione explicitamente a cidade novamente antes de gerar ou transmitir.';
            elseif(!preg_match('/^\d{5}$/',GnreMunicipality::gnreCode($ibge)))$errors[]=$prefix.'município IBGE inválido.';
            elseif(function_exists('gnre_connect')){$options=(new GnreMunicipality())->options();if(($options['states']??[])&&GnreMunicipality::cityUfForCode($options,$ibge)==='')$errors[]=$prefix.'compatibilidade: o IBGE persistido não existe mais em D020/D018; corrija explicitamente a cidade antes de gerar ou transmitir.';}
        }
        return $errors;
    }
    private function emitterRequired(array $rules,array $items): bool {foreach($items as $item){$r=GnreConfigUfRules::receipt($rules,(string)($item['GNRE003_Codigo_Receita']??''));if($r&&($r['fields']['emitter']??'N')!=='N')return true;}return false;}
}
