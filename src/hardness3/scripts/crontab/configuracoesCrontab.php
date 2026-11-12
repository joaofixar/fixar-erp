<?php
namespace hardness;

/**
* Este arquivo é processado 1 vez por dia
*/
set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
$time_start = microtime(true);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';

// Verificações
if (!isset($argv)) {
	$argv = $_SERVER['argv'];
}
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com nome da empresa a ser executado!\nExemplo: fixar\n";
	die();
}
if(preg_match('#e[0-9]{3}#', $argv[1])){
    $local = '/var/www/html/hardness3/';
    $conf = "/var/www/html/hardness3/bibliotecas/confUsuario.php";
    session_start();
    $_SESSION['bancoDados'] = $argv[1];
    $dominio = $_SESSION['bancoDados'];
} else {
	$dominio = $argv[1];
	$conf = "/var/www/sites/{$dominio}.hardness.com.br-confUsuario.php";
	if ($dominio == 'teste_dev') {
		$conf = "/var/www/html/hardness3/bibliotecas/confUsuario.php";
	}
}
if (!file_exists($conf)) {
	echo "Empresa não encontrada! $conf\n";
	die();
}
// Carrega as bibliotecas necessárias...
require_once($conf);
$home = $confUsuario['pathRaiz'];
$local = $home;
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'sistema/confSistema.php');
require_once($home . 'bibliotecas/funcoesGerais.php');
require_once($home . 'bibliotecas/restricoes.php');
$g['conf'] = new confSistema();
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
chdir($g['pathRaiz']);

// Caminhos
// dados_usuarios/{DB}/
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
$g['pathDados'] = $home . $g['pathDados'];
$g['pathDadosUsuario'] = $home . $g['pathDadosUsuario'];
$g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
$g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}';"));

$sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
while($rowC031 = mysql_fetch_assoc($sqlC031)){
	$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
	$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
}

