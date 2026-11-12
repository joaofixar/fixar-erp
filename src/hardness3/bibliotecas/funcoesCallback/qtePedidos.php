<?php
namespace hardness;

/**
* qtePedidos
*
* @var $array
* @var $opcoes = array(
* 	abrirLocacoes - abre com foco na segunda aba (Locações)
*	valorMenor - pode ser número ou um campo (se for menor que este valor, ficará em vermelho)
* )
*	
*/
function qtePedidos($array, $opcoes = array('abrirLocacao' => false, 'valorMenor' => 0)){
    global $g;
    $numPedidos = gCorrigeNumero($array['todosCampos']['Pedido']);
    $opcoes['valorMenor'] = is_numeric($opcoes['valorMenor']) ? $opcoes['valorMenor'] : $array['todosCampos'][$opcoes['valorMenor']];
    $numPedidos = ($array['todosCampos']['Pedido'] < $opcoes['valorMenor']) ? '<span style="color: red;">' . $numPedidos . '</span>' : $numPedidos;
    $extra = $opcoes['abrirLocacao'] ? '&abrirLocacao=true' : '';
    return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '{$extra}&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [1000,600]); return false;\">{$numPedidos}</a>";
}
