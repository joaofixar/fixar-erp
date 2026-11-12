<?php
namespace hardness;

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
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com nome da empresa a ser executado!\nExemplo: fixar\n";
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
	$conf = "/var/www_novo/hardness3/bibliotecas/confUsuario.php";
	$local = '/var/www_novo/hardness3/';
}
if ($dominio == 'ceigon') {
	$conf = "/var/www/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/var/www/html/hardness3/';
}
if ($dominio == 'guimepa') {
    $conf = "/var/www_novo/hardness3/bibliotecas/confUsuario.php";
    $local = '/var/www_novo/hardness3/';
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
require_once($home . 'sistema/apps.php');
require_once($home . 'sistema/personalizacoes.php');
require_once($home . 'bibliotecas/restricoes.php');
require_once($home . 'sistema/comunicacaoEmail.php');
$g['conf'] = new confSistema();
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
is_dir($g['pathDados']) or mkdir($g['pathDados'], 0777, true);
chdir($g['pathRaiz']);

//echo "Atualizando...\n";
$sqlC007 = mysql_query("SELECT * FROM C007 WHERE IFNULL(C007_Email_SMTP_Usuario,'') != '' AND C007_Ativo = 'S'");
while($rowC007 = mysql_fetch_assoc($sqlC007)){
	//echo $rowC007['C007_Primeiro_Nome']."\n";
	$g['usuarioAtual'] = $rowC007['C007_Id'];
	$g['C007'] = $rowC007;
	$mail = new comunicacaoEmail($g['C007']['C007_Email_POP_Servidor'], $g['C007']['C007_Email_SMTP_Usuario'], $g['C007']['C007_Email_SMTP_Senha'], 'E', false, $g['C007']['C007_Email_POP_Porta']);
	$mail->atualizarEmails();
}
//echo "\n";