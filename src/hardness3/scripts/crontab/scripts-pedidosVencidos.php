<?php

namespace hardness;
/**
* Atualização campo T003_Flag_Status_Orcamento
* 
* Este script atualiza o status de todos os pedidos para vencidos(26) - pedidos aberto por mais dias que 'diasVencPedido'
*/
$g['r_'] = '';
$g['pathDados'] = $home . "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['usuarioAtual'] = 0;

require_once($home . 'bibliotecas/classes/VEN002.php');
$VEN002 = new VEN002();

$resposta = $VEN002->VEN002ProcessarVencidos();
