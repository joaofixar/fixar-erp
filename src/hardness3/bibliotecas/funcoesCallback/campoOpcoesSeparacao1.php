<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoOpcoesSeparacao1()
*/
function campoOpcoesSeparacao1 ($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	if ($g['C031']['conferenciaVolumes'] === 'S') {
		$imagem = <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Conferência de Volumes', '/est/est002/content/est002VolumesConferencia/','&acaoId={$array['todosCampos']['T005_Id']}&T005_Id={$array['todosCampos']['T005_Id']}', [950,550]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-circle-arrow-e" title="Conferência de Volumes"></span>
			   </div>
			</span>
EOT;
	} else {
		if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
			$imagem = <<<EOT
				<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Confirmar Separação do Pedido', '/est/est002/content/est002SeparacaoPorLocacao/','&acaoId={$array['todosCampos']['T005_Id']}&T005_Id={$array['todosCampos']['T005_Id']}', [950,550]);">
					<div class="icone ui-state-default ui-corner-all">
							<span class="ui-icon ui-icon-circle-arrow-e" title="Separando o Pedido"></span>
					</div>
				</span>
EOT;
		}
	}
	return $imagem;
}
