<?php
namespace hardness;
function campoOpcoesConferencia1($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Faturamento', '/est/est002/content/est002FaturamentoPedido/','&acaoId={$array['todosCampos']['T005_Id']}&T005_Id={$array['todosCampos']['T005_Id']}', [990,605]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Conferência Pedido"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}