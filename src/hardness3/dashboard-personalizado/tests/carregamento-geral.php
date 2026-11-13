<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);

// Mysqli inteiramente substituído: não lê credenciais nem conecta ao cliente.
class GeralStatement {
    public $sql; public $valores = array();
    function __construct($sql) { $this->sql=$sql; }
    function bind_param($tipos, &...$valores) { $this->valores=$valores; return true; }
}
function mysqli_prepare($connection,$sql) { return new GeralStatement($sql); }
function mysqli_stmt_execute($stmt) {
    if (strpos($stmt->sql,'SELECT v.C007_Id AS usuarioId')===0) $GLOBALS['geralAuthQueries']++;
    else $GLOBALS['geralQueries'][]=array($stmt->sql,$stmt->valores);
    return empty($GLOBALS['geralFalha']);
}
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_error($stmt) { return 'Falha simulada'; }
function mysqli_stmt_close($stmt) {}
function mysqli_num_rows($rows) { return count($rows); }
function mysqli_free_result($rows) {}
function mysqli_fetch_all($rows,$mode) { $GLOBALS['geralFetches']++; return $rows; }
function mysqli_stmt_get_result($stmt) {
    if (strpos($stmt->sql,'SELECT v.C007_Id AS usuarioId')===0) return $GLOBALS['geralVinculos'];
    if (isset($GLOBALS['geralOverride'])) return $GLOBALS['geralOverride'];
    if (strpos($stmt->sql,'SELECT D060_Id AS valor')===0) return array(array('valor'=>'1','titulo'=>'Fictício'));
    if (strpos($stmt->sql,'SELECT COUNT(*)')===0) return array(array('quantidade'=>2,'valor'=>'35.50','resumo0'=>'2'));
    return array(array('id'=>1,'descricao'=>'Fictício','data'=>'2026-10-16','periodo'=>'2026-10-16','rotulo'=>'16/10','chave'=>'aberto','valor'=>35.5,'quantidade'=>2,'peso'=>1.0));
}
function checkGeral($ok,$message) { if (!$ok) throw new \RuntimeException($message); }
function rejeitaGeral($call,$message) {
    try { $call(); } catch (DashboardCargaPendente $e) { throw new \RuntimeException($message); } catch (\RuntimeException $e) { return; }
    throw new \RuntimeException($message);
}
function limparGeral($token) {
    $dir=dashboardCargaDiretorio().'/'.$token;
    foreach (glob($dir.'/*') as $file) if (is_file($file) || is_link($file)) unlink($file);
    @rmdir($dir);
}
$oldEnv=getenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL');
putenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL');
$GLOBALS['geralAuthQueries']=0; $GLOBALS['geralQueries']=array(); $GLOBALS['geralFetches']=0;
$GLOBALS['geralVinculos']=array(array('usuarioId'=>7,'supervisorId'=>0,'empresaId'=>3));
$sql='SELECT d.T003_Id AS id, d.T003_Valor_Total AS valor, d.T003_Data_Emissao AS data, d.T003_Observacao AS descricao FROM T003 d WHERE d.T003_C004_Id = :empresa AND d.T003_Data_Emissao BETWEEN :dataInicio AND :dataFim';
$indicador=array('id'=>'fixture_geral','titulo'=>'Fixture geral','carregamentoProgressivo'=>true,'sqlBase'=>$sql,
    'filtros'=>array(array('id'=>'dataInicio','tipo'=>'data','padrao'=>'2026-10-01'),array('id'=>'dataFim','tipo'=>'data','padrao'=>'2026-10-31'),
        array('id'=>'setor','tipo'=>'selecao','padrao'=>'','opcoesSql'=>'SELECT D060_Id AS valor, D060_Descricao_Grupo AS titulo FROM D060 INNER JOIN D009 ON D009_C004_Id = :empresa')),
    'card'=>array('campo'=>'valor'),'resumos'=>array(array('campo'=>'id')),
    'grafico'=>array('campoData'=>'data'),'composicao'=>array('campo'=>'descricao'),
    'calendarios'=>array(array('id'=>'agenda','campoData'=>'data')),
    'listagem'=>array('limite'=>10,'agrupamento'=>array('campo'=>'descricao')),
    'secoes'=>array(array('titulo'=>'Bloco','sqlBase'=>$sql,'colunas'=>array(array('campo'=>'valor')))));
$auth=array('usuario'=>7,'empresa'=>3,'perfil'=>1); $tenant='fixture-'.bin2hex(random_bytes(12));
$entrada=array('id'=>$indicador['id'],'action'=>'indicator','f_dataInicio'=>'2026-10-01','f_dataFim'=>'2026-10-31');
$sync=dashboardIndicadorExecutar(null,$indicador,3,$entrada,$error);
$syncQueries=$GLOBALS['geralQueries'];
checkGeral($sync!==false && count($syncQueries)===7,'Fixture deve exercitar sete fontes');
checkGeral($sync['indicador']['carregamentoProgressivo'] && !$sync['indicador']['limitarPeriodoProgressivo'],'Metadata geral incorreta');
$GLOBALS['geralQueries']=array(); $token=''; $steps=0;
try {
    for ($step=0;$step<20;$step++) {
        $input=$entrada+array('carregarProgressivo'=>'1','cargaToken'=>$token);
        dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant);
        $token=$GLOBALS['dashboardCarga']['token']; $before=count($GLOBALS['geralQueries']);
        try {
            $progressivo=dashboardIndicadorExecutar(null,$indicador,3,$input,$error);
            checkGeral($progressivo!==false,'Execução falhou: '.$error);
            checkGeral(count($GLOBALS['geralQueries'])===$before,'Conclusão repetiu SQL');
            dashboardCargaFinalizar(true); break;
        } catch (DashboardCargaPendente $e) {
            checkGeral(count($GLOBALS['geralQueries'])===$before+1,'Cada etapa deve executar uma fonte nova');
            checkGeral($GLOBALS['dashboardCarga']['progresso']['fontesConcluidas']===++$steps,'Progresso incorreto');
            dashboardCargaFinalizar();
        }
    }
    checkGeral(isset($progressivo) && $progressivo===$sync,'Resultados completos diferentes');
    checkGeral($steps===7 && $GLOBALS['geralQueries']===$syncQueries,'SQL, parâmetros, limites ou ordem mudaram');
    checkGeral($GLOBALS['geralAuthQueries']===8,'Vínculos não revalidados em toda continuação');
    $dir=dashboardCargaDiretorio().'/'.$token;
    checkGeral((fileperms($dir)&0777)===0700,'Diretório sem proteção');
    foreach (glob($dir.'/*.json') as $file) checkGeral((fileperms($file)&0777)===0600,'Fonte sem proteção');
    $input=$entrada+array('cargaToken'=>$token,'formato'=>'csv');
    $before=count($GLOBALS['geralQueries']);
    dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant,true);
    $export=dashboardIndicadorExecutar(null,$indicador,3,$input,$error);
    checkGeral($export===$sync && count($GLOBALS['geralQueries'])===$before,'Exportação repetiu consultas ou alterou os dados');
    rejeitaGeral(function() { $error=''; dashboardCargaGeralConsultar(null,'SELECT desconhecida',array(),$error); },'Exportação aceitou fonte desconhecida');
    dashboardCargaFinalizar();
    // Uma carga concluída também não pode acrescentar fontes em modo normal.
    dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant);
    rejeitaGeral(function() { $error=''; dashboardCargaGeralConsultar(null,'SELECT desconhecida',array(),$error); },'Carga concluída mudou');
    dashboardCargaFinalizar();
    foreach (array('tab'=>'outra','ufMapa'=>'SP','paretoClasse'=>'A','f_setor'=>'1') as $key=>$value) {
        $changed=$input; $changed[$key]=$value;
        rejeitaGeral(function() use($auth,$indicador,$changed,$tenant) { dashboardCargaInicializar(null,$auth,$indicador,$changed,$tenant,true); },'Contexto mudou sem invalidar: '.$key);
    }
    foreach (array(array('usuario'=>8,'empresa'=>3,'perfil'=>1),array('usuario'=>7,'empresa'=>4,'perfil'=>1)) as $outroAuth) {
        rejeitaGeral(function() use($outroAuth,$indicador,$input,$tenant) { dashboardCargaInicializar(null,$outroAuth,$indicador,$input,$tenant,true); },'Vazamento entre usuário/empresa');
    }
    rejeitaGeral(function() use($auth,$indicador,$input) { dashboardCargaInicializar(null,$auth,$indicador,$input,'outro-tenant',true); },'Vazamento entre clientes');
    $GLOBALS['geralVinculos'][0]['supervisorId']=99;
    rejeitaGeral(function() use($auth,$indicador,$input,$tenant) { dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant,true); },'Vínculo alterado não invalidou');
    $GLOBALS['geralVinculos'][0]['supervisorId']=0;
    $mudou=$indicador; $mudou['sqlBase'].=' AND 1=1';
    rejeitaGeral(function() use($auth,$mudou,$input,$tenant) { dashboardCargaInicializar(null,$auth,$mudou,$input,$tenant,true); },'Configuração alterada não invalidou');
    $lock=fopen($dir.'/lock','c'); flock($lock,LOCK_EX);
    rejeitaGeral(function() use($auth,$indicador,$input,$tenant) { dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant); },'Concorrência deve ser rejeitada sem repetir SQL');
    flock($lock,LOCK_UN); fclose($lock);
    $state=json_decode(file_get_contents($dir.'/estado.json'),true);
    $key=array_keys($state['consultas'])[0]; file_put_contents($dir.'/'.$key.'.json','[]');
    dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant,true);
    rejeitaGeral(function() use($indicador,$input) { $error=''; dashboardIndicadorExecutar(null,$indicador,3,$input,$error); },'Fonte corrompida aceita');
    dashboardCargaFinalizar();
    $state['expira']=time()-1; file_put_contents($dir.'/estado.json',json_encode($state));
    rejeitaGeral(function() use($auth,$indicador,$input,$tenant) { dashboardCargaInicializar(null,$auth,$indicador,$input,$tenant,true); },'Carga expirada aceita');
} finally {
    dashboardCargaFinalizar(); if ($token!=='') limparGeral($token);
}
// Limites e falhas nunca armazenam resultados incompletos.
$paramsLimites=array('empresa'=>3,'dataInicio'=>'2026-10-01','dataFim'=>'2026-10-31');
foreach (array('linhas','bytesFonte','bytesTotal','fontes','falha') as $caso) {
    dashboardCargaAbrir('','limites-'.$caso,false,'',true); $token=$GLOBALS['dashboardCarga']['token'];
    try {
        if ($caso==='linhas') $GLOBALS['geralOverride']=array_fill(0,20001,array('valor'=>1));
        if ($caso==='bytesFonte') $GLOBALS['geralOverride']=array(array('valor'=>str_repeat('a',8388609)));
        if ($caso==='bytesTotal') $GLOBALS['dashboardCarga']['estado']['bytes']=67108864;
        if ($caso==='fontes') $GLOBALS['dashboardCarga']['estado']['consultas']=array_fill(0,100,array());
        if ($caso==='falha') $GLOBALS['geralFalha']=true;
        $beforeFetch=$GLOBALS['geralFetches']; $beforeQueries=count($GLOBALS['geralQueries']);
        if ($caso==='linhas' || $caso==='falha') {
            checkGeral(dashboardCargaGeralConsultar(null,$sql,$paramsLimites,$error)===false,'Falha/limite ignorado');
            checkGeral($GLOBALS['geralFetches']===$beforeFetch,'Limite de linhas deve impedir fetch_all');
        } else rejeitaGeral(function() use($sql,$paramsLimites) { $error=''; dashboardCargaGeralConsultar(null,$sql,$paramsLimites,$error); },'Limite ignorado: '.$caso);
        if ($caso==='fontes') checkGeral(count($GLOBALS['geralQueries'])===$beforeQueries,'Limite de fontes consultou banco');
        else {
            checkGeral(count($GLOBALS['geralQueries'])===$beforeQueries+1,'Fixture não chegou à consulta');
            checkGeral(!$GLOBALS['dashboardCarga']['estado']['consultas'],'Fonte incompleta armazenada');
        }
    } finally {
        unset($GLOBALS['geralOverride'],$GLOBALS['geralFalha']); dashboardCargaFinalizar(); limparGeral($token);
    }
}
foreach (array('desempenhoComercial','painelCotacoes','clientesInativos','saldoCaixa') as $especial) {
    $i=$indicador; $i[$especial]=array('habilitado'=>true);
    checkGeral(!dashboardCargaGeralHabilitada($i),'Motor especializado entrou no modo geral');
}
$i=$indicador; $i['previsaoCaixa']=array('habilitada'=>true);
checkGeral(!dashboardCargaGeralHabilitada($i),'Fonte externa entrou no modo geral');
foreach (array(array('edicaoTitulo'=>array('habilitado'=>true)),array('agenda'=>array('habilitarInclusao'=>true))) as $acao) {
    $i=$indicador; $i['listagem']['detalhamento']=$acao;
    checkGeral(!dashboardCargaGeralHabilitada($i),'Edição local poderia deixar exportação antiga após atualizar a tela');
}
$i=$indicador; unset($i['carregamentoProgressivo']); checkGeral(!dashboardCargaHabilitada($i),'Ativação não é opcional');
putenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL=0'); checkGeral(!dashboardCargaHabilitada($indicador),'Desativação ignorada');
if ($oldEnv===false) putenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL'); else putenv('HARDNESS_DASHBOARD_PROGRESSIVE_GENERAL='.$oldEnv);
echo "OK: sete fontes em etapas; paridade completa/SQL/tipos, exportação sem repetição, isolamento, revalidação, concorrência, corrupção, expiração e limites.\n";
