<?php
namespace hardness;

// Somente conexões simuladas e configurações fictícias; nunca acessa MySQL.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/connection.php';
require_once dirname(__DIR__) . '/session.php';
if (!defined('MYSQLI_OPT_CONNECT_TIMEOUT')) define('MYSQLI_OPT_CONNECT_TIMEOUT', 0);

function conferirConexao($condicao, $mensagem)
{
    if (!$condicao) throw new \RuntimeException($mensagem);
}

function reiniciarConexoes($falhas = array(), $inicializacoes = array(), $opcoes = array())
{
    $GLOBALS['conexoesTeste'] = array();
    $GLOBALS['tentativasTeste'] = array();
    $GLOBALS['falhasTeste'] = $falhas;
    $GLOBALS['inicializacoesTeste'] = $inicializacoes;
    $GLOBALS['opcoesTeste'] = $opcoes;
}

function mysqli_init()
{
    if ($GLOBALS['inicializacoesTeste'] && !array_shift($GLOBALS['inicializacoesTeste'])) return false;
    $conexao = (object) array('fechada' => false, 'opcoes' => array());
    $GLOBALS['conexoesTeste'][] = $conexao;
    return $conexao;
}

function mysqli_options($conexao, $opcao, $valor)
{
    $conexao->opcoes[$opcao] = $valor;
    return $GLOBALS['opcoesTeste'] ? array_shift($GLOBALS['opcoesTeste']) : true;
}

function mysqli_real_connect($conexao, $host, $usuario, $senha, $base)
{
    conferirConexao($conexao->opcoes[MYSQLI_OPT_CONNECT_TIMEOUT] === 5, 'Timeout deve ser aplicado antes de conectar');
    $conexao->destino = array($host, $usuario, $senha, $base);
    $GLOBALS['tentativasTeste'][] = $host;
    if (!isset($GLOBALS['falhasTeste'][$host])) return true;
    if ($GLOBALS['falhasTeste'][$host] === 'exception') throw new \RuntimeException('Falha simulada');
    return false;
}

function mysqli_close($conexao)
{
    $conexao->fechada = true;
}

$principal = array('dbHost' => 'principal.ficticio', 'dbUser' => 'usuario_ficticio', 'dbPass' => 'senha_ficticia', 'dbDatabase' => 'cliente_ficticio');
$espelho = array('dbHost' => 'espelho.ficticio', 'dbUser' => 'leitura_ficticia', 'dbPass' => 'outra_senha_ficticia', 'dbDatabase' => 'cliente_ficticio');
$original = $principal;
$originalEspelho = $espelho;
$contexto = array('usuario' => 7, 'empresa' => 1, 'perfil' => 2);
$autorizacao = dashboardPersonalizadoCriarAutorizacao($contexto, $principal);

reiniciarConexoes();
$conexao = dashboardPersonalizadoConectarLeitura($principal, $espelho);
conferirConexao($conexao->destino === array_values($espelho), 'Usar integralmente as credenciais do espelho');
conferirConexao($GLOBALS['tentativasTeste'] === array('espelho.ficticio'), 'Espelho disponível dispensa conexão ao principal');
conferirConexao($principal === $original && $espelho === $originalEspelho, 'Preservar ambas as configurações');
conferirConexao(dashboardPersonalizadoValidarAutorizacao($autorizacao, $principal)['usuario'] === 7, 'Sessão assinada pelo principal continua válida');

foreach (array(null, array(), false, 'invalido', array_merge($espelho, array('dbHost' => '')), array_merge($espelho, array('dbDatabase' => 'outro_cliente')), array_merge($espelho, array('dbUser' => array('invalido')))) as $invalido) {
    reiniciarConexoes();
    $conexao = dashboardPersonalizadoConectarLeitura($principal, $invalido);
    conferirConexao($conexao->destino === array_values($principal), 'Ausência/configuração inválida deve usar o principal');
    conferirConexao($GLOBALS['tentativasTeste'] === array('principal.ficticio'), 'Não tentar espelho inválido ou de outro cliente');
}
foreach (array_keys($espelho) as $campo) {
    $incompleto = $espelho;
    unset($incompleto[$campo]);
    reiniciarConexoes();
    dashboardPersonalizadoConectarLeitura($principal, $incompleto);
    conferirConexao($GLOBALS['tentativasTeste'] === array('principal.ficticio'), 'Não herdar credencial ausente do principal');
}

reiniciarConexoes();
dashboardPersonalizadoConectarLeitura($principal, array_merge($espelho, array('dbPass' => '')));
conferirConexao($GLOBALS['tentativasTeste'] === array('espelho.ficticio'), 'Senha explicitamente vazia é válida para mysqli');

reiniciarConexoes();
$conexao = dashboardPersonalizadoConectarLeitura($principal, $espelho, false);
conferirConexao($conexao->destino === array_values($principal) && $GLOBALS['tentativasTeste'] === array('principal.ficticio'), 'Validações do ERP podem exigir o principal mesmo com espelho');

foreach (array('false', 'exception') as $falha) {
    reiniciarConexoes(array('espelho.ficticio' => $falha));
    ob_start();
    $conexao = dashboardPersonalizadoConectarLeitura($principal, $espelho);
    $saida = ob_get_clean();
    conferirConexao($saida === '', 'Não expor falhas de conexão na resposta');
    conferirConexao($conexao->destino === array_values($principal), 'Falha no espelho deve tentar o principal');
    conferirConexao($GLOBALS['tentativasTeste'] === array('espelho.ficticio', 'principal.ficticio'), 'Tentar cada destino uma vez, na ordem correta');
    conferirConexao($GLOBALS['conexoesTeste'][0]->fechada && !$conexao->fechada, 'Fechar a tentativa malsucedida e manter a conexão ativa');
}

reiniciarConexoes(array('espelho.ficticio' => 'exception', 'principal.ficticio' => 'false'));
conferirConexao(dashboardPersonalizadoConectarLeitura($principal, $espelho) === false, 'Falha nos dois destinos deve permitir a resposta de indisponibilidade');
conferirConexao($GLOBALS['conexoesTeste'][0]->fechada && $GLOBALS['conexoesTeste'][1]->fechada, 'Liberar ambas as tentativas malsucedidas');

reiniciarConexoes(array(), array(false, true));
conferirConexao(dashboardPersonalizadoConectarLeitura($principal, $espelho)->destino === array_values($principal), 'Falha na inicialização do espelho não impede fallback');
reiniciarConexoes(array(), array(), array(false, true));
conferirConexao(dashboardPersonalizadoConectarLeitura($principal, $espelho)->destino === array_values($principal), 'Falha ao aplicar timeout não impede fallback');
conferirConexao($GLOBALS['conexoesTeste'][0]->fechada, 'Fechar conexão que não aceitou timeout');

echo "OK: espelho, principal, isolamento da base, configurações, sessão, timeout, falhas e liberação das conexões.\n";
