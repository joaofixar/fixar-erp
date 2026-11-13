<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
function check($expected, $actual, $message) {
    if ($expected !== $actual) throw new \RuntimeException($message . ': ' . var_export($actual, true));
}
function dashboardPersonalizadoArquivoAcessos($empresa) { return $GLOBALS['walletTestDir'] . '/' . $empresa . '-acessos.json'; }
class WalletStatement {
    public $conexao;
    public $sql;
    public function bind_param($types, &...$values) { $this->conexao->params = $values; return true; }
}
function mysqli_prepare($conexao, $sql) {
    $conexao->sql = $sql; $conexao->queries++;
    if (strpos($sql, 'SELECT portador, boleto,') === 0) $conexao->aggregateQueries++;
    $stmt = new WalletStatement(); $stmt->conexao = $conexao; $stmt->sql = $sql; return $stmt;
}
function mysqli_stmt_execute($stmt) { return true; }
function mysqli_stmt_close($stmt) {}
function mysqli_stmt_get_result($stmt) {
    if (strpos($stmt->sql, 'SELECT C004_Id AS valor') === 0) return array(
        array('valor' => 2, 'titulo' => 'Empresa 2'), array('valor' => 4, 'titulo' => 'Empresa 4'), array('valor' => 5, 'titulo' => 'Empresa 5')
    );
    return $stmt->conexao->linhas;
}
function mysqli_fetch_all($rows, $mode) { return $rows; }
function mysqli_stmt_errno($stmt) { return 0; }
$GLOBALS['walletTestDir'] = sys_get_temp_dir() . '/carteira-bancos-' . bin2hex(random_bytes(8));
mkdir($GLOBALS['walletTestDir'], 0700);
try {
    $indicator = json_decode(file_get_contents(__DIR__ . '/fixtures/controle-carteira-bancos.json'), true);
    $fixture = json_decode(file_get_contents(__DIR__ . '/fixtures/controle-carteira-bancos-planilha.json'), true);
    check(true, dashboardPersonalizadoValidarConfiguracao(array('habilitado' => true, 'indicadores' => array($indicator)), $errors), 'JSON importável');
    $banks = dashboardCarteiraBancosConfig($indicator, $error);
    $connection = (object) array('queries' => 0, 'aggregateQueries' => 0, 'linhas' => $fixture['linhas']);
    $input = array('f_dataInicio' => '2026-11-04', 'f_diasProjecao' => 45);
    $result = dashboardIndicadorExecutar($connection, $indicator, 1, $input, $error);
    check('', $error, 'Execução');
    check(2, $connection->queries, 'Consulta dos nomes das empresas e consulta agregada');
    check(1, $connection->aggregateQueries, 'Uma única consulta de movimentos');
    check(true, strpos($connection->sql, 'GROUP BY portador, boleto, dataCarteira') !== false, 'Bucket futuro sem conflito de alias');
    check(true, strpos($connection->sql, 'LIMIT 20001') !== false, 'Retorno limitado');
    check(true, in_array(1, $connection->params, true), 'Empresa vinculada por parâmetro');
    $data = $result['controleCarteiraBancos'];
    $companyFilter = $result['indicador']['filtros'][0];
    check('abrangenciaEmpresa', $companyFilter['id'], 'Primeiro filtro Empresas');
    check('todas', $companyFilter['valor'], 'Todas as empresas por padrão');
    check(array('atual', '2', '4', '5', 'todas'), array_column($companyFilter['opcoes'], 'valor'), 'Empresas do cadastro');
    check('Todas as empresas', $data['empresasTitulo'], 'Abrangência na tela e no PDF');
    check(true, $result['indicador']['controleCarteiraBancos'], 'Ativação pública');
    check(false, isset($result['indicador']['sqlBase']), 'SQL privado');
    check($fixture['esperado']['totalCentavos'], $data['totalCentavos'], 'Composição completa da base da planilha');
    check('2026-12-18', $data['dataFim'], 'Horizonte inclusivo');
    foreach ($data['bancos'] as $bank) {
        $balance = $bank['carteiraCentavos'] - (int) round($bank['garantia'] * 100);
        $expected = $fixture['esperado']['bancos'][$bank['id']];
        check($expected['saldoInicialCentavos'], $balance, 'Abertura ' . $bank['nome']);
        foreach ($expected['pontos'] as $point) {
            $balance -= isset($bank['vencimentos'][$point['data']]) ? $bank['vencimentos'][$point['data']] : 0;
            check($point['saldoCentavos'], $balance, $bank['nome'] . ' em ' . $point['data']);
        }
    }
    if (in_array('--payload', $argv, true)) { echo json_encode($result, JSON_UNESCAPED_UNICODE); return; }
    if (in_array('--query', $argv, true)) { echo json_encode(array('sql' => $connection->sql, 'params' => $connection->params)); return; }
    foreach (array('atual' => 'Empresa atual', '5' => 'Empresa 5') as $scope => $title) {
        $selected = dashboardIndicadorExecutar($connection, $indicator, 1, $input + array('f_abrangenciaEmpresa' => (string) $scope), $error);
        check($title, $selected['controleCarteiraBancos']['empresasTitulo'], 'Seleção de empresa');
        check(true, in_array((string) $scope, $connection->params, true), 'Abrangência vinculada ao SQL');
    }
    foreach (array('9', 'todas OR 1=1', '') as $invalid) {
        $before = $connection->aggregateQueries;
        check(false, dashboardIndicadorExecutar($connection, $indicator, 1, $input + array('f_abrangenciaEmpresa' => $invalid), $error), 'Empresa fora do grupo');
        check($before, $connection->aggregateQueries, 'Abrangência inválida não consulta movimentos');
    }
    $legacy = $indicator;
    array_shift($legacy['filtros']);
    $legacy['sqlBase'] = 'SELECT T002_Valor_Saldo AS valor FROM T002 WHERE T002_C004_Id = :empresa';
    $before = $connection->queries;
    $legacyResult = dashboardIndicadorExecutar($connection, $legacy, 1, $input, $error);
    check('Empresa atual', $legacyResult['controleCarteiraBancos']['empresasTitulo'], 'Compatibilidade com JSON anterior');
    check($before + 1, $connection->queries, 'JSON sem filtro dinâmico mantém uma consulta');
    $file = dashboardCarteiraBancosArquivo(1, $indicator['id']);
    $original = dashboardCarteiraBancosLer($file, $banks, $error);
    $values = $original['garantias']; $values['itau'] = 3500000.25;
    $saved = dashboardCarteiraBancosSalvar($file, $banks, array('garantias' => $values, 'revisao' => $original['revisao']), 7, $error);
    check(true, $saved !== false, 'Salvar');
    check(3500000.25, dashboardCarteiraBancosLer($file, $banks, $error)['garantias']['itau'], 'Persistência compartilhada');
    check(false, dashboardCarteiraBancosSalvar($file, $banks, array('garantias' => $values, 'revisao' => 'inicial'), 8, $error), 'Conflito entre usuários');
    check(true, strpos($error, 'Outro usuário') !== false, 'Mensagem de conflito');
    check(3000000.0, dashboardCarteiraBancosLer(dashboardCarteiraBancosArquivo(5, $indicator['id']), $banks, $error)['garantias']['itau'], 'Isolamento de empresa');
    check('inicial', dashboardCarteiraBancosLer(dashboardCarteiraBancosArquivo(1, 'outro_indicador'), $banks, $error)['revisao'], 'Isolamento de indicador');
    foreach (array(-1, '100', 0.001, 0.00001, 1000000000000.01, INF, null, array()) as $invalid) {
        $values['itau'] = $invalid;
        check(false, dashboardCarteiraBancosSalvar($file, $banks, array('garantias' => $values, 'revisao' => $saved['revisao']), 7, $error), 'Rejeita garantia inválida');
    }
    $bad = $indicator; $bad['controleCarteiraBancos']['bancos'][1]['portadores'][] = ' boleto itau cc 18626-7 ';
    check(false, dashboardCarteiraBancosConfig($bad, $error), 'Portador duplicado');
    foreach (array(array('f_diasProjecao' => 0), array('f_diasProjecao' => 6), array('f_diasProjecao' => 367), array('f_diasProjecao' => 7.5), array('f_dataInicio' => '2026-02-30')) as $invalid) {
        $before = $connection->queries;
        check(false, dashboardIndicadorExecutar($connection, $indicator, 1, array_merge($input, $invalid), $error), 'Filtro inválido');
        check($before, $connection->queries, 'Valida antes de consultar');
    }
    file_put_contents($file, '{invalid');
    check(false, dashboardCarteiraBancosLer($file, $banks, $error), 'Arquivo corrompido preservado');
    $connection->linhas = array();
    unlink($file);
    check(6, count(dashboardIndicadorExecutar($connection, $indicator, 1, $input, $error)['controleCarteiraBancos']['bancos']), 'Bancos sem carteira continuam editáveis');
    $connection->linhas = array_fill(0, 20001, $fixture['linhas'][0]);
    check(false, dashboardIndicadorExecutar($connection, $indicator, 1, $input, $error), 'Não mostra totais truncados');
    echo "OK: 270 saldos diários iguais à planilha; consulta agregada, filtros, centavos, portadores, persistência, conflito e isolamento de empresa/indicador.\n";
} finally {
    foreach (glob($GLOBALS['walletTestDir'] . '/*') as $file) unlink($file);
    rmdir($GLOBALS['walletTestDir']);
}
