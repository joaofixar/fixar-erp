<?php
namespace hardness;

function filaEnvioData($array){
	global $g;
	$T007_Data_Emissao = gCorrigeData($array['todosCampos']['T007_Data_Emissao']);
	$T007_Data_Saida = gCorrigeData($array['todosCampos']['T007_Data_Saida']);
	$T007_Id = $array['todosCampos']['T007_Id'];
	$novoIdTelaForm = uniqid();
	$from='T007';

	$retorno = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_Data_Emissao}
		</span>
		<br />
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_Data_Saida}
		</span>
EOT;
	return $retorno;
}
