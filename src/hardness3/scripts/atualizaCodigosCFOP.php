<?php
// http://www.sefaz.pe.gov.br/sefaz2/flexpub/versao1/filesdirectory/sessions398.htm
date_default_timezone_set("America/Sao_Paulo");
ini_set('default_charset', 'UTF-8');
require_once("../bibliotecas/confUsuario.php");
require_once("../bibliotecas/conexaoBanco.php");

$pagina = file_get_contents('http://www.sefaz.pe.gov.br/sefaz2/flexpub/versao1/filesdirectory/sessions398.htm');
preg_match_all('/<tr style=\'mso-yfti-irow:[0-9]+\'>.*<\/tr>/Uis', $pagina, $cfops);
foreach ($cfops[0] as $cfop) {
	preg_match_all('/<td.*>(.*)<\/td>/Uis', $cfop, $itens);
	$item_cfop = strip_tags($itens[1][0]);
	$item_descricao = strip_tags($itens[1][1]);
	$item_aplicacao = strip_tags($itens[1][2]);
	
	$item_cfop = trim(str_replace("\r\n ", '', $item_cfop));
	$item_descricao = trim(str_replace("\r\n ", '', $item_descricao));
	$item_aplicacao = trim(str_replace("\r\n ", '', $item_aplicacao));	
	
	$item_cfop = mysql_real_escape_string($item_cfop);
	$item_descricao = mysql_real_escape_string($item_descricao);
	$item_aplicacao = mysql_real_escape_string($item_aplicacao);
	
	echo $item_cfop . " ...\n";
	$sql = "INSERT INTO cfop (cfop, descricao, aplicacao) VALUES ('{$item_cfop}', '{$item_descricao}', '{$item_aplicacao}');";
	$result = mysql_query($sql);
	if (mysql_error()) {
		//die("Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
	}
}
?>