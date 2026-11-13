<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/access.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
function checkCotacoes($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
$config = json_decode(file_get_contents(__DIR__ . '/fixtures/painel-cotacoes.json'), true);
$indicator = $config['indicadores'][0];
$companies=array(array('id'=>1,'cnpj'=>'','nome'=>'Matriz'),array('id'=>5,'cnpj'=>'11.222.333/0001-81','nome'=>'Filial'));
$configured=dashboardCotacoesEmpresasConfiguradas($indicator['painelCotacoes'],$error);
$resolved=dashboardCotacoesIdentificarEmpresas($companies,$configured,$error);
checkCotacoes($resolved!==false && $resolved[1]['id']===5 && $resolved[1]['toneladas'],'Identificar filial pelo CNPJ, sem presumir ID');
if (isset($argv[1]) && $argv[1] === '--sql-mcp') {
    // Lê os WHERE reais dos repositórios MCP sem construir PDO nem abrir conexão.
    $root=dirname(dirname(__DIR__)) . '/mcp/src/Tools/';
    $definitions=array(
        'orcamentos'=>array('Quote/Summary','QuoteSummary',array(1,'2026-04-01','2026-04-15 23:59:59',null,null,null,null,null)),
        'pedidos'=>array('Order','OrderBacklog',array(1,'2026-04-01','2026-04-15 23:59:59',null,null,null)),
        'notas'=>array('Sales','SalesSummary',array(1,'2026-04-01','2026-04-15 23:59:59',null,null))
    );
    $out=array();
    foreach ($definitions as $kind=>$d) {
        require_once $root.$d[0].'/Domain/'.$d[1].'RepositoryInterface.php';
        require_once $root.$d[0].'/Infrastructure/Pdo'.$d[1].'Repository.php';
        $class=new \ReflectionClass('HardnessMcp\\Tools\\'.str_replace('/','\\',$d[0]).'\\Infrastructure\\Pdo'.$d[1].'Repository');
        $method=$class->getMethod('where');$method->setAccessible(true);
        $out[$kind]=$method->invokeArgs($class->newInstanceWithoutConstructor(),$d[2]);
    }
    require_once $root.'ItemReport/Domain/ItemReportRepositoryInterface.php';
    require_once $root.'ItemReport/Infrastructure/PdoItemReportRepository.php';
    $itemClass=new \ReflectionClass('HardnessMcp\\Tools\\ItemReport\\Infrastructure\\PdoItemReportRepository');
    $source=$itemClass->getProperty('source');$source->setAccessible(true);
    $parts=$itemClass->getMethod('queryParts');$parts->setAccessible(true);
    foreach (array('orcamentos'=>'quote','pedidos'=>'order','notas'=>'sales') as $kind=>$type) {
        $instance=$itemClass->newInstanceWithoutConstructor();$source->setValue($instance,$type);
        $out['itens_'.$kind]=$parts->invokeArgs($instance,array(5,'2026-04-01','2026-04-15 23:59:59',null,null,null));
    }
    echo json_encode($out);exit;
}
if (isset($argv[1]) && $argv[1] === '--sql') {
    $sql = array();
    $sqlConfig=$indicator['painelCotacoes'];$sqlConfig['_empresas']=$resolved;
    foreach (array('orcamentos', 'pedidos', 'notas') as $type) {
        $sql[$type] = dashboardCotacoesSql($type, $sqlConfig);
        $sql[$type . '_toneladas'] = dashboardCotacoesSqlToneladas($type, $sqlConfig);
    }
    foreach (array('pedidos', 'notas') as $type) $sql[$type . '_historico'] = dashboardCotacoesSql($type, $sqlConfig, true);
    $sql['pedidos_toneladas_historico']=dashboardCotacoesSqlToneladas('pedidos',$sqlConfig,true);
    echo json_encode($sql); exit;
}
checkCotacoes(dashboardCotacoesEmpresasConfiguradas(array('empresasConfiguradas'=>array()),$error)===false,'Configuração vazia não amplia acesso');
checkCotacoes(dashboardCotacoesEmpresasConfiguradas(array('empresasConfiguradas'=>array(array('cnpj'=>'inválido'))),$error)===false,'CNPJ inválido não amplia acesso');
$mismatch=$companies;$mismatch[1]['cnpj']='99999999000199';
checkCotacoes(dashboardCotacoesIdentificarEmpresas($mismatch,$configured,$error)===false,'Filial com CNPJ diferente nunca recebe as regras da Vendas Aço');
$teamConfig=array('equipes'=>array(
    array('id'=>'a','nome'=>'Equipe A','empresaId'=>1,'supervisores'=>array(10,20)),
    array('id'=>'b','nome'=>'Equipe B','empresaId'=>1,'supervisores'=>array(30)),
    array('id'=>'c','nome'=>'Equipe C','empresaId'=>1,'supervisores'=>array(40)),
    array('id'=>'d','nome'=>'Equipe D','empresaCnpj'=>'11222333000181','supervisoresNomes'=>array('SUPERVISOR D'))
));
$roster=array(array('id'=>7,'nome'=>'Vendedor','supervisorId'=>50,'supervisor'=>'Supervisor D'));
$fixed=dashboardCotacoesEquipes($teamConfig,$resolved,$roster,$error);
checkCotacoes(count($fixed)===4 && $fixed[3]['empresaId']===5 && $fixed[3]['supervisores']===array(50),'Quatro grupos, empresa pelo CNPJ e supervisor pelo nome exato disponível no cadastro');
checkCotacoes(count(dashboardCotacoesEquipes($teamConfig,array($resolved[0]),$roster,$error))===3,'Não expor equipe de empresa sem acesso');
checkCotacoes(dashboardCotacoesEquipes(array(),$resolved,$roster,$error)===null,'Personalizações sem equipes mantêm descoberta automática');
checkCotacoes(dashboardCotacoesEquipes(array('equipes'=>array('inválido')),$resolved,$roster,$error)===false,'Equipe malformada retorna erro de configuração');
$overlap=$teamConfig;$overlap['equipes'][1]['supervisores']=array(10);
checkCotacoes(dashboardCotacoesEquipes($overlap,$resolved,$roster,$error)===false,'Supervisor não pode duplicar equipes na mesma empresa');
$ambiguous=$roster;$ambiguous[]=array('id'=>8,'nome'=>'Outro','supervisorId'=>60,'supervisor'=>'Supervisor D');
checkCotacoes(dashboardCotacoesEquipes($teamConfig,$resolved,$ambiguous,$error)===false,'Nome ambíguo exige código, sem presumir supervisor');
checkCotacoes(dashboardPersonalizadoValidarConfiguracao($config, $errors, true), implode(' ', $errors));
checkCotacoes(dashboardPersonalizadoIndicadorSuportaRestricaoVendedor($indicator), 'Preservar liberação individual');
foreach (array(array('2026-10-15','2026-10-13'), array('2026-02-30','2026-10-13'), array('2024-10-13','2026-10-13'), array('2026-10-13','2026-10-14'), array(array('x'),'2026-10-13')) as $dates) {
    checkCotacoes(dashboardCotacoesPeriodo(array('f_dataInicio'=>$dates[0],'f_dataFim'=>$dates[1]),$error,'2026-10-13') === false, 'Rejeitar período inválido');
}
checkCotacoes(dashboardCotacoesPeriodo(array('f_dataInicio'=>'2025-12-20','f_dataFim'=>'2026-01-10'),$error,'2026-10-13') !== false, 'Aceitar virada de ano');
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class CotacoesStatement {
    public $sql; public $db; public $values;
    public function bind_param($types, &...$values) { $this->values=$values; return true; }
}
function mysqli_prepare($db, $sql) { $s=new CotacoesStatement(); $s->db=$db; $s->sql=$sql; return $s; }
function mysqli_stmt_execute($s) { $s->db->calls[]=array('sql'=>$s->sql,'values'=>$s->values); return true; }
function mysqli_stmt_get_result($s) {
    if (strpos($s->sql,'SELECT C004_Id')===0) return $s->db->companies;
    if (strpos($s->sql,'SELECT v.C007_Id')===0) return $s->db->sellers;
    return $s->db->rows;
}
function mysqli_fetch_all($rows,$mode) { return $rows; }
function mysqli_stmt_close($s) {}
$db=(object)array('calls'=>array(),'companies'=>$companies,'sellers'=>array(),'rows'=>array());
$entry=array('cotacoesDados'=>'periodo','f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15','f_vendedor'=>'999','f_empresa'=>'999','restrito'=>'0');
$restrict=array('vendedorId'=>7,'vendedorNome'=>'Teste');
$limited=dashboardPersonalizadoAplicarRestricaoUsuario($indicator,$restrict,$entry,$error);
$limited['_cotacoesUsuario']=7;
checkCotacoes($limited['_cotacoesRestricao']['vendedorId']===7 && $entry['f_dataInicio']==='2026-01-01','Datas livres, limite no servidor');
$r=dashboardIndicadorExecutar($db,$limited,1,array(),$error);
checkCotacoes($r!==false && !$db->calls,'Abertura do componente sem consultas desnecessárias');
$r=dashboardIndicadorExecutar($db,$limited,1,$entry,$error);
checkCotacoes($r!==false,$error);
checkCotacoes(count($db->calls)===8,'Carga com empresas, três fontes, itens por etapa e cadastro');
checkCotacoes($r['painelCotacoes']['equipes']===null,'Resposta preserva modo automático sem configuração');
checkCotacoes($r['painelCotacoes']['escopo']==='vendedor','Responder escopo individual');
checkCotacoes(strpos($db->calls[0]['sql'],'C019_C007_Id=?')!==false,'Consolidar somente empresas liberadas ao usuário');
foreach(array_slice($db->calls,1) as $call) {
    checkCotacoes((strpos($call['sql'],'IN (1,5)')!==false || strpos($call['sql'],'IN (5)')!==false) && strpos($call['sql'],'999')===false,'Empresa forjada não altera consulta');
    checkCotacoes(in_array(7,$call['values'],true),'Limite de vendedor aplicado a todas as fontes/cadastro');
    checkCotacoes(strpos($call['sql'],'v.C007_C007_Id_2 =')===false,'Supervisor 2 nunca entra no filtro');
}
$teamEntry=$entry;$team=dashboardPersonalizadoAplicarRestricaoUsuario($indicator,array_merge($restrict,array('escopo'=>'equipe')),$teamEntry,$error);
checkCotacoes(dashboardCotacoesEscopo($team)['equipeRestrita']===1,'Permissão explícita de equipe');
checkCotacoes(dashboardCotacoesEscopo($indicator)['restrito']===0,'Visão do gestor');
$legacy=json_decode(file_get_contents(__DIR__.'/fixtures/desempenho-comercial.json'),true)['indicadores'][0];
checkCotacoes(dashboardPersonalizadoAplicarRestricaoUsuario($legacy,array_merge($restrict,array('escopo'=>'equipe')),$teamEntry,$error)===false,'Indicador antigo não ignora acesso por equipe');
$db->rows=array_fill(0,20001,array());
checkCotacoes(dashboardIndicadorExecutar($db,$limited,1,$entry,$error)===false,'Não retornar totais parciais silenciosos');
$db->rows=array();$db->calls=array();$entry['cotacoesDados']='projecao';
$r=dashboardIndicadorExecutar($db,$limited,1,$entry,$error);
checkCotacoes($r!==false && count($db->calls)===5 && $r['painelCotacoes']['historicoInicio']==='2024-04-01','Histórico limitado de pedidos/notas e toneladas de pedidos');
checkCotacoes(strpos($db->calls[1]['sql'],'GROUP BY d.T005_C004_Id')!==false,'Histórico agrega por empresa e dia');
$db->rows=array_fill(0,40001,array());
checkCotacoes(dashboardIndicadorExecutar($db,$limited,1,$entry,$error)===false,'Histórico excedido nunca produz previsão parcial');
$withTeams=$limited;$withTeams['painelCotacoes']['equipes']=$teamConfig['equipes'];
$db->rows=array();$db->sellers=$roster;
foreach(array('periodo'=>8,'projecao'=>5) as $mode=>$count) {
    $entry['cotacoesDados']=$mode;$db->calls=array();$error=null;
    $r=dashboardIndicadorExecutar($db,$withTeams,1,$entry,$error);
    checkCotacoes($r!==false,$error);
    checkCotacoes($r['painelCotacoes']['equipes']===$fixed,'Período e histórico devolvem os mesmos quatro grupos');
    checkCotacoes(count($db->calls)===$count,'Agrupamento não acrescenta consultas');
    checkCotacoes($r['painelCotacoes']['escopo']==='vendedor','Grupo comercial não amplia acesso individual');
    foreach(array_slice($db->calls,1) as $call) checkCotacoes(in_array(7,$call['values'],true),'Equipe configurada mantém restrição em todas as fontes');
}
echo "OK: configuração, quatro equipes, escopos, empresas autorizadas, Supervisor 1, datas, histórico e limites.\n";
