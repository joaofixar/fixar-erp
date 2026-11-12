<?php
namespace hardness;

error_reporting(E_ALL & ~E_NOTICE);

// confs
ini_set('session.cookie_lifetime', 31536000);
ini_set('session.gc_maxlifetime', 31536000);
session_cache_expire(31536000);
session_start();
require_once("../../../bibliotecas/confUsuario.php");
//$confUsuario['dbDatabase'] = 'e009';
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
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
//Apps
require_once 'sistema/apps.php';
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

// nivelAlcada
$g['nivelAlcada'] = 1;
switch ($g['C007']['C007_Flag_Vendedor']) {
	case 'S':
		$g['nivelAlcada'] = 2;
		break;
	case 'D':
		$g['nivelAlcada'] = 4;
		break;
}

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

function montaDadosCliente($row){
	if(empty($row['D024_Cnpj'])){
		$documentoCliente = $row['D024_Cpf'];
	}else{
		$documentoCliente = $row['D024_Cnpj'];
	}
	//$row['D024_Nome_Fantasia'] = substr($row['D024_Nome_Fantasia'], 0, 30);
	$row['D020_Nome_Cidade'] = substr($row['D020_Nome_Cidade'], 0, 20);
	$D013_DDD_Telefone_1 = $row['D013_DDD_Telefone_1'];
	$D013_Telefone_1 = $row['D013_Telefone_1'];
	if(!empty($D013_DDD_Telefone_1)){
		$telefoneCliente = "({$D013_DDD_Telefone_1}) {$D013_Telefone_1}";
	}else{
		$telefoneCliente = "{$D013_Telefone_1}";
	}
	if(empty($telefoneCliente)){
		$telefoneCliente = $row['D024_DDD_Telefone_1'] . ' ' . $row['D024_Telefone_1'];
	}
	$row['D024_Cep'] = substr($row['D024_Cep'],0,5).'-'.substr($row['D024_Cep'],5,3);
	$row['D024_Complemento'] = !empty($row['D024_Complemento']) ? " - &nbsp;{$row['D024_Complemento']}" : '';

	// 		<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px;">{$documentoCliente}&nbsp; - &nbsp;{$telefoneCliente}</div>
	//	<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px; white-space: normal;">{$row['D024_Nome_Empresa']}</div>

	return <<<EOT
		<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px; white-space: normal;">{$row['D024_Nome_Fantasia']} - {$row['D024_Id']}</div>
		<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px;">{$row['D024_Endereco']}, {$row['D024_Numero']}&nbsp; {$row['D024_Complemento']}</div>
		<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px;">{$row['D024_Bairro']} - {$row['D024_Cep']}</div>
		<div style="width: 100%; clear: both; text-align: left;margin-bottom: 2px;">{$row['D020_Nome_Cidade']}/{$row['D018_UF']}&nbsp; - &nbsp;{$row['C007_Primeiro_Nome']}</div>
EOT;
}

class formWrap {
	private $array;
	
	function __construct($array) {
		$this->array = $array;
	}
	
	public function campoValorEnviado($id) {
		return isset($this->array[$id]) ? $this->array[$id] : '';
	}
}
