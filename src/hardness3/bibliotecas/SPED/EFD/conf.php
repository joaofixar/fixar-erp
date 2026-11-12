<?php
namespace hardness;

// confs
session_start();
require_once("../../../bibliotecas/confUsuario.php");
//$confUsuario['dbDatabase'] = 'e009';
$g['pathRaiz'] = $confUsuario['pathRaiz'];
chdir($g['pathRaiz']);
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";

// Sessão
require_once('bibliotecas/sessao.php');
// Banco de Dados
require_once('bibliotecas/conexaoBanco.php');
// Bibliotecas de Interface com o Usuário (UI)
require_once('sistema/interfaceUsuario.php');
// Geração automática de SQLs (grid, forms, etc)
require_once('sistema/automacaoBanco.php');
// Automatização de Formulários
require_once('sistema/automacaoFormulario.php');
// Configurações da Entidade
require_once('sistema/confSistema.php');
// Restrições
require_once('bibliotecas/restricoes.php');
// Funções Gerais
require_once('bibliotecas/funcoesGerais.php');
$g['conf'] = new confSistema();
// Permissões
$g['c029Ids'] = array();
$tmp = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S';");
if ($tmp) {
	while ($row = mysql_fetch_row($tmp)) {
		$g['c029Ids'][$row[0]] = true;
	}
}
$g['empresaAtual'] = 1;

// mysql
/*$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
 $Error=mysqli_connect_error();
 if (!empty($Error)) {
 echo $Error;
 die();
 }
 $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
 mysql_set_charset('utf8');
 mysqli_query('SET NAMES utf8;');
 $g['conexaoBanco']=$con;*/

class formWrap {
	private $array;

	function __construct($array) {
		$this->array = $array;
	}

	public function campoValorEnviado($id) {
		return isset($this->array[$id]) ? $this->array[$id] : '';
	}
}

