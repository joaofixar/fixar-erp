<?php
global $sys;

$confUsuarioERP = array(
    'dbDatabase' => $sys['dbDatabase'],
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
mysqli_select_db($conERP, $confUsuarioERP['dbDatabase']) or die ("Erro conexão");
mysqli_set_charset($conERP, 'utf8');
mysqli_query($conERP, 'SET NAMES utf8;');
