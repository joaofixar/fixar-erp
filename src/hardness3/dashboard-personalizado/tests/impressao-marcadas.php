<?php
// Local fixtures and simulated mysqli only. No client database or credentials.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
require_once dirname(__DIR__) . '/../hardness/fpdf/fpdf.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
$export = file_get_contents(dirname(__DIR__) . '/export.php');
$export = substr($export, strpos($export, 'function dashboardExportarErro'));
$export = substr($export, 0, strpos($export, '$autorizacao = dashboardPersonalizadoLerAutorizacao'));
eval('namespace hardness; ' . $export);

class ImpressaoStatement {
    public $sql, $values = array();
    function __construct($sql) { $this->sql = $sql; }
    function bind_param($types, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($connection, $sql) { return new ImpressaoStatement($sql); }
function mysqli_stmt_execute($stmt) { $GLOBALS['printQueries'][] = $stmt; return empty($GLOBALS['printFail']); }
function mysqli_stmt_errno($stmt) { return 0; }
function mysqli_stmt_error($stmt) { return 'Falha simulada'; }
function mysqli_stmt_close($stmt) {}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_get_result($stmt) {
    return strpos($stmt->sql, 'AS dashboard_impressao_selecao') !== false ? $GLOBALS['printNames'] : $GLOBALS['printRows'];
}
function checkPrint($ok, $message) { if (!$ok) throw new \RuntimeException($message); }

$columns = array(
    array('campo'=>'documento','titulo'=>'NF','largura'=>15),
    array('campo'=>'valorNf','titulo'=>'Valor NF','formato'=>'moeda','largura'=>23),
    array('campo'=>'valorParcela','titulo'=>'Valor parcela','formato'=>'moeda','largura'=>23),
    array('campo'=>'percentualComissao','titulo'=>'% Comissão','formato'=>'numero','largura'=>18),
    array('campo'=>'valorComissao','titulo'=>'Valor comissão','formato'=>'moeda','largura'=>25),
    array('campo'=>'clienteCodigo','titulo'=>'Cód. cliente','largura'=>17),
    array('campo'=>'cliente','titulo'=>'Cliente','largura'=>69)
);
$indicator = array('id'=>'comissao','titulo'=>'Comissão','filtros'=>array(
    array('id'=>'dataInicio','tipo'=>'data','titulo'=>'Início','obrigatorio'=>true),
    array('id'=>'dataFim','tipo'=>'data','titulo'=>'Fim','obrigatorio'=>true)),
    'sqlBase'=>"SELECT D021_Id AS id, D021_Nome_Empresa AS descricao, 'E' AS tipoRepresentante FROM D021 INNER JOIN C004 ON C004_Id = :empresa",
    'listagem'=>array('colunas'=>array(array('campo'=>'id'),array('campo'=>'descricao')), 'impressaoMarcadas'=>array(
        'habilitada'=>true,'campoChave'=>'id','campoNome'=>'descricao','campoTipo'=>'tipoRepresentante',
        'titulo'=>'Relatório de Comissão de Representante','assinatura'=>'ASSINATURA DO REPRESENTANTE',
        'sqlBase'=>"SELECT T007_D021_Id AS id, T007_Numero_Nota_Fiscal AS documento, T007_Valor_Total AS valorNf, T002_Valor_Duplicata AS valorParcela, D021_Percentual_Comissao AS percentualComissao, T002_Valor_Recebido * D021_Percentual_Comissao / 100 AS valorComissao, D024_Id AS clienteCodigo, D024_Nome_Empresa AS cliente FROM T007 INNER JOIN T002 ON T002_T007_Id = T007_Id INNER JOIN D021 ON D021_Id = T007_D021_Id LEFT JOIN D024 ON D024_Id = T007_D024_Id WHERE T007_C004_Id = :empresa",
        'colunas'=>$columns,'totais'=>array('valorNf','valorParcela','valorComissao'),
        'rotulosTipo'=>array('E'=>'Eletricista','A'=>'Arquiteto'))));
$input = array('selecionados'=>'101,154','f_dataInicio'=>'2026-10-01','f_dataFim'=>'2026-10-31','empresa'=>'999');
$GLOBALS['printNames'] = array(array('id'=>101,'nome'=>'Representante A','tipo'=>'E'),array('id'=>154,'nome'=>'Representante B','tipo'=>'A'));
$GLOBALS['printRows'] = array();
foreach (array(1291.71,13.00,227.43,430.02) as $n=>$value) $GLOBALS['printRows'][] = array('id'=>101,'documento'=>118166+$n,
    'valorNf'=>$value,'valorParcela'=>$value,'percentualComissao'=>5,'valorComissao'=>$value*.05,'clienteCodigo'=>10+$n,'cliente'=>'Cliente fictício '.$n);
$GLOBALS['printRows'][] = array('id'=>154,'documento'=>200000,'valorNf'=>80,'valorParcela'=>80,'percentualComissao'=>3,'valorComissao'=>2.4,'clienteCodigo'=>20,'cliente'=>'Cliente fictício B');
$GLOBALS['printQueries'] = array();
$result = dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error);
checkPrint($result !== false,$error);
checkPrint(count($GLOBALS['printQueries'])===2 && count($result['registros'])===2,'Seleção inteira usa duas consultas, não uma por representante.');
foreach ($GLOBALS['printQueries'] as $q) {
    checkPrint(in_array(7,$q->values,true) && !in_array('999',$q->values,true),'Empresa vem da autorização, não da entrada.');
    checkPrint(strpos($q->sql,'IN (?, ?)')!==false && in_array('101',$q->values,true),'IDs devem ser vinculados, sem interpolação.');
}
checkPrint(count($result['registros'][0]['linhas'])===4 && count($result['registros'][1]['linhas'])===1,'Parcelas de cada marcado ficam separadas.');
$public = dashboardIndicadorDefinicaoPublica($indicator,array());
checkPrint(!isset($public['listagem']['impressaoMarcadas']['sqlBase']) && !isset($public['listagem']['impressaoMarcadas']['colunas']),'SQL e modelo do PDF ficam no servidor.');

$pdf = dashboardImpressaoMarcadasPdf($result);
checkPrint(strpos($pdf,'%PDF-')===0 && preg_match_all('/\/Type \/Page\b/',$pdf)===2,'Um único PDF deve ter uma página por representante neste recorte.');
if (isset($argv[1])) file_put_contents($argv[1],$pdf);
$many=$result;$many['registros'][0]['linhas']=array_fill(0,70,$result['registros'][0]['linhas'][0]);
checkPrint(preg_match_all('/\/Type \/Page\b/',dashboardImpressaoMarcadasPdf($many))>2,'Muitas parcelas continuam em páginas adicionais sem truncar dados.');

foreach (array('', '0', '101 OR 1=1', '101,154;DROP', array('101'), implode(',',range(1,501))) as $bad) {
    $GLOBALS['printQueries']=array();$in=$input;$in['selecionados']=$bad;
    checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$in,$error)===false && !$GLOBALS['printQueries'],'Seleção inválida não consulta.');
}
foreach (array('2026-02-30','2026-11-01') as $badDate) {
    $GLOBALS['printQueries']=array();$in=$input;$in['f_dataInicio']=$badDate;
    checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$in,$error)===false && !$GLOBALS['printQueries'],'Período inválido não consulta.');
}
$bad=$indicator;$bad['listagem']['impressaoMarcadas']['habilitada']=false;$GLOBALS['printQueries']=array();
checkPrint(dashboardImpressaoMarcadasConsultar(null,$bad,7,$input,$error)===false && !$GLOBALS['printQueries'],'Recurso exige ativação explícita.');
$bad=$indicator;$bad['listagem']['impressaoMarcadas']['sqlBase']='SELECT T007_Id FROM T007';$GLOBALS['printQueries']=array();
checkPrint(dashboardImpressaoMarcadasConsultar(null,$bad,7,$input,$error)===false && !$GLOBALS['printQueries'],'Relatório sem filtro de empresa é recusado.');
$GLOBALS['printNames']=array_slice($GLOBALS['printNames'],0,1);$GLOBALS['printQueries']=array();
checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error)===false && count($GLOBALS['printQueries'])===1,'ID fora da visão autorizada impede a leitura das parcelas.');
$GLOBALS['printNames'][]=array('id'=>154,'nome'=>'Representante B','tipo'=>'A');
$originalRows=$GLOBALS['printRows'];$GLOBALS['printRows'][0]['id']=999;
checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error)===false,'Linha de representante não selecionado é recusada.');
$GLOBALS['printRows']=array_fill(0,10001,$originalRows[0]);
checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error)===false,'Limite excedido falha sem entregar PDF parcial.');
$GLOBALS['printRows']=array();
$empty=dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error);
checkPrint($empty!==false && preg_match_all('/\/Type \/Page\b/',dashboardImpressaoMarcadasPdf($empty))===2,'Sem parcelas ainda imprime os dois selecionados com totais zerados.');
$GLOBALS['printFail']=true;
checkPrint(dashboardImpressaoMarcadasConsultar(null,$indicator,7,$input,$error)===false,'Falha de leitura não gera relatório parcial.');
echo "OK: seleção autorizada, parâmetros, empresa, PDF por representante, continuação, totais, registros vazios e falhas sem resultados parciais.\n";
