<?php
namespace hardness;
// Pedido, D049_Id
function verPedidosDoProduto($array){
    global $g;
    $numPedidos = gCorrigeNumero($array['todosCampos']['Pedido']);
    return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [900,500]); return false;\">{$numPedidos}</a>";
}