<?php

$con =@mysqli_connect("192.168.15.199", "hardness", "root","e010");
// $dbs =@mysql_select_db("e010", $con) or die (mysql_error());

$sql = mysqli_query($con,"SELECT T005_Id,T005_Ordem_Compra,T005_Observacao_Nota_Fiscal,T005_Flag_Plataforma_Ecommerce,T005_Canal_Vendas_Ecommerce FROM
  T005
WHERE T005_Flag_Plataforma_Ecommerce=2");

while($T005 = mysqli_fetch_assoc($sql)){

    echo "\n\n\nT005_Observacao_Nota_Fiscal:".$T005["T005_Observacao_Nota_Fiscal"];
    $letras =  soLetras($T005["T005_Observacao_Nota_Fiscal"]);
    echo "\nLetras:".$letras;
}

function soletras($str) {
    return preg_replace("/[^A-Z-a-z]/", " ", $str);
}

?>