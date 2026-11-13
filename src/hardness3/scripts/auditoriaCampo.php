<?php
namespace hardness;

set_time_limit(0);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';

// Verificações
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1]) && (!isset($argv[2]) || empty($argv[2]))) {
	echo "Parametros: {empresa} {campo}\nExemplo: fixar D024_Nome_Empresa\n";
	die();
}
$dominio = $argv[1];
$conf = "/var/www/sites/{$dominio}.hardness.com.br-confUsuario.php";
$local = '/var/www/html/hardness3/';
if ($dominio == 'flycker') {
	$conf = "/home/flycker/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/home/flycker/html/hardness3/';
}
if ($dominio == 'fabio') {
	$conf = "/home/fabio/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/home/fabio/html/hardness3/';
}
if ($dominio == 'fixar') {
	$conf = "/var/www/hardness3/bibliotecas/confUsuario.php";
	$local = '/var/www/hardness3/';
}
if (!file_exists($conf)) {
	echo "Empresa não encontrada!\n";
	die();
}

// Carrega as bibliotecas necessárias...
require_once($conf);
$home = $confUsuario['pathRaiz'];
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'sistema/confSistema.php');
require_once($home . 'bibliotecas/funcoesGerais.php');
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

gExplodeAuditoria($argv[2]);

die("Fim!\n");