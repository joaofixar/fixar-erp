<?php
namespace hardness;


if($row['tipo_produto'] == 'CP'){
    $tipoProduto = "COMPRADO";
}elseif($row['tipo_produto'] == 'PD'){
    $tipoProduto = "PRODUZIDO";
}elseif($row['tipo_produto'] == 'MT'){
    $tipoProduto = "MONTADO";
}elseif($row['tipo_produto'] == 'MD'){
    $tipoProduto = "MODIFICADO";
}elseif($row['tipo_produto'] == 'IN'){
    $tipoProduto = "INDUSTRIALIZAÇÃO";
}

$row['tipo_produto'] = $tipoProduto;



