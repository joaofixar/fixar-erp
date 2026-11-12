<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoOpcoesConferencia3()
*/

function campoOpcoesConferencia3 ($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="nova_janela('/est/est002/report/pedidoAlmoxarifado/?ajax=true&T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print"  title="Ver Pedido"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
