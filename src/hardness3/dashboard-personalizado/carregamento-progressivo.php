<?php
namespace hardness;
class DashboardCargaPendente extends \RuntimeException {}
require_once __DIR__ . '/carregamento-geral.php';
function dashboardCargaHabilitada($indicador) {
    return !empty($indicador['desempenhoComercial']['carregamentoProgressivo']) || !empty($indicador['painelCotacoes']['carregamentoProgressivo'])
        || (isset($indicador['abcClientes']['habilitado'], $indicador['abcClientes']['leituraEmLotes'])
            && $indicador['abcClientes']['habilitado'] === true && $indicador['abcClientes']['leituraEmLotes'] === true)
        || dashboardCargaGeralHabilitada($indicador);
}
function dashboardCargaAtiva() { return !empty($GLOBALS['dashboardCarga']); }
function dashboardCargaDiretorio() { return sys_get_temp_dir().'/hardness-dashboard-cargas-'.substr(hash('sha256',__DIR__),0,16); }
function dashboardCargaJanelas($inicio,$fim) {
    $result=array();$date=new \DateTime($inicio);
    while($date->format('Y-m-d')<=$fim) {
        $last=min($fim,$date->format('Y-m-t'));
        $result[]=array($date->format('Y-m-d'),$last);
        $date=(new \DateTime($last))->modify('+1 day');
    }
    return $result;
}
function dashboardCargaSalvar($arquivo,$data) {
    $temp=$arquivo.'.tmp';$json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if($json===false || @file_put_contents($temp,$json,LOCK_EX)===false) throw new \RuntimeException('Não foi possível salvar o lote. Tente novamente.');
    chmod($temp,0600);
    if(!@rename($temp,$arquivo)) throw new \RuntimeException('Não foi possível concluir o lote. Tente novamente.');
}
function dashboardCargaAbrir($token,$assinatura,$somenteLeitura=false,$dono='',$geral=false,$abcClientes=false) {
    $base=dashboardCargaDiretorio();
    if(!is_dir($base) && !mkdir($base,0700,true)) throw new \RuntimeException('Não foi possível preparar o carregamento.');
    if(is_link($base) || !chmod($base,0700)) throw new \RuntimeException('Não foi possível proteger o carregamento.');
    if($token!=='' && !preg_match('/^[a-f0-9]{32}$/',$token)) throw new \RuntimeException('Carregamento inválido. Atualize o indicador.');
    if($token==='') {
        if($somenteLeitura) throw new \RuntimeException('Atualize o indicador antes de exportar.');
        // Limpeza de cargas expiradas; dados ficam fora da pasta pública.
        foreach(glob($base.'/*/estado.json') as $old) {
            $oldState=json_decode(@file_get_contents($old),true);
            if(filemtime($old)>=time()-3600 && ($dono==='' || !isset($oldState['dono']) || $oldState['dono']!==$dono)) continue;
            $oldLock=@fopen(dirname($old).'/lock','c');
            if(!$oldLock || !flock($oldLock,LOCK_EX|LOCK_NB)) { if($oldLock) fclose($oldLock);continue; }
            foreach(glob(dirname($old).'/*') as $file) if(is_file($file)) @unlink($file);
            flock($oldLock,LOCK_UN);fclose($oldLock);@rmdir(dirname($old));
        }
        $token=bin2hex(random_bytes(16));mkdir($base.'/'.$token,0700);
        dashboardCargaSalvar($base.'/'.$token.'/estado.json',array('assinatura'=>$assinatura,'dono'=>$dono,'expira'=>time()+3600,'consultas'=>array(),'concluida'=>false,'geral'=>$geral,'abcClientes'=>$abcClientes));
    }
    $dir=$base.'/'.$token;$lock=@fopen($dir.'/lock','c');
    if(!$lock || !flock($lock,($geral || $abcClientes) ? LOCK_EX|LOCK_NB : LOCK_EX)) {
        if($lock) fclose($lock);
        throw new \RuntimeException('Carregamento indisponível ou em uso. Atualize o indicador.');
    }
    @chmod($dir.'/lock',0600);
    $estado=json_decode(@file_get_contents($dir.'/estado.json'),true);
    if(!is_array($estado) || !isset($estado['expira'],$estado['assinatura'],$estado['consultas'],$estado['concluida']) || $estado['expira']<time() || !hash_equals($estado['assinatura'],$assinatura) || (!empty($estado['geral']) !== $geral) || (!empty($estado['abcClientes']) !== $abcClientes) || ($somenteLeitura && !$estado['concluida'])) {
        flock($lock,LOCK_UN);fclose($lock);throw new \RuntimeException('Os resultados expiraram ou os filtros/permissões mudaram. Atualize o indicador.');
    }
    $GLOBALS['dashboardCarga']=array('dir'=>$dir,'token'=>$token,'estado'=>$estado,'lock'=>$lock,'leitura'=>$somenteLeitura,'progresso'=>array(),'geral'=>$geral,'abcClientes'=>$abcClientes);
}
function dashboardCargaAssinatura($auth,$indicador,$entrada,$tenant,$vinculos) {
    unset($indicador['_comercialSomenteVisual'],$indicador['_abaComercial']);
    $filtros=array();foreach($entrada as $key=>$value) if(strpos($key,'f_')===0 || $key==='cotacoesDados') $filtros[$key]=$value;
    ksort($filtros);
    if(dashboardCargaGeralHabilitada($indicador)) $filtros=array(dashboardCargaGeralAssinaturaEntrada($entrada),date('Y-m-d'));
    return hash('sha256',serialize(array($tenant,$auth,$indicador,$filtros,$vinculos)));
}
function dashboardCargaInicializar($conexao,$auth,$indicador,$entrada,$tenant,$leitura=false) {
    if (isset($indicador['abcClientes']['habilitado'], $indicador['abcClientes']['leituraEmLotes'])
        && $indicador['abcClientes']['habilitado'] === true && $indicador['abcClientes']['leituraEmLotes'] === true) {
        dashboardAbcClientesLotesInicializar($auth,$indicador,$entrada,$tenant,$leitura);
        return;
    }
    // Revalida vínculos de empresa/supervisor a cada continuação e exportação.
    $error='';$roles=dashboardIndicadorConsultarDireto($conexao,'SELECT v.C007_Id AS usuarioId, COALESCE(v.C007_C007_Id,0) AS supervisorId, c.C019_C004_Id AS empresaId FROM C007 v LEFT JOIN C019 c ON c.C019_C007_Id=v.C007_Id ORDER BY v.C007_Id,c.C019_C004_Id',array(),$error);
    if($roles===false) throw new \RuntimeException($error);
    $token=isset($entrada['cargaToken'])?$entrada['cargaToken']:'';
    if(!is_string($token)) throw new \RuntimeException('Carregamento inválido. Atualize o indicador.');
    $dono=hash('sha256',serialize(array($tenant,$auth['usuario'],$auth['empresa'],$indicador['id'])));
    $geral=dashboardCargaGeralHabilitada($indicador);
    dashboardCargaAbrir($token,dashboardCargaAssinatura($auth,$indicador,$entrada,$tenant,$roles),$leitura,$dono,$geral);
    if($geral || !empty($indicador['desempenhoComercial']['carregamentoProgressivo']) || !empty($indicador['painelCotacoes']['carregamentoProgressivo'])) {
        // Orçamento só desta requisição, sem alterar o snapshot ou a assinatura.
        // No modo automático, fontes rápidas cabem na mesma chamada. Fontes
        // lentas continuam cedendo pelo orçamento de tempo antes da próxima.
        $limite=$geral && !isset($indicador['carregamentoProgressivo']) ? 24 : 6;
        $GLOBALS['dashboardCarga']['lotesAgrupados']=array('inicio'=>microtime(true),'executados'=>0,'limite'=>$limite);
    }
    if($geral) $GLOBALS['dashboardCarga']['limitesOpcionais']=!isset($indicador['carregamentoProgressivo']);
    if(!$geral) @ini_set('memory_limit','512M');
}
function dashboardCargaFinalizar($concluida=false) {
    if(!dashboardCargaAtiva()) return;
    $ctx=&$GLOBALS['dashboardCarga'];
    if($concluida) $ctx['estado']['concluida']=true;
    try { dashboardCargaSalvar($ctx['dir'].'/estado.json',$ctx['estado']); }
    finally { flock($ctx['lock'],LOCK_UN);fclose($ctx['lock']);unset($GLOBALS['dashboardCarga']); }
}
// Localiza somente cláusulas do SELECT externo. Agrupamentos, ordenações e
// limites das propostas/serviços continuam pertencendo aos seus subSELECTs.
function dashboardCargaClausulasExternas($sql) {
    $depth=0;$quote='';$positions=array();$length=strlen($sql);
    for($i=0;$i<$length;$i++) {
        $c=$sql[$i];
        if($quote!=='') { if($c==='\\') $i++; elseif($c===$quote) { if($i+1<$length && $sql[$i+1]===$quote) $i++;else $quote=''; } continue; }
        if($c==="'" || $c==='"' || $c==='`') { $quote=$c;continue; }
        if($c==='(') $depth++;elseif($c===')') $depth--;
        elseif($depth===0 && ($i===0 || !preg_match('/[a-zA-Z0-9_]/',$sql[$i-1]))
            && preg_match('/\G(GROUP\s+BY|HAVING|ORDER\s+BY|LIMIT)\b/i',$sql,$m,0,$i)) {
            $positions[]=array('clausula'=>preg_replace('/\s+/',' ',strtoupper($m[1])),
                'inicio'=>$i,'conteudo'=>$i+strlen($m[0]));
            $i+=strlen($m[0])-1;
        }
    }
    return $positions;
}
// Insere uma restrição adicional no SELECT externo, sem modificar descoberta
// de serviços/toneladas, conversão ou o período original de seus subSELECTs.
function dashboardCargaClausula($sql,$clausula) {
    $positions=dashboardCargaClausulasExternas($sql);
    $at=$positions?$positions[0]['inicio']:strlen($sql);
    $prefix=substr($sql,0,$at);
    return $prefix.$clausula.' '.substr($sql,$at);
}
function dashboardCargaSqlPaginada($sql,$offset) {
    $query=rtrim($sql);$group=null;$order=null;$limit=null;
    $clauses=dashboardCargaClausulasExternas($query);
    foreach($clauses as $clause) {
        if($clause['clausula']==='GROUP BY') $group=$clause;
        elseif($clause['clausula']==='ORDER BY') $order=$clause;
        elseif($clause['clausula']==='LIMIT') $limit=$clause;
    }
    if($limit!==null) $query=rtrim(substr($query,0,$limit['inicio']));
    // Preserve a ordenação externa existente. Um GROUP BY de cliente_proposta
    // não transforma a consulta de documentos em uma consulta agregada.
    if($order===null) {
        if(strpos($query,'AS comercial_base')!==false) $query.=' ORDER BY clienteId,responsavelId,mes';
        elseif($group!==null) {
            $end=strlen($query);
            foreach($clauses as $clause) if($clause['inicio']>$group['inicio']) $end=min($end,$clause['inicio']);
            $query.=' ORDER BY '.trim(substr($query,$group['conteudo'],$end-$group['conteudo']));
        } else $query.=' ORDER BY empresaId,id';
    } elseif($group===null && strpos($query,'AS comercial_base')===false
        && preg_match('/\b(?:FROM|JOIN)\s+(T003|T005|T007)\s+d\b/i',$query,$table)) {
        // Empresa desempata documentos com mesmo número/data entre filiais.
        $query.=',d.'.$table[1].'_C004_Id';
    }
    return $query.' LIMIT 2000 OFFSET '.(int)$offset;
}
function dashboardCargaVerificarOrcamento() {
    $ctx=&$GLOBALS['dashboardCarga'];
    $limite=isset($ctx['lotesAgrupados']['limite'])?$ctx['lotesAgrupados']['limite']:6;
    if(!empty($ctx['lotesAgrupados']) && ($ctx['lotesAgrupados']['executados']>=$limite
        || ($ctx['lotesAgrupados']['executados']>0 && microtime(true)-$ctx['lotesAgrupados']['inicio']>=1.2))) throw new DashboardCargaPendente();
}
function dashboardCargaMemorizarFonte($chave,$linhas,$bytes,$memoria) {
    $ctx=&$GLOBALS['dashboardCarga'];
    $usados=isset($ctx['bytesEmMemoria'])?$ctx['bytesEmMemoria']:0;
    // Arrays PHP podem ocupar muito mais que o JSON. Conta também a memória
    // materializada e reserva espaço para os cálculos e a resposta completa.
    $custo=max($bytes,$memoria);
    $limite=trim((string)ini_get('memory_limit'));
    $unidade=strtolower(substr($limite,-1));$limiteBytes=(float)$limite;
    if($unidade==='g') $limiteBytes*=1073741824;
    elseif($unidade==='m') $limiteBytes*=1048576;
    elseif($unidade==='k') $limiteBytes*=1024;
    $temEspaco=$limiteBytes<=0 || memory_get_usage(true)+16777216<$limiteBytes;
    if($custo+$usados<=8388608 && $temEspaco && !isset($ctx['fontesEmMemoria'][$chave])) {
        $ctx['fontesEmMemoria'][$chave]=$linhas;
        $ctx['bytesEmMemoria']=$usados+$custo;
    }
    return $linhas;
}
function dashboardCargaConsultar($conexao,$sql,$params,&$erro) {
    if(empty($GLOBALS['dashboardCarga']['lotesAgrupados'])) return dashboardCargaConsultarLote($conexao,$sql,$params,$erro);
    while(true) {
        $antes=$GLOBALS['dashboardCarga']['lotesAgrupados']['executados'];
        try { return dashboardCargaConsultarLote($conexao,$sql,$params,$erro); }
        catch(DashboardCargaPendente $e) {
            // O lote concluído pode ser devolvido do cache mesmo no limite.
            // Se nenhum novo lote coube, devolve o progresso ao navegador.
            if($GLOBALS['dashboardCarga']['lotesAgrupados']['executados']===$antes) throw $e;
        }
    }
}
function dashboardCargaConsultarLote($conexao,$sql,$params,&$erro) {
    if(dashboardCargaGeralAtiva()) return dashboardCargaGeralConsultar($conexao,$sql,$params,$erro);
    $ctx=&$GLOBALS['dashboardCarga'];$key=hash('sha256',$sql.serialize($params));
    if(!isset($ctx['estado']['consultas'][$key])) {
        $financial=strpos($sql,'AS comercial_base')!==false || preg_match('/\b(?:FROM|JOIN)\s+(T003|T005|T007)\s+d\b/',$sql);
        $chunkSql=$sql;
        if(strpos($sql,'AS comercial_base')!==false) $chunkSql=dashboardCargaClausula($sql," WHERE data>=:loteInicio AND data<DATE_ADD(:loteFim, INTERVAL 1 DAY)");
        elseif($financial && preg_match('/\b(?:FROM|JOIN)\s+(T003|T005|T007)\s+d\b/',$sql,$m)) $chunkSql=dashboardCargaClausula($sql," AND d.{$m[1]}_Data_Emissao>=:loteInicio AND d.{$m[1]}_Data_Emissao<DATE_ADD(:loteFim, INTERVAL 1 DAY)");
        $windows=$financial?dashboardCargaJanelas($params['dataInicio'],$params['dataFim']):array(array(null,null));
        $ctx['estado']['consultas'][$key]=array('sql'=>$chunkSql,'janelas'=>$windows,'janela'=>0,'offset'=>0,'paginas'=>array(),'registros'=>0,'concluida'=>false,'financeiro'=>(bool)$financial,'inicioJanela'=>0);
        // Uma nova aba pode acrescentar fontes ao mesmo snapshot concluído.
        if(!$ctx['leitura']) $ctx['estado']['concluida']=false;
    }
    $q=&$ctx['estado']['consultas'][$key];
    if($q['concluida']) {
        if(isset($ctx['fontesEmMemoria'][$key])) return $ctx['fontesEmMemoria'][$key];
        $memoriaAntes=memory_get_usage();
        $rows=array();$bytes=0;foreach($q['paginas'] as $file) {
            $json=file_get_contents($ctx['dir'].'/'.$file);$bytes+=strlen($json);
            $part=json_decode($json,true);
            if(!is_array($part)) throw new \RuntimeException('Lote indisponível. Atualize o indicador.');
            foreach($part as $row) $rows[]=$row;
        }
        return dashboardCargaMemorizarFonte($key,$rows,$bytes,memory_get_usage()-$memoriaAntes);
    }
    if($ctx['leitura']) throw new \RuntimeException('Atualize o indicador para concluir a consulta antes de exportar.');
    dashboardCargaVerificarOrcamento();
    $runParams=$params;$window=$q['janelas'][$q['janela']];
    if($q['financeiro']) {
        $runParams['loteInicio']=$window[0];$runParams['loteFim']=$window[1];
        // Nas três fontes comerciais, essas datas delimitam os documentos
        // emitidos. Reduzir também a leitura interna evita materializar dois
        // anos antes de aplicar o filtro externo em versões antigas do MySQL.
        if(strpos($q['sql'],'AS comercial_base')!==false) {
            $runParams['dataInicio']=$window[0];$runParams['dataFim']=$window[1];
        }
        $query=dashboardCargaSqlPaginada($q['sql'],$q['offset']);
    } else $query=$q['sql'];
    if(!empty($ctx['lotesAgrupados'])) $ctx['lotesAgrupados']['executados']++;
    $rows=dashboardIndicadorConsultarDireto($conexao,$query,$runParams,$erro);
    if($rows===false) {
        $timeout=in_array(isset($GLOBALS['dashboardConsultaErroNumero'])?$GLOBALS['dashboardConsultaErroNumero']:0,array(3024,1969,1317),true);
        if($timeout && $q['financeiro'] && $window[0]<$window[1]) {
            foreach(array_slice($q['paginas'],$q['inicioJanela']) as $file) {
                $part=json_decode(@file_get_contents($ctx['dir'].'/'.$file),true);
                if(is_array($part)) $q['registros']-=count($part);
                @unlink($ctx['dir'].'/'.$file);
            }
            $q['paginas']=array_slice($q['paginas'],0,$q['inicioJanela']);
            $days=(new \DateTime($window[0]))->diff(new \DateTime($window[1]))->days;
            $middle=(new \DateTime($window[0]))->modify('+'.intdiv($days,2).' days')->format('Y-m-d');
            array_splice($q['janelas'],$q['janela'],1,array(array($window[0],$middle),array((new \DateTime($middle))->modify('+1 day')->format('Y-m-d'),$window[1])));
            $q['offset']=0;$erro='';
        } else return false;
    } else {
        $file=$key.'-'.count($q['paginas']).'.json';dashboardCargaSalvar($ctx['dir'].'/'.$file,$rows);$q['paginas'][]=$file;
        $q['registros']+=count($rows);
        if($q['financeiro'] && count($rows)===2000) $q['offset']+=2000;
        else { $q['janela']++;$q['offset']=0;$q['inicioJanela']=count($q['paginas']);$q['concluida']=$q['janela']>=count($q['janelas']); }
    }
    $ctx['progresso']=array('token'=>$ctx['token'],'intervalo'=>min($q['janela']+1,count($q['janelas'])),'intervalos'=>count($q['janelas']),'registros'=>$q['registros']);
    throw new DashboardCargaPendente();
}
