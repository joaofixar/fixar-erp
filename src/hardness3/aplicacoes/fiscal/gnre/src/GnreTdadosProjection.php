<?php
declare(strict_types=1);
require_once __DIR__.'/Municipality.php';
require_once __DIR__.'/ConfigUfRules.php';

/** Pure TDadosGNRE projection shared with the XML serializer's inclusion rules. */
final class GnreTdadosProjection
{
    /** @return array<string,mixed> */
    public static function project(array $guide,array $items,array $rules,array $emitter):array
    {
        $out=['ufFavorecida'=>(string)$guide['GNRE001_UF_Favorecida'],'tipoGnre'=>(string)$guide['GNRE001_Tipo_GNRE']];
        if(self::needsEmitter($rules,$items))$out['contribuinteEmitente']=self::emitter($emitter,$rules);
        $projected=[];foreach($items as $item)$projected[]=self::item($item);
        $out['itensGNRE']=['item'=>$projected];
        if(self::valueMode($rules,$items)!=='all_n')$out['valorGNRE']=number_format(self::total($items),2,'.','');
        if(trim((string)($guide['GNRE001_Data_Pagamento']??''))!=='')$out['dataPagamento']=(string)$guide['GNRE001_Data_Pagamento'];
        return $out;
    }
    private static function needsEmitter(array $rules,array $items):bool {foreach($items as $item){$receipt=GnreConfigUfRules::receipt($rules,(string)$item['GNRE003_Codigo_Receita']);if($receipt&&($receipt['fields']['emitter']??'N')!=='N')return true;}return false;}
    private static function emitter(array $emitter,array $rules):array {$out=['identificacao'=>['CNPJ'=>(string)($emitter['cnpj']??'')],'razaoSocial'=>trim((string)($emitter['name']??''))];if(($rules['address_required']['cnpj']??'N')==='S')foreach(['address'=>'endereco','city'=>'municipio','uf'=>'uf','cep'=>'cep','phone'=>'telefone'] as $key=>$node)$out[$node]=(string)($emitter[$key]??'');return $out;}
    private static function item(array $item):array {$out=['receita'=>(string)$item['GNRE003_Codigo_Receita']];if(!empty($item['GNRE003_Detalhamento_Receita']))$out['detalhamentoReceita']=(string)$item['GNRE003_Detalhamento_Receita'];if(!empty($item['GNRE002_Identificacao']))$out['documentoOrigem']=['tipo'=>(string)$item['GNRE003_Tipo_Documento_Origem'],'valor'=>(string)$item['GNRE002_Identificacao']];if(!empty($item['GNRE003_Codigo_Produto']))$out['produto']=(string)$item['GNRE003_Codigo_Produto'];$ref=[];foreach(['Periodo'=>'periodo','Mes'=>'mes','Ano'=>'ano','Parcela'=>'parcela'] as $column=>$node)if(!empty($item['GNRE003_Referencia_'.$column]))$ref[$node]=(string)$item['GNRE003_Referencia_'.$column];if($ref)$out['referencia']=$ref;if(!empty($item['GNRE003_Data_Vencimento']))$out['dataVencimento']=(string)$item['GNRE003_Data_Vencimento'];$out['valor']=self::values($item);if(!empty($item['GNRE003_Convenio']))$out['convenio']=(string)$item['GNRE003_Convenio'];if(!empty($item['GNRE003_Destinatario_Identificacao'])){$recipient=['identificacao'=>[(string)$item['GNRE003_Destinatario_Tipo_Identificacao'] => (string)$item['GNRE003_Destinatario_Identificacao']]];$name=trim((string)($item['GNRE003_Destinatario_Razao_Social']??''));if($name!=='')$recipient['razaoSocial']=$name;$municipality=GnreMunicipality::gnreCode(GnreMunicipality::storedIbge($item));if($municipality!=='')$recipient['municipio']=$municipality;$out['contribuinteDestinatario']=$recipient;}$extras=self::extras($item['GNRE003_Campos_Extras_JSON']??null);if($extras){$out['camposExtras']=['campoExtra'=>$extras];}return $out;}
    private static function values(array $item):array {$out=[];foreach(($item['GNRE006']??[]) as $value)$out[]=['tipo'=>(string)($value['GNRE006_Tipo_Valor']??$value['type']??''),'valor'=>number_format((float)($value['GNRE006_Valor']??$value['value']??0),2,'.','')];return $out;}
    private static function total(array $items):float {$total=0.;foreach($items as $item)foreach(self::values($item) as $value)$total+=(float)$value['valor'];return $total;}
    private static function valueMode(array $rules,array $items):string {$n=false;$contributor=false;$hasValues=false;foreach($items as $item){if(self::values($item))$hasValues=true;$receipt=GnreConfigUfRules::receipt($rules,(string)$item['GNRE003_Codigo_Receita']);if($receipt&&strtoupper((string)($receipt['value']??''))==='N')$n=true;else $contributor=true;}return $n&&$contributor?'mixed':($n&&!$hasValues?'all_n':'contributor');}
    private static function extras(?string $json):?array {$items=GnreMunicipality::extras($json);if($items===null||count($items)>3)return null;$out=[];foreach($items as $item){if(!is_array($item)||!isset($item['codigo'],$item['valor'])||!is_string($item['valor'])||mb_strlen($item['valor'],'UTF-8')>100)return null;$code=$item['codigo'];if(is_string($code)&&ctype_digit($code))$code=(int)$code;if(!is_int($code))return null;$out[]=['codigo'=>$code,'valor'=>$item['valor']];}return $out;}
}
