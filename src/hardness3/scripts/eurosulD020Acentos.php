<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');

echo "Recebidos...\n";
$sql = "SELECT D020_Id, D020_Nome_Cidade FROM D020";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$sql = "UPDATE D020 SET D020_Nome_Cidade = '{$row['D020_Nome_Cidade']}' WHERE D020_Id = '{$row['D020_Id']}';";
	echo $sql . "\n";
	$row['D020_Nome_Cidade'] = RemoveAcentos($row['D020_Nome_Cidade']);
	$row['D020_Nome_Cidade'] = mysql_real_escape_string($row['D020_Nome_Cidade']);
	$sql = "UPDATE D020 SET D020_Nome_Cidade = '{$row['D020_Nome_Cidade']}' WHERE D020_Id = '{$row['D020_Id']}';";
	echo $sql . "\n\n"; 
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br />SQL: {$sql}"); }
}

function RemoveAcentos($str, $enc = 'UTF-8') {
 
$acentos = array(
        'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
        'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
        'C' => '/&Ccedil;/',
        'c' => '/&ccedil;/',
        'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
        'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
        'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
        'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
        'N' => '/&Ntilde;/',
        'n' => '/&ntilde;/',
        'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
        'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
        'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
        'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
        'Y' => '/&Yacute;/',
        'y' => '/&yacute;|&yuml;/',
        'a.' => '/&ordf;/',
        'o.' => '/&ordm;/'
);

        return preg_replace($acentos, array_keys($acentos), htmlentities($str,ENT_NOQUOTES, $enc));
 }
