<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoPedidoNF($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\">{$array['todosCampos']['T005_Id']}</a>";
}