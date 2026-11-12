<?php
$confUsuarioERP = array(
        'dbDatabase' => 'global',
        'dbHost' => '127.0.0.1',
        'dbUser' => 'root',
        'dbPass' => 'hardness',
);
$conERP=@mysqli_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysqli_error($conERP);
if (!empty($Error)) { echo $Error; die(); }
mysqli_select_db($conERP, $confUsuarioERP['dbDatabase']) or die (mysqli_error($conERP));
mysqli_set_charset($conERP, 'utf8');
mysqli_query($conERP, 'SET NAMES utf8;');

$confUsuarioNOTA = array(
        'dbDatabase' => 'e001',
        'dbHost' => '127.0.0.1',
        'dbUser' => 'root',
        'dbPass' => 'hardness',
);
$conNOTA=mysqli_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error=mysqli_error($conNOTA);
if (!empty($Error)) { echo $Error; die(); }
mysqli_select_db($conNOTA, $confUsuarioNOTA['dbDatabase']) or die (mysqli_error($conNOTA));
mysqli_set_charset($conNOTA, 'utf8');
mysqli_query($conNOTA, 'SET NAMES utf8;');
mysqli_query($conNOTA, "USE e001");

