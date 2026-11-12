<?php
namespace hardness;

// confs
ini_set('session.cookie_lifetime', 31536000);
ini_set('session.gc_maxlifetime', 31536000);
session_cache_expire(31536000);
session_start();
require_once("../../../bibliotecas/confUsuario.php");
//$confUsuario['dbDatabase'] = 'e009';
$g['pathRaiz'] = $confUsuario['pathRaiz'];
chdir($g['pathRaiz']);

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

$sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
while($rowC031 = mysql_fetch_assoc($sqlC031)){
	$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
	$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
}
$g['c020'] = $g['C020'] = @mysql_fetch_assoc(@mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
$g['c004'] = $g['C004'] = @mysql_fetch_assoc(@mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}';"));
$g['c007'] = $g['C007'] = @mysql_fetch_assoc(@mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
$g['c029Ids'] = array();
$tmp = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S';");
if ($tmp) {
	while ($row = mysql_fetch_row($tmp)) {
		$g['c029Ids'][$row[0]] = true;
	}
}
$g['pathWeb'] = '/hardness3/';
// dirs dados_usuarios
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
is_dir($g['pathDados']) or mkdir($g['pathDados'], 0777, true);
$g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
is_dir($g['pathDadosUsuario']) or mkdir($g['pathDadosUsuario'], 0777, true);
$tmp = $g['pathDadosUsuario'] . 'tabletFiltro/';
is_dir($tmp) or mkdir($tmp, 0777, true);

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
