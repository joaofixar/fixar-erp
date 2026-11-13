<?php
namespace hardness;
function campoOpcoesImpressao1($array){
	global $g;
	$novoIdTelaForm = uniqid();
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem = <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Estoquista', '/est/est002/content/est002ImprimirPedido/','&T005_Id={$array['todosCampos']['T005_Id']}', [450,170]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-circle-arrow-e" title="Imprima o pedido"></span>
			   </div>
			</span>
EOT;
	}
	return $imagem;
}
