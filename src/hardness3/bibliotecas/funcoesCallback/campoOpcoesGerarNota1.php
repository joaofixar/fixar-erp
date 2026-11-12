<?php
namespace hardness;
function campoOpcoesGerarNota1($array){
	global $g;
	$imagem = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturamento', '/ven/ven012/content/ven012ContentFaturamento/','&T005_Id={$array['todosCampos']['T005_Id']}&tabela=T005&acaoId={$array['todosCampos']['T005_Id']}', [900,550]);">
			<div class="icone ui-state-default ui-corner-all">
				<span class="ui-icon ui-icon-circle-arrow-e" title="Faturamento"></span>
			</div>
		</span>
EOT;
	return $imagem;
}