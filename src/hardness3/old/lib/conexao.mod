<?php

    $Banco_Dados='e000';
    $Banco_Dados=strtolower($Banco_Dados);
	$host='localhost';
    $user='root';
    $pass='';

    $con=@mysql_connect($host,$user,$pass);

    $Error=mysql_error();
    if (!empty($Error)) {
       include "erro_conexao.php";
    }

    $dbs=mysql_select_db("$Banco_Dados",$con) or die (mysql_error());
    mysql_set_charset('utf8');

?>