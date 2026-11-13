<?php
namespace hardness;
// Fixtures e mysqli simulado. Não abre conexão nem consulta base de cliente.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/access.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class SqlTesteStatement {
    public function bind_param($tipos, &...$valores) { return true; }
}
function mysqli_prepare($conexao, $sql) { $GLOBALS['sqlTesteChamadas']++; return new SqlTesteStatement(); }
function mysqli_stmt_execute($stmt) { return empty($GLOBALS['sqlTesteFalha']); }
function mysqli_stmt_errno($stmt) { return empty($GLOBALS['sqlTesteFalha']) ? 0 : 1054; }
function mysqli_stmt_error($stmt) { return "Unknown column 'campo_inexistente' in 'field list'"; }
function mysqli_stmt_get_result($stmt) { return array(array('valor' => 123)); }
function mysqli_fetch_all($resultado, $modo) { return $resultado; }
function mysqli_stmt_close($stmt) {}
function sqlConferir($ok, $mensagem) { if (!$ok) throw new \RuntimeException($mensagem); }

// Exercita o endpoint real com sessão assinada e personalização fictícia.
if (isset($argv[1])) {
    $dir = sys_get_temp_dir() . '/dashboard-sql-endpoint-' . bin2hex(random_bytes(8));
    mkdir($dir . '/personalizacaoConf', 0700, true);
    register_shutdown_function(function () use ($dir) { foreach (glob($dir . '/personalizacaoConf/*') as $file) unlink($file); rmdir($dir . '/personalizacaoConf'); rmdir($dir); });
    $g = array('pathDados' => $dir);
    $confUsuario = array('dbHost' => 'ficticio', 'dbUser' => 'ficticio', 'dbPass' => 'ficticio', 'dbDatabase' => 'ficticio');
    $root = strpos($argv[1], 'root') !== false;
    $auth = array('usuario' => $root ? 63 : 7, 'empresa' => 1, 'perfil' => 2, 'root' => false);
    $_COOKIE[dashboardPersonalizadoNomeCookieSessao()] = dashboardPersonalizadoCriarAutorizacao($auth, $confUsuario);
    file_put_contents(dashboardPersonalizadoArquivo(1), json_encode(array('habilitado' => true, 'indicadores' => array(array('id' => 'fixture_sql', 'titulo' => 'Teste SQL', 'sqlBase' => 'SELECT T007_Id AS id FROM T007 WHERE T007_C004_Id = :empresa')))));
    dashboardPersonalizadoSalvarAcessos(1, array('perfisGestores' => array(2), 'perfis' => array(), 'usuarios' => array()));
    $_GET = array('action' => strpos($argv[1], 'config') !== false ? 'configuration' : 'indicator', 'id' => 'inexistente', 'root' => '1', 'analiseSql' => '1');
    $source = file_get_contents(dirname(__DIR__) . '/data.php');
    $source = str_replace("require_once __DIR__ . '/../bibliotecas/confUsuario.php';", '', $source);
    $source = str_replace('__DIR__', var_export(dirname(__DIR__), true), $source);
    eval('?>' . $source);
    exit;
}

$sql = 'SELECT T007_Id AS id FROM T007 WHERE T007_C004_Id = :empresa';
$root = array('usuario' => 63, 'empresa' => 1, 'perfil' => 2, 'root' => false);
$normal = array('usuario' => 7, 'empresa' => 1, 'perfil' => 2, 'root' => false, 'diretor' => true);
$GLOBALS['sqlTesteChamadas'] = 0;
$entradaCarga = array('id' => 'teste', 'f_dataInicio' => '2026-10-01');
sqlConferir(dashboardCargaGeralAssinaturaEntrada($entradaCarga + array('diagnosticoCarga' => 'abcdef0123456789')) === dashboardCargaGeralAssinaturaEntrada($entradaCarga), 'ID de diagnóstico não altera assinatura de carga/exportação');
foreach (array($normal, array_merge($normal, array('diretor' => false))) as $auth) {
    dashboardSqlIniciar($auth, 'indicator', array('root' => 1, 'analiseSql' => 1));
    $linhas = dashboardIndicadorConsultarDireto(null, $sql, array('empresa' => 1), $erro);
    sqlConferir($linhas === array(array('valor' => 123)) && empty($GLOBALS['dashboardSql']), 'Gestor/diretor não pode coletar diagnóstico, mesmo forjando GET');
    sqlConferir(!isset(dashboardSqlResposta(200, array())['analiseSql']), 'Resposta sem SQL para não ROOT');
}
$antes = $GLOBALS['sqlTesteChamadas'];
dashboardSqlIniciar($root, 'indicator', array('id' => 'teste', 'tab' => 'notas', 'diagnosticoCarga' => 'abcdef0123456789'));
dashboardSqlContexto(array('titulo' => 'Notas', 'sqlBase' => $sql, 'password' => 'ocultar'), array('f_dataInicio' => '2026-10-01', 'cargaToken' => 'ocultar'));
$rows = dashboardIndicadorConsultarDireto(null, $sql, array('empresa' => 1, 'password' => 'ocultar'), $erro);
$response = dashboardSqlResposta(200, array('linhas' => $rows));
$r = $response['analiseSql'];
sqlConferir($GLOBALS['sqlTesteChamadas'] === $antes + 1, 'Diagnóstico não pode acrescentar queries');
sqlConferir($r['quantidadeConsultas'] === 1 && $r['consultas'][0]['linhasRetornadas'] === 1, 'SQL e linhas reais');
sqlConferir($r['consultas'][0]['parametros'] === array('empresa' => 1), 'Só parâmetros utilizados no SQL');
sqlConferir($r['consultas'][0]['sql'] === $sql && $r['consultas'][0]['origem'], 'SQL e origem preservados');
sqlConferir(strpos(json_encode($response), 'ocultar') === false && $r['continuacaoProgressiva'], 'Omitir segredos e token');
sqlConferir($r['tempoServidorMs'] >= $r['tempoSqlMs'] && $r['tempoForaSqlMs'] >= 0, 'Tempos coerentes');
sqlConferir($r['aberturaId'] === 'abcdef0123456789' && $r['versao'] === 2 && isset($r['phpVersao']), 'Correlação e versão do ambiente');
sqlConferir(isset($r['consultas'][0]['etapasMs']['executarBanco'], $r['consultas'][0]['etapasMs']['materializarLinhas']), 'Tempos separados de execução e leitura');

dashboardSqlIniciar($root, 'indicator', array());
$GLOBALS['sqlTesteFalha'] = true;
$result = dashboardIndicadorConsultarDireto(null, $sql, array('empresa' => 1), $erro);
$failed = dashboardSqlResposta(422, array('error' => $erro))['analiseSql'];
sqlConferir($result === false && $failed['ultimaConsultaComErro']['mysql']['codigo'] === 1054, 'Capturar código do banco');
sqlConferir(strpos($failed['ultimaConsultaComErro']['mysql']['mensagem'], 'campo_inexistente') !== false, 'Capturar erro original do banco');
sqlConferir($failed['ultimaConsultaComErro']['faseDaFalha'] === 'executarBanco', 'Fase exata da falha SQL');
$GLOBALS['sqlTesteFalha'] = false;

dashboardSqlIniciar($root, 'indicator', array());
for ($i = 0; $i < 45; $i++) dashboardSqlRegistrar($sql, array('empresa' => 1), microtime(true), array(), '', array());
dashboardSqlRegistrar($sql, array('empresa' => 1), microtime(true), false, 'Falha após limite', array());
dashboardSqlRegistrar($sql . ' AND 1=1', array('empresa' => 1), microtime(true) - 1.5, array(), '', array());
$limited = dashboardSqlResposta(422, array('error' => 'Falha após limite'))['analiseSql'];
sqlConferir(count($limited['consultas']) === 40 && $limited['quantidadeConsultas'] === 47 && $limited['consultasOmitidas'] === 7, 'Histórico limitado preserva contagem total');
sqlConferir($limited['ultimaConsultaComErro']['erro'] === 'Falha após limite', 'Erro final preservado após limite');
sqlConferir($limited['consultaMaisLenta']['ordem'] === 47 && $limited['consultaMaisLenta']['tempoMs'] >= 1500, 'SQL mais lento preservado mesmo depois do limite');
dashboardSqlIniciar($root, 'indicator', array());
dashboardIndicadorConsultarDireto(null, $sql, array(), $erro);
sqlConferir(strpos(dashboardSqlResposta(422, array('error' => $erro))['analiseSql']['consultas'][0]['erro'], 'Parâmetro') !== false, 'Erro antes de executar o SQL também é capturado');
dashboardSqlIniciar($root, 'indicator', array('diagnosticoCarga' => 'invalido'));
$handler = set_error_handler(function () {}); restore_error_handler();
sqlConferir($handler(E_USER_WARNING, 'Aviso parcial de cálculo', dirname(__DIR__) . '/custom-indicator-engine.php', 1), 'Aviso PHP preservado sem invalidar JSON');
dashboardSqlExcecao(new \RuntimeException('Falha de cálculo fictícia'));
$warning = dashboardSqlResposta(200, array('desempenhoComercial' => array('resumo' => array('metaAviso' => 'Meta duplicada')), 'listagem' => array(), 'card' => array('quantidade' => 0)))['analiseSql'];
sqlConferir($warning['aberturaId'] === '' && count($warning['avisos']) === 2, 'Avisos PHP e componente mesmo com HTTP 200');
sqlConferir($warning['volumesRetornados']['cardQuantidade'] === 0 && $warning['erroPhp']['tipo'] === 'RuntimeException', 'Quantidades e origem da exceção sem copiar dados');

foreach (array('--config-root', '--config-user', '--error-root', '--error-user') as $mode) {
    $result = json_decode(shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg($mode)), true);
    sqlConferir(is_array($result), 'Endpoint deve retornar JSON: ' . $mode);
    $isRoot = strpos($mode, 'root') !== false;
    if (strpos($mode, 'config') !== false) {
        sqlConferir($result['podeAnalisarSql'] === $isRoot && $result['podeGerenciarAcessos'], 'Somente ROOT, mesmo com gestão de acessos');
        sqlConferir(!isset($result['analiseSql']), 'Configuração normal não transporta trace');
    } else sqlConferir(isset($result['analiseSql']) === $isRoot, 'Erro de endpoint preserva isolamento ROOT');
}
echo "OK: ROOT no servidor, gestores/diretores bloqueados, GET forjado ignorado, zero queries extras, erro MySQL, parâmetros, limites e endpoint com sessão assinada.\n";
