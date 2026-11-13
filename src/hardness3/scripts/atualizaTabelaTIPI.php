<?php
// http://www.receita.fazenda.gov.br/aliquotas/downloadarqtipi.htm
date_default_timezone_set("America/Sao_Paulo");
ini_set('default_charset', 'UTF-8');
require_once("../bibliotecas/confUsuario.php");
require_once("../bibliotecas/conexaoBanco.php");
mysql_set_charset('latin1');

/*$pagina = file_get_contents('http://www.receita.fazenda.gov.br/aliquotas/downloadarqtipi.htm');
preg_match_all('/(\/publico\/tipi\/TIPI.*\.doc)/Uis', $pagina, $arquivos);
foreach ($arquivos[1] as $link) {
	$nome = explode('/', $link);
	$nome = $nome[count($nome)-1];
	echo "wget \"http://www.receita.fazenda.gov.br{$link}\"\n";
}
die();*/

$files = scandir('TabelaTIPI/');
foreach ($files as $file) {
	if (!preg_match('/\.htm$/is', $file)) { continue; }
	$pagina = file_get_contents('TabelaTIPI/' . $file);
	preg_match_all('/<tr.*>(.*)<\/tr>/Uis', $pagina, $tipis);
	foreach ($tipis[1] as $tipi) {
		preg_match_all('/<td.*>(.*)<\/td>/Uis', $tipi, $tds);
 		$a = preg_replace('/ +/is', ' ', str_replace("\r\n", ' ', str_replace('&nbsp;', '', trim(strip_tags($tds[1][0])))));
 		$b = preg_replace('/ +/is', ' ', str_replace("\r\n", ' ', str_replace('&nbsp;', '', trim(strip_tags($tds[1][1])))));
 		$c = preg_replace('/ +/is', ' ', str_replace("\r\n", ' ', str_replace('&nbsp;', '', trim(strip_tags($tds[1][2])))));
 		if (preg_match('/^[0-9]{2}/is', $a)) {
 			echo "$a - $b - $c\n";
 			$a = mysql_real_escape_string($a);
 			$b = mysql_real_escape_string($b);
 			$c = mysql_real_escape_string($c);
 			$sql = "INSERT INTO T153 (T153_NCM, T153_Descricao, T153_Aliquota) VALUES ('{$a}', '{$b}', '{$c}');";
			$result = mysql_query($sql);
			if (mysql_error()) {
				die("Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
			}
		}
	}
}
?>