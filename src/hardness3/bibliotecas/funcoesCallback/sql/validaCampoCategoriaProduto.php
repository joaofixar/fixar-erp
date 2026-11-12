<?php
namespace hardness;


if($row['categoria_produto'] == '00'){
    $categoria = "00-MERCADORIA PARA REVENDA";
}elseif($row['categoria_produto'] == '01'){
    $categoria = "01-MATERIA PRIMA";
}elseif($row['categoria_produto'] == '02'){
    $categoria = "02-EMBALAGEM";
}elseif($row['categoria_produto'] == '03'){
    $categoria = "03-PRODUTO EM PROCESSO";
}elseif($row['categoria_produto'] == '04'){
    $categoria = "04-PRODUTO ACABADO";
}elseif($row['categoria_produto'] == '05'){
    $categoria = "05-SUBPRODUTO";
}elseif($row['categoria_produto'] == '06'){
    $categoria = "06-PRODUTO INTERMEDIARIO";
}elseif($row['categoria_produto'] == '07'){
    $categoria = "07-MATERIAL DE USO E CONSUMO";
}elseif($row['categoria_produto'] == '08'){
    $categoria = "08-ATIVO IMOBILIZADO";
}elseif($row['categoria_produto'] == '09'){
    $categoria = "09-SERVICOS";
}elseif($row['categoria_produto'] == '10'){
    $categoria = "10-OUTROS INSUMOS";
}elseif($row['categoria_produto'] == '99'){
    $categoria = "99-OUTRAS";
}

$row['categoria_produto'] = $categoria;


