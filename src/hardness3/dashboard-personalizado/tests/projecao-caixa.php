<?php
// Execute via PHP CLI. Todos os registros são fictícios; não há conexão com banco.
namespace hardness;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/custom-indicator-engine.php';

function conferir($esperado, $obtido, $mensagem)
{
    if ($esperado !== $obtido) {
        throw new \RuntimeException($mensagem . ': esperado ' . var_export($esperado, true) . ', obtido ' . var_export($obtido, true));
    }
}

$casos = array(
    array('2026-10-12', 0, '2026-10-13'),
    array('2026-10-10', 0, '2026-10-13'),
    array('2026-10-11', 0, '2026-10-13'),
    array('2026-10-13', 0, '2026-10-13'),
    array('2026-10-12', 2, '2026-10-15'),
    array('2026-10-10', 2, '2026-10-15'),
    array('2026-10-11', 2, '2026-10-15'),
    array('2026-10-13', 2, '2026-10-15'),
    array('2026-10-17', 0, '2026-10-19'),
    array('2026-10-17', 2, '2026-10-21'),
    array('2026-10-18', 2, '2026-10-21'),
    array('2026-10-14', 1, '2026-10-15'),
    array('2026-10-09', 1, '2026-10-13'),
    array('2026-04-03', 0, '2026-04-06'),
    array('2026-04-02', 2, '2026-04-07'),
    array('2026-11-20', 0, '2026-11-23'),
    array('2026-12-25', 0, '2026-12-28'),
    array('2026-12-31', 2, '2027-01-05'),
    array('2026-02-16', 0, '2026-02-16'), // Ponto facultativo exige cadastro.
    array('2026-06-04', 0, '2026-06-04'),
);
foreach ($casos as $caso) {
    conferir($caso[2], dashboardIndicadorProximoDiaUtil($caso[0], array(), $caso[1], true), $caso[0] . ' + ' . $caso[1] . ' dias úteis');
}
conferir('2026-10-16', dashboardIndicadorProximoDiaUtil('2026-10-10', array('2026-10-13' => true), 2, true), 'Feriado cadastrado junto ao nacional');
conferir('2026-10-12', dashboardIndicadorProximoDiaUtil('2026-10-12', array(), 0), 'Compatibilidade sem opção nacional');
conferir('2026-10-13', dashboardIndicadorProximoDiaUtil('2026-10-12', array('2026-10-12' => true), 0), 'Feriado cadastrado no modo anterior');
foreach (array('', '0000-00-00', '2026-02-30', 'inválida') as $invalida) {
    conferir('', dashboardIndicadorProximoDiaUtil($invalida, array(), 0, true), 'Data inválida');
}
$feriados2026 = array_keys(dashboardIndicadorFeriadosNacionais(2026));
sort($feriados2026);
conferir(array('2026-01-01', '2026-04-03', '2026-04-21', '2026-05-01', '2026-09-07', '2026-10-12', '2026-11-02', '2026-11-15', '2026-11-20', '2026-12-25'), $feriados2026, 'Calendário nacional 2026');
conferir(false, isset(dashboardIndicadorFeriadosNacionais(2023)['2023-11-20']), '20/11 ainda não nacional em 2023');
conferir(true, isset(dashboardIndicadorFeriadosNacionais(2024)['2024-11-20']), '20/11 nacional em 2024');
foreach (array(2024 => '2024-03-29', 2025 => '2025-04-18', 2027 => '2027-03-26') as $ano => $paixao) {
    conferir(true, isset(dashboardIndicadorFeriadosNacionais($ano)[$paixao]), 'Feriado móvel em ' . $ano);
}

// Substitui apenas a camada mysqli para exercitar o executor real com fixtures.
// Não carrega sessão, credenciais nem o conector de diagnóstico.
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
function dashboardPersonalizadoSqlTexto($sql)
{
    return is_array($sql) ? implode("\n", $sql) : $sql;
}
class ProjecaoStatement
{
    public $sql;
    public $conexao;
    public function bind_param($tipos, &...$valores) { return true; }
}
function mysqli_prepare($conexao, $sql)
{
    $stmt = new ProjecaoStatement();
    $stmt->sql = $sql;
    $stmt->conexao = $conexao;
    return $stmt;
}
function mysqli_stmt_execute($stmt) { return true; }
function mysqli_stmt_close($stmt) {}
function mysqli_fetch_all($linhas, $modo) { return $linhas; }
function mysqli_stmt_get_result($stmt)
{
    if (strpos($stmt->sql, 'SELECT COUNT(*) AS quantidade') === 0) return array(array('quantidade' => 0, 'valor' => 0));
    if (strpos($stmt->sql, 'SELECT DISTINCT D207_Data') === 0) return $stmt->conexao->feriados;
    foreach (array('pagar', 'receber') as $serie) {
        if (strpos($stmt->sql, "SELECT '" . $serie . "' AS serie") === 0) {
            return array_values(array_filter($stmt->conexao->linhas, function ($linha) use ($serie) { return $linha['serie'] === $serie; }));
        }
    }
    throw new \RuntimeException('Consulta inesperada na fixture: ' . $stmt->sql);
}
function titulo($documento, $serie, $vencimento, $data, $dias, $valor)
{
    return array('documento' => $documento, 'serie' => $serie, 'dataVencimento' => $vencimento, 'data' => $data, 'diasUteisAdicionais' => $dias, 'valor' => $valor);
}
$conexao = (object) array('feriados' => array(), 'linhas' => array(
    titulo('pagar-feriado', 'pagar', '2026-10-12', '2026-10-12', 0, 100.11),
    titulo('pagar-terca', 'pagar', '2026-10-13', '2026-10-13', 0, 200.22),
    titulo('pagar-sabado', 'pagar', '2026-10-10', '2026-10-10', 0, 30.33),
    titulo('receber-feriado', 'receber', '2026-10-12', '2026-10-12', 0, 40.44),
    titulo('receber-terca', 'receber', '2026-10-13', '2026-10-13', 0, 50.55),
    titulo('boleto-feriado', 'receber', '2026-10-12', '2026-10-12', 2, 60.66),
    titulo('boleto-domingo', 'receber', '2026-10-11', '2026-10-11', 2, 70.77),
    titulo('boleto-creditado', 'receber', '2026-10-12', '2026-10-14', 1, 80.88),
    titulo('boleto-sabado-comum', 'receber', '2026-10-17', '2026-10-17', 2, 90.99),
    titulo('boleto-fim-periodo', 'receber', '2026-10-31', '2026-10-31', 2, 10.01),
));
$grafico = array('tipo' => 'barras_comparativas', 'considerarFeriados' => true, 'considerarFeriadosNacionais' => true, 'detalhamento' => array('habilitado' => true), 'sqlSeries' => array());
foreach (array('pagar' => 'T015', 'receber' => 'T002') as $serie => $tabela) {
    $grafico['sqlSeries'][] = array('chave' => $serie, 'sqlBase' => "SELECT '" . $serie . "' AS serie, " . $tabela . "_Valor_Saldo AS valor FROM " . $tabela . " WHERE " . $tabela . "_C004_Id = :empresa");
}
$indicador = array('id' => 'teste', 'titulo' => 'Teste', 'sqlBase' => 'SELECT T002_Valor_Saldo AS valor FROM T002 WHERE T002_C004_Id = :empresa', 'grafico' => $grafico);
function executarFixture($conexao, $indicador, $entrada = array())
{
    $erro = '';
    $resultado = dashboardIndicadorExecutar($conexao, $indicador, 1, $entrada, $erro);
    conferir('', $erro, 'Execução da projeção');
    if ($resultado === false) throw new \RuntimeException('Projeção não retornou resultado');
    return $resultado;
}
$resultado = executarFixture($conexao, $indicador);
$esperado = array(
    '2026-10-13/pagar' => 330.66,
    '2026-10-13/receber' => 90.99,
    '2026-10-15/receber' => 212.31,
    '2026-10-21/receber' => 90.99,
    '2026-11-05/receber' => 10.01,
);
$obtido = array();
foreach ($resultado['grafico'] as $barra) {
    $obtido[$barra['periodo'] . '/' . $barra['serie']] = round($barra['valor'], 2);
    $detalhe = executarFixture($conexao, $indicador, array('detalheGraficoSerie' => $barra['serie'], 'detalheGraficoData' => $barra['periodo']));
    conferir(round($barra['valor'], 2), round(array_sum(array_column($detalhe['graficoDetalhes'], 'valor')), 2), 'Soma do detalhamento igual à barra');
    foreach ($detalhe['graficoDetalhes'] as $titulo) {
        conferir($barra['periodo'], $titulo['dataProjetada'], 'Data projetada do detalhe');
    }
}
conferir($esperado, $obtido, 'Distribuição por data e série');
conferir(round(array_sum(array_column($conexao->linhas, 'valor')), 2), round(array_sum(array_column($resultado['grafico'], 'valor')), 2), 'Conservação dos valores');
$detalhe = executarFixture($conexao, $indicador, array('detalheGraficoSerie' => 'pagar', 'detalheGraficoData' => '2026-10-13'));
conferir(3, count($detalhe['graficoDetalhes']), 'Títulos somados na terça');
conferir('2026-10-12', $detalhe['graficoDetalhes'][0]['dataVencimento'], 'Vencimento original preservado');
$detalhe = executarFixture($conexao, $indicador, array('detalheGraficoSerie' => 'receber', 'detalheGraficoData' => '2026-10-12'));
conferir(array(), $detalhe['graficoDetalhes'], 'Feriado não mantém títulos');
$limitado = $indicador;
$limitado['grafico']['limitarAoPeriodoFiltrado'] = true;
$limitado['filtros'] = array(
    array('id' => 'dataInicio', 'tipo' => 'data', 'padrao' => '2026-10-01', 'obrigatorio' => true),
    array('id' => 'dataFim', 'tipo' => 'data', 'padrao' => '2026-10-31', 'obrigatorio' => true),
);
$outubro = executarFixture($conexao, $limitado);
conferir(array('dataInicio' => '2026-10-01', 'dataFim' => '2026-10-31'), $outubro['indicador']['grafico']['periodoFiltrado'], 'Período aplicado disponível para rótulos e cliques');
conferir(false, in_array('2026-11-05', array_column($outubro['grafico'], 'periodo'), true), 'Compensação de novembro fora do gráfico de outubro');
conferir(724.95, round(array_sum(array_column($outubro['grafico'], 'valor')), 2), 'Totais não incluem valores fora do período');
$foraPeriodo = executarFixture($conexao, $limitado, array('detalheGraficoSerie' => 'receber', 'detalheGraficoData' => '2026-11-05'));
conferir(array(), $foraPeriodo['graficoDetalhes'], 'Detalhamento respeita limite do gráfico');
foreach ($outubro['grafico'] as $barra) {
    $detalhe = executarFixture($conexao, $limitado, array('detalheGraficoSerie' => $barra['serie'], 'detalheGraficoData' => $barra['periodo']));
    conferir(round($barra['valor'], 2), round(array_sum(array_column($detalhe['graficoDetalhes'], 'valor')), 2), 'Barra limitada fecha com detalhamento');
}
$curto = executarFixture($conexao, $limitado, array('f_dataInicio' => '2026-10-13', 'f_dataFim' => '2026-10-15'));
conferir(array('2026-10-13', '2026-10-13', '2026-10-15'), array_column($curto['grafico'], 'periodo'), 'Limites inicial e final inclusivos');
$ampliado = executarFixture($conexao, $limitado, array('f_dataFim' => '2026-11-05'));
conferir($resultado['grafico'], $ampliado['grafico'], 'Ampliar intervalo recupera títulos projetados no mês seguinte');
$erroPeriodo = '';
conferir(false, dashboardIndicadorExecutar($conexao, $limitado, 1, array('f_dataInicio' => '2026-11-01', 'f_dataFim' => '2026-10-31'), $erroPeriodo), 'Recusa período invertido');
conferir(true, $erroPeriodo !== '', 'Erro de período informado');
$semDatas = $limitado;
unset($semDatas['filtros']);
conferir(false, dashboardIndicadorExecutar($conexao, $semDatas, 1, array(), $erroPeriodo), 'Opção de limite exige as datas');
$semOpcao = $indicador;
unset($semOpcao['grafico']['considerarFeriadosNacionais']);
$anterior = executarFixture($conexao, $semOpcao);
conferir(true, in_array('2026-10-12', array_column($anterior['grafico'], 'periodo'), true), 'Opção não afeta outros gráficos');
$semFeriados = $indicador;
$semFeriados['grafico']['considerarFeriados'] = false;
$desabilitado = executarFixture($conexao, $semFeriados);
conferir(true, in_array('2026-10-12', array_column($desabilitado['grafico'], 'periodo'), true), 'Desabilitar feriados respeita configuração existente');
$conexao->feriados = array(array('data' => '2026-10-13'));
$comLocal = executarFixture($conexao, $indicador);
conferir(false, in_array('2026-10-13', array_column($comLocal['grafico'], 'periodo'), true), 'Feriados cadastrados continuam respeitados');

echo "OK: datas, feriados nacionais/cadastrados, compensação, virada de ano, conservação de valores e integração entre barras e detalhamento.\n";
