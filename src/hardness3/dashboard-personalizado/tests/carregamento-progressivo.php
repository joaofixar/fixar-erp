<?php
namespace hardness;
// Somente dados artificiais: nenhum acesso a banco ou credenciais.
require dirname(__DIR__).'/carregamento-progressivo.php';
require dirname(__DIR__).'/desempenho-comercial.php';
require dirname(__DIR__).'/painel-cotacoes.php';
function check($value,$message) { if(!$value) throw new \RuntimeException($message); }
$GLOBALS['calls']=0;$GLOBALS['timeout']=false;
function dashboardIndicadorConsultarDireto($conn,$sql,$params,&$error) {
    $GLOBALS['calls']++;
    check(strpos($sql,'LIMIT 2000 OFFSET ')!==false,'Financeiro paginado');
    preg_match('/OFFSET (\d+)$/',$sql,$m);$offset=(int)$m[1];
    if($GLOBALS['timeout'] && $params['loteInicio']==='2024-10-01' && $params['loteFim']==='2024-10-31' && $offset===2000) {
        $GLOBALS['dashboardConsultaErroNumero']=3024;$error='Timeout simulado';return false;
    }
    $GLOBALS['dashboardConsultaErroNumero']=0;
    $rows=array();for($id=1;$id<=5003;$id++) {
        $date='2024-10-'.str_pad(($id%31)+1,2,'0',STR_PAD_LEFT);
        if($date>=$params['loteInicio'] && $date<=$params['loteFim']) $rows[]=array('id'=>$id,'valor'=>$id,'data'=>$date);
    }
    return array_slice($rows,$offset,2000);
}
function runJob($timeout=false) {
    $GLOBALS['timeout']=$timeout;$token='';$signature='fixture-signature';$error='';
    $sql='SELECT d.T003_Id AS id FROM T003 d WHERE d.T003_Data_Emissao>=:dataInicio ORDER BY d.T003_Id LIMIT 20001';
    $params=array('dataInicio'=>'2024-10-01','dataFim'=>'2024-11-30');
    for($step=0;$step<30;$step++) {
        dashboardCargaAbrir($token,$signature);$token=$GLOBALS['dashboardCarga']['token'];$before=$GLOBALS['calls'];
        try {
            $rows=dashboardCargaConsultar(null,$sql,$params,$error);
            check($GLOBALS['calls']===$before,'Replay sem query');dashboardCargaFinalizar(true);break;
        } catch(DashboardCargaPendente $e) {
            check($GLOBALS['calls']===$before+1,'Uma query por requisição');dashboardCargaFinalizar();
        }
    }
    check(isset($rows) && count($rows)===5003,'Todos os registros, inclusive após subdividir timeout');
    check(count(array_unique(array_column($rows,'id')))===5003,'Sem duplicações após rollback de lote');
    check(array_sum(array_column($rows,'valor'))===5003*5004/2,'Totais completos');
    $before=$GLOBALS['calls'];dashboardCargaAbrir($token,$signature,true);
    check(dashboardCargaConsultar(null,$sql,$params,$error)===$rows && $GLOBALS['calls']===$before,'Exportação reutiliza todos os lotes');dashboardCargaFinalizar();
    $denied=false;
    try { dashboardCargaAbrir($token,'other-user');dashboardCargaFinalizar(); } catch(\RuntimeException $e) { $denied=true; }
    check($denied,'Assinatura distinta rejeitada');
    $dir=dashboardCargaDiretorio().'/'.$token;
    check((fileperms($dir)&0777)===0700 && (fileperms($dir.'/estado.json')&0777)===0600,'Cache privado');
    foreach(glob($dir.'/*') as $file) unlink($file);rmdir($dir);
}
runJob();runJob(true);
$sql="SELECT SUM(valor) FROM (SELECT 'ORDER BY LIMIT' AS label FROM T003 d WHERE x=1 GROUP BY d.id) AS comercial_base GROUP BY clienteId LIMIT 12001";
$chunk=dashboardCargaClausula($sql,' WHERE data>=:loteInicio');
check(strpos($chunk,"GROUP BY d.id) AS comercial_base  WHERE data>=:loteInicio GROUP BY clienteId")!==false,'Só restringe SELECT externo');
// Um agrupamento de proposta não pode ser copiado para o ORDER BY externo.
$config=array('atribuirClienteProposta'=>true,'clienteProposta'=>true,'origemIA'=>true,'_empresas'=>array());
foreach(array('orcamentos'=>'T003','pedidos'=>'T005','notas'=>'T007') as $type=>$table) {
    $base=dashboardCotacoesSql($type,$config);
    $chunk=dashboardCargaClausula($base," AND d.{$table}_Data_Emissao>=:loteInicio AND d.{$table}_Data_Emissao<DATE_ADD(:loteFim, INTERVAL 1 DAY)");
    $page=dashboardCargaSqlPaginada($chunk,2000);
    $expected=preg_replace('/\s+LIMIT\s+\d+\s*$/i','',$chunk).",d.{$table}_C004_Id LIMIT 2000 OFFSET 2000";
    check($page===$expected,'Mantém SELECT, junções, condições e ordenação do documento: '.$type);
    $clauses=dashboardCargaClausulasExternas($page);
    check(array_column($clauses,'clausula')===array('ORDER BY','LIMIT'),'Agrupamento interno não cria ordenação duplicada: '.$type);
}
$nested="SELECT d.T003_Id AS id, (SELECT COUNT(*) FROM T003A a GROUP BY a.T003A_T003_Id ORDER BY a.T003A_T003_Id LIMIT 1) AS valor FROM T003 d WHERE d.T003_C004_Id=:empresa LIMIT 20001";
check(dashboardCargaSqlPaginada($nested,0)===substr($nested,0,-12).' ORDER BY empresaId,id LIMIT 2000 OFFSET 0','ORDER BY e LIMIT internos preservados');
$having='SELECT d.T005_C004_Id AS empresaId, COUNT(*) AS valor FROM T005 d WHERE d.T005_C004_Id=:empresa GROUP BY d.T005_C004_Id HAVING COUNT(*)>0 LIMIT 40001';
check(dashboardCargaSqlPaginada($having,0)===substr($having,0,-12).' ORDER BY d.T005_C004_Id LIMIT 2000 OFFSET 0','HAVING não é copiado para a ordenação');
$quoted="SELECT 'GROUP BY ORDER BY LIMIT' AS descricao, `someLIMIT` AS id FROM T003 d WHERE d.T003_C004_Id=:empresa ORDER BY COALESCE(d.T003_Id,0) LIMIT 20";
check(dashboardCargaSqlPaginada($quoted,0)===substr($quoted,0,-9).',d.T003_C004_Id LIMIT 2000 OFFSET 0','Literais, identificadores e função na ordenação não geram novas cláusulas');
$error='';
check(dashboardComercialPeriodo(array('dataInicio'=>'2024-10-14','dataFim'=>'2026-10-14'),$error,true)!==false,'Vendedores aceita 2 anos');
check(dashboardComercialPeriodo(array('dataInicio'=>'2024-10-13','dataFim'=>'2026-10-14'),$error,true)===false,'Vendedores rejeita excesso');
check(dashboardComercialPeriodo(array('dataInicio'=>'2024-10-14','dataFim'=>'2026-10-14'),$error,false)===false,'Legado preservado');
check(dashboardCotacoesPeriodo(array('f_dataInicio'=>'2024-10-14','f_dataFim'=>'2026-10-14'),$error,'2026-10-14',true)!==false,'Cotações aceita 2 anos');
check(dashboardCotacoesPeriodo(array('f_dataInicio'=>'2024-10-13','f_dataFim'=>'2026-10-14'),$error,'2026-10-14',true)===false,'Cotações rejeita excesso');
$a=array('usuario'=>7);$i=array('id'=>'v');$input=array('f_dataInicio'=>'2024-10-14');
check(dashboardCargaAssinatura($a,$i,$input,'tenant',array())===dashboardCargaAssinatura($a,$i+array('_abaComercial'=>'representantes','_comercialSomenteVisual'=>true),$input+array('cargaToken'=>'abc','format'=>'csv'),'tenant',array()),'Export e abas compartilham snapshot');
check(dashboardCargaAssinatura($a,$i,$input,'tenant',array())!==dashboardCargaAssinatura($a,$i,$input,'other-tenant',array()),'Isolamento entre clientes');
echo "OK: paginação, totais, timeout adaptativo, replay/export, permissões e período de 2 anos.\n";
