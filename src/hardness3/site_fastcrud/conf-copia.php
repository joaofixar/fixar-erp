<?php
die("Não está mais em uso.");

//$confUsuarioERP = array(
//	'dbDatabase' => 'global',
//	//'dbHost' => '52.22.24.146',
//	'dbHost' => 'fabio',
//	'dbUser' => 'dev',
//	'dbPass' => 'dev',
//);
//$conERP=@mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
//$Error=mysql_error();
//if (!empty($Error)) { echo $Error; die(); }
//mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
//mysql_set_charset('utf8');
//mysql_query('SET NAMES utf8;');
//
//$confUsuarioNOTA = array(
//	'dbDatabase' => 'e001',
//	'dbHost' => 'fabio',
//	'dbUser' => 'dev',
//	'dbPass' => 'dev',
//);
//$conNOTA=mysql_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
//$Error=mysql_error();
//if (!empty($Error)) { echo $Error; die(); }
//mysql_select_db($confUsuarioNOTA['dbDatabase'], $conNOTA) or die (mysql_error());
//mysql_set_charset('utf8', $conNOTA);
//mysql_query('SET NAMES utf8;', $conNOTA);


$confUsuarioERP = array(
    'dbDatabase' => 'global',
    'dbHost' => 'host',
    'dbUser' => 'user',
    'dbPass' => 'pass',
);
$conERP = @mysqli_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error = mysqli_error($conERP);
if (!empty($Error)) {
    echo $Error;
    die();
}
mysqli_select_db($conERP, $confUsuarioERP['dbDatabase']) or die (mysql_error($conERP));
mysqli_set_charset($conERP, 'utf8');
mysqli_query($conERP, 'SET NAMES utf8;');

$confUsuarioNOTA = array(
    'dbDatabase' => 'e001',
    'dbHost' => 'host',
    'dbUser' => 'user',
    'dbPass' => 'pass',
);
$conNOTA = mysqli_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error = mysqli_error($conNOTA);
if (!empty($Error)) {
    echo $Error;
    die();
}
mysqli_select_db($conNOTA, $confUsuarioNOTA['dbDatabase']) or die (mysql_error($conNOTA));
mysqli_set_charset($conNOTA, 'utf8');
mysqli_query($conNOTA, 'SET NAMES utf8;');
mysqli_query($conNOTA, "USE e001");
