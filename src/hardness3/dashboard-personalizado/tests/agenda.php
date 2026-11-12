<?php
// PHP CLI; fixtures fictícias, sem credenciais, sessão ou conexão com MySQL.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
require_once dirname(__DIR__) . '/agenda-action.php';

function conferir($condicao, $mensagem) { if (!$condicao) throw new \RuntimeException($mensagem); }
function dashboardPersonalizadoSqlTexto($sql) { return is_array($sql) ? implode("\n", $sql) : $sql; }
function mb_substr($str, $inicio, $tamanho, $encoding) { return substr($str, $inicio, $tamanho); }
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class AgendaStatement
{
    public $sql, $valores, $conexao;
    public function bind_param($tipos, &...$valores) { $this->valores = $valores; return true; }
}
function mysqli_prepare($conexao, $sql) { $stmt = new AgendaStatement(); $stmt->conexao = $conexao; $stmt->sql = $sql; return $stmt; }
function mysqli_stmt_execute($stmt) {
    conferir(strpos($stmt->sql, 'SELECT ') === 0, 'A integração deve executar somente SELECT');
    $stmt->conexao->queries[] = array('sql' => $stmt->sql, 'valores' => $stmt->valores);
    return true;
}
function mysqli_stmt_get_result($stmt) { return array_shift($stmt->conexao->respostas); }
function mysqli_fetch_all($linhas, $modo) { return $linhas; }
function mysqli_stmt_close($stmt) {}
function conexao($respostas) { return (object) array('respostas' => $respostas, 'queries' => array()); }

$auth = array('usuario' => 7, 'empresa' => 1);
$sessao = array('Usuario_Atual' => 7, 'Empresa_Atual' => 1);
$indicador = array(
    'filtros' => array(array('id' => 'abrangenciaEmpresa', 'tipo' => 'selecao', 'padrao' => 'atual', 'opcoes' => array(array('valor' => 'atual'), array('valor' => 'todas')))),
    'listagem' => array('detalhamento' => array('habilitado' => true, 'modal' => true, 'campoGrupo' => 'descricao', 'agenda' => array('habilitarInclusao' => true),
        'sqlBase' => "SELECT T002_Id AS tituloId, T002_D024_Id AS clienteId, T002_C004_Id AS empresaTituloId, T002_Nome_Cliente AS descricao FROM T002 WHERE ((:abrangenciaEmpresa = 'atual' AND T002_C004_Id = :empresa) OR (:abrangenciaEmpresa = 'todas' AND T002_C004_Id IN (1, 2, 4, 5)))")),
);
$entrada = array('agendaTituloId' => '123', 'detalheGrupo' => "Cliente d'Exemplo", 'f_abrangenciaEmpresa' => 'todas');
$titulo = array('tituloId' => 123, 'clienteId' => 45, 'empresaTituloId' => 5, 'agendaId' => 90);
foreach (array(array(), array('Usuario_Atual' => 8, 'Empresa_Atual' => 1), array('Usuario_Atual' => 7, 'Empresa_Atual' => 5)) as $invalida) {
    $db = conexao(array());
    conferir(dashboardAgendaContexto($db, $indicador, $auth, $invalida, $entrada, $erro) === false && !$db->queries, 'Sessão ausente ou divergente deve bloquear antes da consulta');
}
$desabilitado = $indicador;
unset($desabilitado['listagem']['detalhamento']['agenda']);
$db = conexao(array());
conferir(dashboardAgendaContexto($db, $desabilitado, $auth, $sessao, $entrada, $erro) === false && !$db->queries, 'Não habilitar por padrão');
foreach (array('0', '-1', '123 OR 1=1', array(123), '9999999999999') as $id) {
    $db = conexao(array());
    conferir(dashboardAgendaContexto($db, $indicador, $auth, $sessao, array_merge($entrada, array('agendaTituloId' => $id)), $erro) === false && !$db->queries, 'Rejeitar ID inválido');
}
$db = conexao(array());
conferir(dashboardAgendaContexto($db, $indicador, $auth, $sessao, array_merge($entrada, array('f_abrangenciaEmpresa' => 'fraude')), $erro) === false && !$db->queries, 'Validar opções do filtro');
$db = conexao(array(array()));
conferir(dashboardAgendaContexto($db, $indicador, $auth, $sessao, $entrada, $erro) === false && count($db->queries) === 1, 'Título fora dos filtros/grupo não autoriza o cadastro');
$db = conexao(array(array($titulo), array()));
conferir(dashboardAgendaContexto($db, $indicador, $auth, $sessao, $entrada, $erro) === false && count($db->queries) === 2, 'Acesso somente ao dashboard não libera cadastro');
$db = conexao(array(array($titulo), array($titulo)));
$forjada = array_merge($entrada, array('clienteId' => '999', 'empresaTituloId' => '999', 'usuario' => '63', 'empresa' => '999', 'rootSessao' => '1', 'f_empresa' => 999));
$resultado = dashboardAgendaContexto($db, $indicador, $auth, $sessao, $forjada, $erro);
conferir($erro === '' && $resultado['tituloId'] === 123 && $resultado['clienteId'] === 45 && $resultado['empresaTituloId'] === 5 && $resultado['agendaId'] === 90, 'Usar o vínculo real do título');
conferir($resultado['usuario'] === 7 && $resultado['empresaSessao'] === 1, 'Usar autorização da sessão');
parse_str(parse_url($resultado['url'], PHP_URL_QUERY), $url);
conferir($url === array('oneWindow' => 'true', 'D024_Id' => '45', 'T002_Id' => '123', 'tab' => 'geral'), 'Rota nativa sem troca de usuário/empresa ou IDs fornecidos pelo navegador');
conferir($db->queries[0]['valores'] === array('todas', 1, 'todas', 123, "Cliente d'Exemplo"), 'Filtro e cliente parametrizados');
conferir($db->queries[1]['valores'] === array(7, 123, 45, 5, 0, 7, 5, 7, 1), 'Permissões atuais das duas empresas, sem root forjado');
if (in_array('--fixture', $argv, true)) echo json_encode($db->queries, JSON_UNESCAPED_UNICODE);
else echo "OK: sessão, habilitação, parâmetros, filtros, acesso ao ERP e vínculos da agenda.\n";
