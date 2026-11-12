<?php
namespace hardness;

function excluirTransacaoExtrato($array){
	global $g;
	if(!empty($array['todosCampos']['T216_T019_Id'])){
		return "";
	} else {
		$icone = ($array['campoAtual'] == 'N') ? "ui-icon-circle-close" : "ui-icon-arrowreturnthick-1-w";
		return <<<EOT
			<span class="ui-icon {$icone}" title="Excluir Transação" onclick="excluirTransacaoExtrato('{$array['todosCampos']['T216_Id']}', '{$array['campoAtual']}', '{$g['divId']}'); return false;"></span>
EOT;
	}
}
