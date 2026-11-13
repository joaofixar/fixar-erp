<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/
//Mascara CEP cliente
$tamanhoCEP = strlen($row['cep_cliente']);
$final = substr($row['cep_cliente'],-3);
$inicio = substr($row['cep_cliente'],0,($tamanhoCEP-3));
$cep = $inicio."-".$final;
$row['cep_cliente'] = $cep;
