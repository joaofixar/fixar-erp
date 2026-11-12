<?php

    $Banco_Dados='dev_e009_3';
    $Banco_Dados=strtolower($Banco_Dados);
	$host='debian';
    $user='dev';
    $pass='dev';

    $con=@mysqli_connect($host,$user,$pass,$Banco_Dados);

    $Error=mysqli_connect_error($con);
    if (!empty($Error)) {
       include "erro_conexao.php";
    }

    // $dbs=mysql_select_db("$Banco_Dados",$con) or die (mysql_error());

?>
