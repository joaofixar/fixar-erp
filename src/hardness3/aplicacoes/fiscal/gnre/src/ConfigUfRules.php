<?php
declare(strict_types=1);
require_once __DIR__.'/Municipality.php';

/** Interpreta TConfigUf sem incorporar regras estaduais no código. */
final class GnreConfigUfRules
{
    public static function fromXml(string $xml): array
    {
        if (!class_exists('DOMDocument')) throw new RuntimeException('A extensão DOM é necessária para interpretar a configuração GNRE.');
        $old = libxml_use_internal_errors(true); $dom = new DOMDocument();
        $ok = $dom->loadXML($xml, LIBXML_NONET); libxml_clear_errors(); libxml_use_internal_errors($old);
        if (!$ok) throw new RuntimeException('Configuração GNRE inválida: XML malformado.');
        $xp = new DOMXPath($dom); $root = $xp->query('//*[local-name()="TConfigUf"]')->item(0);
        if (!$root instanceof DOMElement) throw new RuntimeException('Configuração GNRE inválida: TConfigUf ausente.');
        $one = static function (DOMNode $at, string $name) use ($xp): string { return trim((string) $xp->evaluate('string(./*[local-name()="'.$name.'"])', $at)); };
        $many = static function (DOMNode $at, string $path) use ($xp): array { $out=[]; foreach ($xp->query($path, $at) as $node) $out[]=$node; return $out; };
        $status = $xp->evaluate('string(./*[local-name()="situacaoConsulta"]/*[local-name()="codigo"])', $root);
        $uf = strtoupper($one($root, 'uf'));
        if (!preg_match('/^[A-Z]{2}$/', $uf) || $status !== '450') throw new RuntimeException('Configuração GNRE indisponível para a UF: '.trim((string)$status).' '.$one($root, 'situacaoConsulta'));
        $versions=[]; foreach ($many($root, './*[local-name()="versoesXml"]/*[local-name()="versao"]') as $version) { $value=trim($version->textContent); if($value!=='')$versions[]=$value; }
        $rules=['uf'=>$uf, 'status'=>(string)$status, 'description'=>trim((string)$xp->evaluate('string(./*[local-name()="situacaoConsulta"]/*[local-name()="descricao"])', $root)),
            'versions'=>$versions, 'max'=>[], 'uf_types'=>[], 'address_required'=>['cpf'=>'N','cnpj'=>'N'], 'receipts'=>[], 'unknown'=>[]];
        foreach ($many($root, './*[local-name()="qtdMaximas"]/*') as $n) $rules['max'][$n->localName]=trim($n->textContent);
        foreach ($many($root, './*[local-name()="tiposGnreDaUF"]/*[local-name()="tipoGnre"]') as $n) $rules['uf_types'][]=trim($n->textContent);
        $rules['address_required']=['cpf'=>self::flag($one($root, 'enderecoObr') ? trim((string)$xp->evaluate('string(./*[local-name()="enderecoObr"]/*[local-name()="emitCpf"])', $root)) : 'N'), 'cnpj'=>self::flag($one($root, 'enderecoObr') ? trim((string)$xp->evaluate('string(./*[local-name()="enderecoObr"]/*[local-name()="emitCnpj"])', $root)) : 'N')];
        foreach ($many($root, './*[local-name()="receitas"]/*[local-name()="receita"]') as $receipt) {
            $code=trim($receipt->getAttribute('codigo')); if (!preg_match('/^\d{6}$/',$code)) throw new RuntimeException('Configuração GNRE inválida: receita sem código válido.');
            $r=['code'=>$code,'description'=>trim($receipt->getAttribute('descricao')),'courier'=>self::flag($receipt->getAttribute('courier') ?: 'N'),'fields'=>[], 'details'=>[], 'products'=>[], 'periods'=>[], 'document_types'=>[], 'extras'=>[], 'types'=>[], 'xml_doc_versions'=>[], 'unknown'=>[], 'value'=>'', 'pix'=>'N'];
            $flags=['exigeContribuinteEmitente'=>'emitter','exigeDetalhamentoReceita'=>'detail','exigeProduto'=>'product','exigePeriodoReferencia'=>'reference','exigePeriodoApuracao'=>'period','exigeParcela'=>'installment','exigeDocumentoOrigem'=>'document','exigeContribuinteDestinatario'=>'recipient','exigeDataVencimento'=>'due_date','exigeDataPagamento'=>'payment_date','exigeConvenio'=>'agreement','exigeValorFecp'=>'fecp','exigeCamposAdicionais'=>'extras'];
            foreach ($flags as $xmlName=>$key) { $v=$one($receipt,$xmlName); $r['fields'][$key]=self::flag($v === '' ? 'N' : $v); }
            $r['value']=$one($receipt,'valorExigido'); $r['pix']=self::flag($one($receipt,'aceitaPgPix'));
            foreach ($many($receipt, './*[local-name()="detalhamentosReceita"]/*') as $n) $r['details'][]=['code'=>$one($n,'codigo'),'description'=>$one($n,'descricao')];
            foreach ($many($receipt, './*[local-name()="produtos"]/*') as $n) $r['products'][]=['code'=>$one($n,'codigo'),'description'=>$one($n,'descricao')];
            foreach ($many($receipt, './*[local-name()="periodosApuracao"]/*') as $n) $r['periods'][]=['code'=>$one($n,'codigo'),'description'=>$one($n,'descricao')];
            foreach ($many($receipt, './*[local-name()="tiposDocumentosOrigem"]/*') as $n) $r['document_types'][]=['code'=>$one($n,'codigo'),'description'=>$one($n,'descricao')];
            foreach ($many($receipt, './*[local-name()="tiposGnre"]/*') as $n) $r['types'][]=trim($n->textContent);
            foreach ($many($receipt, './*[local-name()="versoesXmlDocOrigem"]/*') as $n) $r['xml_doc_versions'][]=trim($n->textContent);
            foreach ($many($receipt, './*[local-name()="camposAdicionais"]/*') as $n) $r['extras'][]=['code'=>$one($n,'codigo'),'required'=>self::flag($one($n,'obrigatorio')),'type'=>$one($n,'tipo'),'length'=>$one($n,'tamanho'),'decimals'=>$one($n,'casasDecimais'),'title'=>$one($n,'titulo'),'versions'=>array_map(static function($v){return trim($v->textContent);},$many($n,'./*[local-name()="versoesXmlCampoAdicional"]/*'))];
            $known=array_merge(array_keys($flags), ['valorExigido','detalhamentosReceita','produtos','periodosApuracao','tiposDocumentosOrigem','versoesXmlDocOrigem','camposAdicionais','tiposGnre','aceitaPgPix']);
            foreach ($many($receipt,'./*') as $n) if (!in_array($n->localName,$known,true)) $r['unknown'][]=$n->localName;
            $rules['receipts'][$code]=$r;
        }
        if (!$rules['receipts']) throw new RuntimeException('A UF não retornou receitas permitidas para esta consulta.');
        return $rules;
    }
    private static function flag(string $value): string { $value=strtoupper(trim($value)); return in_array($value,['S','N','O'],true)?$value:'N'; }
    public static function receipt(array $rules, string $code): ?array { return $rules['receipts'][$code] ?? null; }
    public static function validate(array $rules, array $guide, array $items, ?array $emitter): array
    {
        $errors=[]; if (($rules['uf']??'') !== ($guide['GNRE001_UF_Favorecida']??'')) return ['A configuração oficial carregada não corresponde à UF da guia.'];
        if (!in_array('2.00',$rules['versions']??[],true)) $errors[]='A UF não permite XML GNRE versão 2.00.';
        $type=trim((string)($guide['GNRE001_Tipo_GNRE']??''));
        if ($type === '') $errors[]='Esta guia foi criada no modelo anterior e precisa ser revisada no fluxo por tipo GNRE.';
        elseif (!in_array($type,['0','1','2'],true)) $errors[]='O tipo GNRE informado é desconhecido.';
        elseif (!in_array($type,$rules['uf_types']??[],true)) $errors[]='O tipo GNRE '.$type.' não é permitido pela UF.';
        self::typeRules($errors,$type,$items);
        if (isset($rules['max']['itensPorGuia']) && ctype_digit((string)$rules['max']['itensPorGuia']) && count($items)>(int)$rules['max']['itensPorGuia']) $errors[]='A quantidade de itens excede o máximo oficial da UF para uma guia.';
        foreach ($items as $idx=>$item) { $p='Item '.($idx+1).': '; $r=self::receipt($rules,(string)$item['GNRE003_Codigo_Receita']); if (!$r) {$errors[]=$p.'receita não permitida pela configuração oficial.'; continue;} if ($r['unknown']) {$errors[]=$p.'a configuração contém campos de receita desconhecidos ('.implode(', ',$r['unknown']).'); atualização do módulo necessária.'; continue;}
            if (!in_array($type,$r['types']??[],true)) $errors[]=$p.'o tipo GNRE '.$type.' não é permitido para esta receita.';
            $field=static function(string $key) use($r): string { return $r['fields'][$key]??'N';};
            self::required($errors,$p,'detalhamento',$item['GNRE003_Detalhamento_Receita'],$field('detail')); self::allowed($errors,$p,'detalhamento',$item['GNRE003_Detalhamento_Receita'],$field('detail'));
            self::list($errors,$p,'detalhamento',(string)$item['GNRE003_Detalhamento_Receita'],$r['details']);
            self::required($errors,$p,'produto',$item['GNRE003_Codigo_Produto'],$field('product')); self::allowed($errors,$p,'produto',$item['GNRE003_Codigo_Produto'],$field('product')); self::list($errors,$p,'produto',(string)$item['GNRE003_Codigo_Produto'],$r['products']);
            self::required($errors,$p,'período',$item['GNRE003_Referencia_Periodo'],$field('period')); self::allowed($errors,$p,'período',$item['GNRE003_Referencia_Periodo'],$field('period')); self::list($errors,$p,'período',(string)$item['GNRE003_Referencia_Periodo'],$r['periods']);
            $hasReference=false;foreach(['Periodo','Mes','Ano','Parcela'] as $part)if(trim((string)($item['GNRE003_Referencia_'.$part]??''))!==''){$hasReference=true;break;}
            self::required($errors,$p,'referência',$hasReference?'x':'',$field('reference'));
            self::required($errors,$p,'parcela',$item['GNRE003_Referencia_Parcela'],$field('installment')); self::allowed($errors,$p,'parcela',$item['GNRE003_Referencia_Parcela'],$field('installment'));
            $hasDoc=!empty($item['GNRE002_Identificacao']); self::required($errors,$p,'documento de origem',$hasDoc?'x':'',$field('document')); self::allowed($errors,$p,'documento de origem',$hasDoc?'x':'',$field('document')); if($hasDoc) self::list($errors,$p,'tipo de documento',(string)$item['GNRE003_Tipo_Documento_Origem'],$r['document_types']);
            $hasRecipient=!empty($item['GNRE003_Destinatario_Identificacao']); self::required($errors,$p,'destinatário',$hasRecipient?'x':'',$field('recipient')); self::allowed($errors,$p,'destinatário',$hasRecipient?'x':'',$field('recipient'));
            self::required($errors,$p,'data de vencimento',$item['GNRE003_Data_Vencimento']??'',$field('due_date'));
            self::allowed($errors,$p,'data de vencimento',$item['GNRE003_Data_Vencimento']??'',$field('due_date'));
            $agreement=trim((string)($item['GNRE003_Convenio']??''));
            if ($field('agreement')==='O' && $agreement==='') $errors[]=$p.'convênio é obrigatório pela UF.';
            self::valueRules($errors,$p,$r['value']??'',is_array($item['GNRE006']??null)?$item['GNRE006']:[],$field('fecp'));
            if ($field('payment_date')==='S' && trim((string)($guide['GNRE001_Data_Pagamento']??''))==='') $errors[]='Data de pagamento é obrigatória pela UF.';
            if ($field('payment_date')==='N' && trim((string)($guide['GNRE001_Data_Pagamento']??''))!=='') $errors[]='Data de pagamento não é permitida pela UF.';
            $extras=GnreMunicipality::extras($item['GNRE003_Campos_Extras_JSON']??null); if (!is_array($extras)) $extras=[]; foreach($extras as &$extra)if(is_array($extra)&&isset($extra['codigo'])&&is_string($extra['codigo'])&&ctype_digit($extra['codigo']))$extra['codigo']=(int)$extra['codigo'];unset($extra); if($field('extras')==='N'&&$extras)$errors[]=$p.'campos adicionais não são permitidos pela UF.'; $codes=array_column($extras,'codigo'); $configured=array_column($r['extras'],'code'); foreach($codes as $code)if(!in_array((string)$code,$configured,true))$errors[]=$p.'campo adicional não consta na configuração oficial.'; foreach($r['extras'] as $x) {if($x['required']==='S'&&!in_array((int)$x['code'],$codes,true))$errors[]=$p.'campo adicional obrigatório ausente: '.$x['title'].'.';}
        }
        $needsEmitter=false; foreach($items as $item){$receipt=self::receipt($rules,(string)$item['GNRE003_Codigo_Receita']);if($receipt&&($receipt['fields']['emitter']??'N')!=='N'){$needsEmitter=true;break;}} if($needsEmitter) { if(!$emitter) $errors[]='Dados do contribuinte emitente indisponíveis no cadastro da empresa.'; elseif(!empty($emitter['errors'])) foreach($emitter['errors'] as $e)$errors[]='Emitente: '.$e; }
        return array_values(array_unique($errors));
    }
    /**
     * Manual GNRE v2.17: A significa Principal OU Total (não parcelas cumulativas).
     * 11/21 são ICMS principal/total e 12/22 seus respectivos pares FCP.
     */
    public static function valueErrors(string $required,array $values,string $fecpFlag):array
    {
        $types=[]; foreach($values as $value){$type=(string)($value['GNRE006_Tipo_Valor']??$value['type']??'');if($type!=='')$types[]=$type;}
        $required=strtoupper(trim($required));$fecpFlag=strtoupper(trim($fecpFlag));$errors=[];
        if (!in_array($required,['P','T','A','PO','TO','AO','N'],true)) return ['valorExigido desconhecido na configuração oficial.'];
        $base=array_values(array_intersect($types,['11','21']));
        $allowed=['P'=>['11','12'],'T'=>['21','22'],'A'=>['11','12','21','22'],'PO'=>['11','12'],'TO'=>['21','22'],'AO'=>['11','12','21','22'],'N'=>[]][$required];
        foreach($types as $type)if(!in_array($type,$allowed,true))$errors[]=$required==='N'?'a receita será calculada pela UF e não aceita valores informados pelo contribuinte.':'tipo de valor '.$type.' não é permitido por valorExigido='.$required.'.';
        if(in_array($required,['P','A'],true)&&!$base)$errors[]=$required==='A'?'selecione Valor Principal ou Valor Total.':'valor principal (tipo 11) é obrigatório pela receita.';
        if($required==='T'&&!in_array('21',$types,true))$errors[]='valor total (tipo 21) é obrigatório pela receita.';
        if(in_array($required,['A','AO'],true)&&count(array_unique($base))>1)$errors[]='Valor Principal (11) e Valor Total (21) são alternativas e não podem ser persistidos simultaneamente.';
        foreach(['12'=>'11','22'=>'21'] as $fecpType=>$baseType){$fecpType=(string)$fecpType;$baseType=(string)$baseType;
            if(in_array($fecpType,$types,true)&&!in_array($baseType,$types,true))$errors[]='valor FCP '.$fecpType.' exige o valor tributário correspondente '.$baseType.'.';
            if($fecpFlag==='N'&&in_array($fecpType,$types,true))$errors[]='FCP não é permitido pela configuração oficial.';
            if($fecpFlag==='S'&&in_array($baseType,$types,true)&&!in_array($fecpType,$types,true))$errors[]='valor FCP '.$fecpType.' é obrigatório para o valor selecionado.';
        }
        return array_values(array_unique($errors));
    }
    private static function valueRules(array &$errors,string $prefix,string $required,array $values,string $fecpFlag):void
    { foreach(self::valueErrors($required,$values,$fecpFlag) as $error)$errors[]=$prefix.$error; }
    private static function typeRules(array &$errors, string $type, array $items): void
    {
        $count=count($items);
        if ($type==='0' && $count!==1) $errors[]='Tipo GNRE 0 exige exatamente um item.';
        if ($type==='1') {
            if ($count<2) $errors[]='Tipo GNRE 1 exige múltiplos documentos de origem.';
            $receipts=array_unique(array_column($items,'GNRE003_Codigo_Receita'));
            $docTypes=array_unique(array_map(static function($i){return (string)($i['GNRE003_Tipo_Documento_Origem']??'');},$items));
            $docs=[]; foreach($items as $i){$doc=trim((string)($i['GNRE002_Identificacao']??'')); if($doc==='')$errors[]='Tipo GNRE 1 exige documento de origem em todos os itens.'; elseif(isset($docs[$doc]))$errors[]='Tipo GNRE 1 não permite documento de origem repetido.'; $docs[$doc]=true;}
            if(count($receipts)!==1)$errors[]='Tipo GNRE 1 exige a mesma receita em todos os itens.';
            if(count($docTypes)!==1 || reset($docTypes)==='')$errors[]='Tipo GNRE 1 exige o mesmo tipo documental em todos os itens.';
        }
        if ($type==='2') {
            if ($count<2) $errors[]='Tipo GNRE 2 exige múltiplos itens de recolhimento.';
            $docs=[];$refs=[]; foreach($items as $i){$receipt=(string)($i['GNRE003_Codigo_Receita']??'');$doc=trim((string)($i['GNRE002_Identificacao']??''));$ref=self::referenceKey($i); if($doc!==''){ $key=$receipt.'|'.$doc;if(isset($docs[$key]))$errors[]='Tipo GNRE 2 não permite repetir receita + documento de origem.';$docs[$key]=true;} if($ref!==''){ $key=$receipt.'|'.$ref;if(isset($refs[$key]))$errors[]='Tipo GNRE 2 não permite repetir receita + referência.';$refs[$key]=true;}}
        }
    }
    private static function referenceKey(array $item): string { return implode('|',array_map(static function($part)use($item){return (string)($item['GNRE003_Referencia_'.$part]??'');},['Periodo','Mes','Ano','Parcela'])); }
    private static function required(array &$e,string $p,string $name,$value,string $flag):void {if($flag==='S'&&trim((string)$value)==='')$e[]=$p.$name.' é obrigatório pela UF.';}
    private static function allowed(array &$e,string $p,string $name,$value,string $flag):void {if($flag==='N'&&trim((string)$value)!=='')$e[]=$p.$name.' não é permitido pela UF.';}
    private static function list(array &$e,string $p,string $name,string $value,array $list):void {if($value!==''&&$list&&!in_array($value,array_column($list,'code'),true))$e[]=$p.$name.' não consta na lista oficial.';}
}
